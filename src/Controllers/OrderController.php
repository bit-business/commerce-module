<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Models\User;
use NadzorServera\Skijasi\Models\UserRole;
use NadzorServera\Skijasi\Module\Commerce\Events\OrderStateWasChanged;
use NadzorServera\Skijasi\Module\Commerce\Models\Order;
use NadzorServera\Skijasi\Module\Commerce\Models\OrderDetail;
use NadzorServera\Skijasi\Module\Commerce\Models\OrderPayment;

use NadzorServera\Skijasi\Module\Commerce\Models\Cart;
use NadzorServera\Skijasi\Theme\CommerceTheme\Models\Form;
use NadzorServera\Skijasi\Helpers\GetData;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function browse(Request $request)
    {
        try {
            $request->validate([
                'page' => 'sometimes|required|integer',
                'limit' => 'sometimes|required|integer',
                'relation' => 'nullable',
                'search' => 'nullable|string',
                'order_field' => 'nullable|string',
                'order_direction' => 'nullable|string|in:desc,asc',
                'status' => 'nullable|string',
            ]);

            $userId = auth()->user()->id;
            $userRole = UserRole::where('user_id', $userId)->get();
            foreach ($userRole as $key => $value) {
                $roleId = $value->role_id;
            }
            $search = $request->search;
            $status = $request->status;
          
            if (in_array($roleId, [1, 2, 3, 4, 5, 6, 7, 9, 2439, 4417])) {
                $orders = Order::when($request->relation, function ($query) use ($request) {
                    return $query->with(explode(',', $request->relation));
                })
                    ->when($search, function ($query, $search) {
                        return $query->where('status', 'LIKE', '%'.$search.'%')
                            ->orWhere('id', 'LIKE', '%'.$search.'%')
                            ->orWhereHas('user', function ($q) use ($search) {
                                $q->where('username', 'LIKE', '%'.$search.'%');
                            });
                    })
                    ->when($status && $status !== 'all', function ($query) use ($status) {
                        $statuses = explode(',', $status);
                        return $query->whereIn('status', $statuses);
                    })
                    ->orderBy($request->order_field ?? 'updated_at', $request->order_direction ?? 'desc')
                    ->paginate($request->limit ?? 10);
            } else {
                $orders = Order::when($request->relation, function ($query) use ($request) {
                    return $query->with(explode(',', $request->relation))->where('user_id', auth()->user()->id);
                })
                    ->where(function ($query) use ($search) {
                        $query->where('status', 'like', '%'.$search.'%');
                        $query->orWhere('id', 'like', '%'.$search.'%');
                        $query->orWhereHas('user', function ($q) use ($search) {
                            $q->where('username', 'like', '%'.$search.'%');
                        });
                    })
                    ->when($status && $status !== 'all', function ($query) use ($status) {
                        $statuses = explode(',', $status);
                        return $query->whereIn('status', $statuses);
                    })
                    ->orderBy($request->order_field ?? 'updated_at', $request->order_direction ?? 'desc')
                    ->paginate($request->limit ?? 10);
            }

            $data['orders'] = $orders->toArray();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }


    public function read(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Order,id',
                'relation' => 'nullable',
            ]);
    
            if (in_array(env('DB_CONNECTION'), ['pgsql'])) {
                $order = Order::where('id', $request->id);
                $order_data = $order->first();
                $with = ['user'];
    
                $order_payment = OrderPayment::where('order_id', $order_data->id)->count();
                if ($order_payment > 0) {
                    $with[] = 'orderPayment';
                }
    
                $order_detail = OrderDetail::where('order_id', $order_data->id)->count();
                if ($order_detail > 0) {
                    $with = array_merge($with, [
                        'orderDetails' => function ($query) {
                            $query->withTrashed();
                        },
                        'orderDetails.productDetail.product',
                    ]);
                }
    
                $order = $order->with($with)->first();
            } else {
                $order = Order::with([
                    'user',
                    'orderDetails' => function ($query) {
                        $query->withTrashed();
                    },
                    'orderDetails.productDetail.product',
                    'orderPayment'
                ])
                ->where('id', $request->id)
                ->first();
            }
    
            $data['order'] = $order->toArray();
            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function confirm(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Order,id',
            ]);

            $order = Order::find($request->id);
            if (! is_null($order->expired_at) && now()->greaterThanOrEqualTo(Carbon::create($order->expired_at))) {
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->productDetail->quantity += $orderDetail->quantity;
                    $orderDetail->productDetail->save();

                    $this->updateCekapotvrduForCartItem($orderDetail->product_detail_id, 0);

                }

                $order->status = 'cancel';
                $order->expired_at = null;
                $order->save();

                return ApiResponse::failed('Order is already expired.');
            }

            if ($order->status == 'waitingSellerConfirmation') {
                $order->status = 'process';
                $order->expired_at = null;
                $order->save();

                event(new OrderStateWasChanged(User::where('id', $order->user_id)->first(), $order, 'process'));

                
                return ApiResponse::success();
            }

            return ApiResponse::failed();
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function reject(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Order,id',
                'cancel_message' => 'required|string|max:255',
            ]);

            $order = Order::find($request->id);

            if (in_array($order->status, ['waitingSellerConfirmation', 'waitingBuyerPayment'])) {
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->productDetail->quantity += $orderDetail->quantity;
                    $orderDetail->productDetail->save();

                    $this->updateCekapotvrduForCartItem($orderDetail->product_detail_id, 0);

                }

                $order->status = 'cancel';
                $order->cancel_message = $request->cancel_message;
                $order->expired_at = null;
                $order->save();

                event(new OrderStateWasChanged(User::where('id', $order->user_id)->first(), $order, 'cancel'));

                return ApiResponse::success();
            }

            return ApiResponse::failed('Invalid request');
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    private function updateCekapotvrduForCartItem($productDetailId, $value)
    {
        Cart::where('product_detail_id', $productDetailId)->update(['cekapotvrdu' => $value]);
    }


    public function ship(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Order,id',
                'tracking_number' => 'required|alpha_num',
            ]);

            $order = Order::find($request->id);
            if ($order->status = 'process') {
                $order->status = 'delivering';
                $order->tracking_number = $request->tracking_number;
                $order->save();

                event(new OrderStateWasChanged(User::where('id', $order->user_id)->first(), $order, 'delivering'));

                return ApiResponse::success();
            }

            return ApiResponse::failed();
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function deleteCartItems($orderId)
    {
        $order = Order::findOrFail($orderId);
        $orderDetails = $order->orderDetails;
        
        foreach ($orderDetails as $orderDetail) {
            Cart::where('product_detail_id', $orderDetail->product_detail_id)
                ->where('user_id', $order->user_id)
                ->delete();
        }
    }


