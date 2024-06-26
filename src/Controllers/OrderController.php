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
            ]);

            $userId = auth()->user()->id;
            $userRole = UserRole::where('user_id', $userId)->get();
            foreach ($userRole as $key => $value) {
                $roleId = $value->role_id;
            }
            $search = $request->search;
            $roleId = null;
            if ($roleId == 1) {
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
                    $with[] = 'orderDetails.productDetail.product';
                }

                $order = $order->with($with)->first();
            } else {
                $order = Order::with('user', 'orderDetails.productDetail.product', 'orderPayment')
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
    $orderDetails = OrderDetail::where('order_id', $orderId)->get();
    
    foreach ($orderDetails as $orderDetail) {
        $cartItems = Cart::where('product_detail_id', $orderDetail->product_detail_id)->get();
        foreach ($cartItems as $cartItem) {
            $cartItem->delete();
        }
    }
}


    public function done(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Order,id',
            ]);

            $order = Order::find($request->id);
          //  if ($order->status = 'delivering') {
                $order->status = 'done';
                $order->save();


                $this->deleteCartItems($order->id);

                event(new OrderStateWasChanged(User::where('id', $order->user_id)->first(), $order, 'done'));

                return ApiResponse::success();
          //  }

          //  return ApiResponse::failed();
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }
}