public function done(Request $request)
{
    try {
        $request->validate([
            'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Order,id',
        ]);

        $order = Order::find($request->id);
        $order->status = 'done';
        $order->save();



        $user = User::find($order->user_id);

        $this->updateStaroPlacanje($order->id);



        if ($user) {
            $paymentData = GetData::fetchPaymentDataForMember($user->id);
            $statusPlacanja = GetData::calculatePaymentStatus($paymentData);

            DB::table('skijasi_form_entries')
                ->where('userid', $order->user_id)
                ->whereNull('placeno')
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->update([
                    'placeno' => $order->payed . '€, ' . $statusPlacanja,
                    'brojnarudzbe' => $order->id
                ]);

        } 

    

        $this->deleteCartItems($order->id);

        event(new OrderStateWasChanged(User::where('id', $order->user_id)->first(), $order, 'done'));

    




        return ApiResponse::success();

    } catch (Exception $e) {
        return ApiResponse::failed($e);
    }
}



public function getOrdersPerMonth(Request $request)
{
    try {
        $startDate = $request->get('start_date', Carbon::now()->subYear()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $orders = Order::selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $formattedData = $orders->map(function ($item) {
            return [
                'month' => Carbon::parse($item->month)->format('n Y'), // Use 'n' for month number without leading zeros
                'count' => $item->count
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $formattedData
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}





public function updateStaroPlacanje($orderId)
{
    $orderDetails = OrderDetail::where('order_id', $orderId)->get();
    
    foreach ($orderDetails as $orderDetail) {
        $cartItem = Cart::where('product_detail_id', $orderDetail->product_detail_id)
                        ->where('user_id', $orderDetail->order->user_id)
                        ->first();

        if ($cartItem) {
            if (in_array($orderDetail->product_detail_id, [10, 11, 12, 37, 38, 39])) {
                // Handle special cases (Amblem, Potvrda, Izdavanje iskaznice)
                $this->insertSpecialPayment($orderDetail, $cartItem);
            } else {
                // Update existing payment record
                $this->updateExistingPayment($cartItem);
            }
        }
    }
}

private function insertSpecialPayment($orderDetail, $cartItem)
{
    $paymentData = [
        'idmember' => $cartItem->user_id,
        'callnumber' => $cartItem->user_id,
        'price' => $orderDetail->price,
        'paidvalue' => $orderDetail->price,
        'opendate' => now(),
        'paidstatus' => 1,
        'paydate' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ];

    switch ($orderDetail->product_detail_id) {
        case 10:
            case 37:
            $paymentData = array_merge($paymentData, [
                'idpaygroup' => 6,
                'idpaysubgroup' => 80,
                'paymenttitle' => 'Amblem',
            ]);
            break;
        case 11:
            case 38:
            $paymentData = array_merge($paymentData, [
                'idpaygroup' => 6,
                'idpaysubgroup' => 147,
                'paymenttitle' => 'Potvrda',
            ]);
            break;
        case 12:
            case 39:
            $paymentData = array_merge($paymentData, [
                'idpaygroup' => 3,
                'idpaysubgroup' => 142,
                'paymenttitle' => 'Izdavanje iskaznice',
            ]);
            break;
    }

    DB::table('tbl_payments')->insert($paymentData);
}

private function updateExistingPayment($cartItem)
{
    $payment = DB::table('tbl_payments')
        ->where('id', $cartItem->tblpaymentsid)
        ->first();

    if ($payment) {
        DB::table('tbl_payments')
            ->where('id', $cartItem->tblpaymentsid)
            ->update([
                'paidstatus' => 1,
                'paidvalue' => $payment->price,
                'paydate' => now()
            ]);
    }
}

public function fetchNewOrdersCount()
{
    try {
        $count = Order::where('status', 'waitingSellerConfirmation')->count();
        return ApiResponse::success(['count' => $count]);
    } catch (Exception $e) {
        return ApiResponse::failed($e);
    }
}

}
