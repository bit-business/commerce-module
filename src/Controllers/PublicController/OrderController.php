<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers\PublicController;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Helpers\Config;
use NadzorServera\Skijasi\Module\Commerce\Events\OrderStateWasChanged;
use NadzorServera\Skijasi\Module\Commerce\Helper\UploadImage;
use NadzorServera\Skijasi\Module\Commerce\Models\Cart;
use NadzorServera\Skijasi\Module\Commerce\Models\Discount;
use NadzorServera\Skijasi\Module\Commerce\Models\Order;
use NadzorServera\Skijasi\Module\Commerce\Models\OrderAddress;
use NadzorServera\Skijasi\Module\Commerce\Models\OrderDetail;
use NadzorServera\Skijasi\Module\Commerce\Models\OrderPayment;
use NadzorServera\Skijasi\Module\Commerce\Models\PaymentOption;
use NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail;
use NadzorServera\Skijasi\Module\Commerce\Models\UserAddress;
use NadzorServera\Skijasi\Traits\FileHandler;


use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    use FileHandler;

    public function browse()
    {
        try {
            if (in_array(env('DB_CONNECTION'), ['pgsql'])) {
                $orders = Order::select(['id', 'status', 'payed', 'expired_at', 'cancel_message']);

                $orders = $orders->where('user_id', auth()->user()->id)
                    ->latest()
                    ->get();

                $orders = $orders->map(function ($order) {
                    if (isset($order->orderDetails)) {
                        $order_details = $order->orderDetails;
                        $order_details = $order_details->map(function ($order_detail) {
                            $product_detail = $order_detail->productDetail;
                            if (isset($product_detail)) {
                                $product_detail->product = $product_detail->product;
                            }
                            $product_detail->review;

                            return $order_detail;
                        });
                        $order->order_details = $order_details;
                    }
                    $order->order_payment = $order->orderPayment;

                    return $order;
                });

                $data['orders'] = $orders->toArray();

                return ApiResponse::success($data);
            } else {
                $orders = Order::select(['id', 'status', 'payed', 'expired_at', 'cancel_message'])
                    ->with(['orderDetails' => function ($query) {
                        return $query
                            ->select(['id', 'order_id', 'product_detail_id', 'price', 'discounted', 'quantity'])
                            ->with(['productDetail' => function ($query) {
                                return $query
                                    ->select(['id', 'product_id', 'name', 'product_image'])
                                    ->with(['product' => function ($query) {
                                        return $query->select(['id', 'name', 'slug']);
                                    }]);
                            }]);
                    }, 'orderDetails.review', 'orderPayment'])
                    ->where('user_id', auth()->user()->id)
                    ->latest()
                    ->get();
            }
            $orders = Order::select(['id', 'status', 'payed', 'expired_at', 'cancel_message'])
                ->with(['orderDetails' => function ($query) {
                    return $query
                        ->select(['id', 'order_id', 'product_detail_id', 'price', 'discounted', 'quantity'])
                        ->with(['productDetail' => function ($query) {
                            return $query
                                ->select(['id', 'product_id', 'name', 'product_image'])
                                ->with(['product' => function ($query) {
                                    return $query->select(['id', 'name', 'slug']);
                                }]);
                        }]);
                }, 'orderDetails.review', 'orderPayment'])
                ->where('user_id', auth()->user()->id)
                ->latest()
                ->get();

            $data['orders'] = $orders->toArray();

            return ApiResponse::success($data);
        } catch (\Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function read(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Order,id',
            ]);

            $order = Order::with(['orderDetails.productDetail.product', 'orderPayment', 'orderAddress'])
                ->where('id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->firstOrFail();

            $payment_option = PaymentOption::where('slug', $order->orderPayment->payment_type_option_id)->first();

            $data['order'] = $order->toArray();
            $data['payment_option'] = $payment_option;

            return ApiResponse::success($data);
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

    


    public function finish(Request $request)
    {
        DB::beginTransaction();
        try {
            Log::info('0.Received checkout request on server:', $request->all());
            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Cart,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.product_name' => 'required|string',
                'payment_type_option_id' => 'string|max:255|exists:NadzorServera\Skijasi\Module\Commerce\Models\PaymentOption,id',
                'message' => 'nullable|string',
            ]);
    
            $itemIds = array_column($request->items, 'id');
            Log::info('1.Item IDs:', $itemIds);
    
            // Find an existing order but ONLY if it is not yet completed
        $existingOrder = Order::where('user_id', auth()->user()->id)
        ->whereHas('orderDetails', function ($query) use ($itemIds) {
            $query->whereIn('product_detail_id', Cart::whereIn('id', $itemIds)->pluck('product_detail_id'));
        })
        // Only consider orders that are in "waiting" status (e.g., not paid or not completed)
        ->whereIn('status', ['waitingBuyerPayment', 'processing'])
        ->first();
    
            $total_discounted = 0;
            $total = 0;
            $status = 'waitingBuyerPayment';
            $shipping_cost = 0;
    
            if (Config::get('commerceUseFixRateShippingCost') == 1) {
                $shipping_cost = Config::get('commerceFixRateShippingCost');
            }
    
            foreach ($request->items as $item) {
                $cart = Cart::find($item['id']);
    
                $product_detail = ProductDetail::with('discount')->findOrFail($cart->product_detail_id);
           
    
                if ($product_detail->product->product_category_id != 30) {
                    if ($item['quantity'] > $product_detail->quantity) {
                        throw new Exception('Nema više raspoloživih');
                    }
                } else {
                    if ($item['quantity'] > ($product_detail->quantity + $item['quantity'])) {
                        throw new Exception('Nema više raspoloživih');
                    }
                }
    
                $discount = null;
                $discounted = 0;
                if ($product_detail->discount_id) {
                    $discount = Discount::findOrFail($product_detail->discount_id);
                    if ($discount->active == 1) {
                        if ($discount->discount_type === 'fixed') {
                            $discounted = $discount->discount_fixed * $item['quantity'];
                        }
    
                        if ($discount->discount_type === 'percent') {
                            $discounted = round($product_detail->price * $discount->discount_percent / 100) * $item['quantity'];
                        }
                    }
                }
    
                $total_discounted += $discounted;
                $total += $product_detail->price * $item['quantity'];
            }
    
            if ($existingOrder) {
                // Update existing order
                Log::info('2.Updating existing order:', ['order_id' => $existingOrder->id]);
                $existingOrder->update([
                    'discounted' => $total_discounted,
                    'total' => $total,
                    'shipping_cost' => $shipping_cost,
                    'payed' => $total - $total_discounted + $shipping_cost,
                    'status' => $status,
                    'message' => $request->message,
                ]);
    
                foreach ($request->items as $item) {
                    $cart = Cart::find($item['id']);
         
                    $product_detail = ProductDetail::findOrFail($cart->product_detail_id);
                    Log::info('3.Found product detail(existingorder):', $product_detail->toArray());
                    $discount = null;
                    $discounted = 0;
                    if (!empty($product_detail->discount_id)) {
                        $discount = Discount::findOrFail($product_detail->discount_id);
                        if ($discount->active === 1 || $discount->active === '1') {
                            if ($discount->discount_type === 'fixed') {
                                $discounted = $discount->discount_fixed;
                            }
                            if ($discount->discount_type === 'percent') {
                                $discounted = round($product_detail->price * $discount->discount_percent / 100);
                            }
                        }
                    }
    
                    $orderDetail = OrderDetail::where('order_id', $existingOrder->id)
                        ->where('product_detail_id', $product_detail->id)
                        ->first();
    
                    if ($orderDetail) {
                        Log::info('4.Updating existing order detail:', ['order_detail_id' => $orderDetail->id]);
                        // Update existing order detail
                        $quantityDifference = $item['quantity'] - $orderDetail->quantity;
                        $orderDetail->update([
                            'discount_id' => $discount ? $discount->id : null,
                            'price' => $product_detail->price,
                            'discounted' => $discounted,
                            'quantity' => $item['quantity'],
                        ]);
    
                        if ($product_detail->product->product_category_id != 30) {
                            $product_detail->quantity -= $quantityDifference;
                            $product_detail->save();
                        }
                    } else {
                        Log::info('5.Creating new order detail for existing order');
                        // Create new order detail
                        OrderDetail::create([
                            'order_id' => $existingOrder->id,
                            'product_detail_id' => $product_detail->id,
                            'discount_id' => $discount ? $discount->id : null,
                            'price' => $product_detail->price,
                            'discounted' => $discounted,
                            'quantity' => $item['quantity'],
                        ]);

                        if ($product_detail->product->product_category_id != 30) {
                            $product_detail->quantity -= $item['quantity'];
                            $product_detail->save();
                        }
                    }
                }

                // ADD THIS BLOCK TO DELETE EXTRA ORDER DETAILS
                 // Replace the existing deletion block with this:
                        $existingOrderDetailIds = OrderDetail::where('order_id', $existingOrder->id)
                        ->pluck('product_detail_id')
                        ->map(function ($id) { return (string) $id; })
                        ->toArray();

                        $itemProductDetailIds = collect($request->items)
                        ->pluck('product_detail_id')
                        ->map(function ($id) { return (string) $id; })
                        ->toArray();

                        $extraOrderDetailIds = array_diff($existingOrderDetailIds, $itemProductDetailIds);
                        Log::info('6.Extra order detail IDs:', ['ids' => $extraOrderDetailIds]);

                        if (!empty($extraOrderDetailIds)) {
                        $deletedCount = OrderDetail::where('order_id', $existingOrder->id)
                            ->whereIn('product_detail_id', $extraOrderDetailIds)
                            ->update(['deleted_at' => now()]);
                        Log::info('7.Soft deleted order details count', [
                            'count' => $deletedCount, 
                            'order_id' => $existingOrder->id,
                            'deleted_product_detail_ids' => $extraOrderDetailIds
                        ]);
                        } else {
                        Log::info('7.No order details to delete', ['order_id' => $existingOrder->id]);
                        }
                                                
                            
                                            

            // Generate the payment slip PDF
            $paymentSlipData = $this->generatePaymentSlipData($existingOrder, $request->items);
            $pdfPath = $this->stvoriuplatnicu2($paymentSlipData, $existingOrder->id);



            event(new OrderStateWasChanged(auth()->user(), $existingOrder, 'waitingBuyerPayment', $pdfPath));

            DB::commit();

            // Make sure this URL is correct and accessible
            $paymentSlipUrl = asset('storage/uplatnice/' . basename($pdfPath));

            return ApiResponse::success([
                'order' => $existingOrder->id,
                'payment_slip_url' => $paymentSlipUrl
            ]);
        }

    
            // Create new order
            $order = Order::create([
                'user_id' => auth()->user()->id,
                'discounted' => $total_discounted,
                'total' => $total,
                'shipping_cost' => $shipping_cost,
                'payed' => $total - $total_discounted + $shipping_cost,
                'status' => $status,
                'expired_at' => Config::get('commerceHasExpiredOrder') == 1
                    ? Carbon::now()->addDays(Config::get('commerceExpiredOrderDay'))
                    : null,
                'message' => $request->message,
            ]);
    
            OrderPayment::create([
                'order_id' => $order->id,
                'payment_type_option_id' => $request->payment_type_option_id,
            ]);
            Log::info('8.Created order payment for new order');

            foreach ($request->items as $item) {
                $cart = Cart::find($item['id']);
                $product_detail = ProductDetail::findOrFail($cart->product_detail_id);
                Log::info('9.Found product detail for new order:', $product_detail->toArray());
                $discount = null;
                $discounted = 0;
                if (!empty($product_detail->discount_id)) {
                    $discount = Discount::findOrFail($product_detail->discount_id);
                    if ($discount->active === 1 || $discount->active === '1') {
                        if ($discount->discount_type === 'fixed') {
                            $discounted = $discount->discount_fixed;
                        }
                        if ($discount->discount_type === 'percent') {
                            $discounted = round($product_detail->price * $discount->discount_percent / 100);
                        }
                    }
                }
    
                $orderDetail = OrderDetail::create([
                    'order_id' => $order->id,
                    'product_detail_id' => $product_detail->id,
                    'discount_id' => $discount ? $discount->id : null,
                    'price' => $product_detail->price,
                    'discounted' => $discounted,
                    'quantity' => $item['quantity'],
                ]);
      
    
                Log::info('10.Created OrderDetail:', $orderDetail->toArray());
    
                if ($product_detail->product->product_category_id != 30) {
                    $product_detail->quantity -= $item['quantity'];
                    $product_detail->save();
                }
            }
            
    
    
            $paymentSlipData = $this->generatePaymentSlipData($order, $request->items);
        $pdfPath = $this->stvoriuplatnicu2($paymentSlipData, $order->id);

        if ($pdfPath === null) {
            Log::error("Failed to generate PDF for new order: " . $order->id);
            throw new \Exception("Failed to generate payment slip PDF");
        }

        event(new OrderStateWasChanged(auth()->user(), $order, 'waitingBuyerPayment', $pdfPath));

        DB::commit();
      
        // Make sure this URL is correct and accessible
        $paymentSlipUrl = asset('storage/uplatnice/' . basename($pdfPath));

        return ApiResponse::success([
            'order' => $order->id,
            'payment_slip_url' => $paymentSlipUrl
        ]);
    } catch (Exception $e) {
        DB::rollback();
        Log::error('12.Error in finish method: ' . $e->getMessage());
        return ApiResponse::failed($e);
    }
}


    public function pay(Request $request)
    {
        try {
            $validationData = $request->validate([
                'order_id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Order,id',
                'source_bank' => 'nullable|string',
                'destination_bank' => 'nullable|string',
                'account_number' => 'nullable|alpha_num',
                'total_transfered' => 'nullable|numeric',
                'proof_of_transaction' => 'nullable|string',
                'item_ids' => 'required|array', // Add this line
                'item_ids.*' => 'exists:NadzorServera\Skijasi\Module\Commerce\Models\Cart,id',
            ]);
    
            DB::beginTransaction();
            $order = Order::where('user_id', auth()->user()->id)
            ->where('id', $request->order_id)
            ->firstOrFail();

            
       

        Cart::whereIn('id', $request->item_ids)
        ->where('user_id', auth()->user()->id)
        ->update(['cekapotvrdu' => 1]);
      
    
            $order_payments = OrderPayment::where('order_id', $order->id)->first();
    
            // Only attempt to upload if proof_of_transaction is provided
            $uploaded_path = null;
            if ($request->has('proof_of_transaction')) {
                $uploaded_path = UploadImage::createImagePotvrda($request->proof_of_transaction, 'uplatnice/potvrde/', $order->id);
                if ($uploaded_path === null) {
                    throw new Exception('Failed to upload proof of transaction');
                }
            }
    
            $order_payments->source_bank = $request->source_bank;
            $order_payments->destination_bank = $request->destination_bank;
            $order_payments->account_number = $request->account_number;
            $order_payments->total_transfered = $request->total_transfered;
            if ($uploaded_path) {
                $order_payments->proof_of_transaction = $uploaded_path;
            }
            $order_payments->save();
    
            $order->status = 'waitingSellerConfirmation';
            $order->expired_at = null;
            $order->save();
    
      
    
            event(new OrderStateWasChanged(auth()->user(), $order, 'waitingSellerConfirmation'));
            DB::commit();
    
            Log::info('Transaction committed');
    
            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Exception occurred', ['exception' => $e]);
            return ApiResponse::failed($e);
        }
    }

    public function update(Request $request)
{
    DB::beginTransaction();
    try {
        $order = Order::findOrFail($request->order_id);
        
        // Update order details
        // ... (code to update order details based on new items)

        // Regenerate payment slip
        $paymentSlipData = $this->generatePaymentSlipData($order);
        $pdfPath = $this->stvoriuplatnicu2($paymentSlipData, $order->id);
        $order->payment_slip_path = $pdfPath;
        $order->save();

        DB::commit();

        return ApiResponse::success([
            'order' => $order->id,
            'payment_slip_url' => asset('storage/' . $pdfPath)
        ]);
    } catch (Exception $e) {
        DB::rollback();
        return ApiResponse::failed($e);
    }
}

private function generatePaymentSlipData($order, $items)
{
    $user = auth()->user();
    $amount = number_format($order->payed, 2, '', '');

    $poziv_na_broj_primatelja = (string)$user->idmember;
    if (empty($poziv_na_broj_primatelja)) {
        $datumrodjenja = new \DateTime($user->datumrodjenja);
        $poziv_na_broj_primatelja = $datumrodjenja->format('dmY');
    }

    $productNames = $this->getProductNames($items);

    return [
        "poziv_na_broj_platitelja" => "",
        "poziv_na_broj_primatelja" => $poziv_na_broj_primatelja,
        "iznos" => $amount,
        "iban_primatelja" => "HR7423600001101359833",
        "iban_platitelja" => "",
        "model_primatelja" => "HR07",
        "model_platitelja" => "",
        "sifra_namjene" => "",
        "datum_izvrsenja" => "",
        "valuta_placanja" => "EUR",
        "ime_i_prezime_platitelja" => $user->name . " " . $user->username,
        "ulica_i_broj_platitelja" => $user->adresa,
        "postanski_i_grad_platitelja" => $user->postanskibroj . " " . $user->grad,
        "naziv_primatelja" => "Hrvatski zbor učitelja i trenera sportova na snijegu(HZUTS)",
        "ulica_i_broj_primatelja" => "Maksimirska 51a",
        "postanski_i_grad_primatelja" => "10 000 Zagreb,Hrvatska",
        "opis_placanja" => $productNames . ", " . $user->name . " " . $user->username
    ];
}

private function getProductNames($items)
{
    $hasSpecialItem = false;
    $otherProductNames = [];
    $specialItems = ['Amblem', 'Izdavanje Iskaznice', 'Potvrda'];

    foreach ($items as $item) {
        $productDetail = ProductDetail::find($item['product_detail_id']);
        $productName = $item['product_name'];

        if (($productDetail && $productDetail->product->product_category_id == 30) || 
            in_array($productName, $specialItems)) {
            $hasSpecialItem = true;
        } else {
            $otherProductNames[] = $productName;
        }
    }

    if ($hasSpecialItem) {
        array_unshift($otherProductNames, 'Članarina');
        return implode(', ', array_unique($otherProductNames));
    } else {
        return implode(', ', $otherProductNames);
    }
}
    
    private function stvoriuplatnicu2($paymentSlipData, $orderId)
    {
        try {
            $transactionUniqueCode = $orderId;
            $paymentSlipPdf = $this->runPythonScript(json_encode($paymentSlipData));
    
            if ($paymentSlipPdf === null || empty($paymentSlipPdf)) {
                Log::error("PDF creation failed or empty for order: " . $orderId);
                return null;
            }
    
               // Generate a random 4-digit number
        $randomNumber = rand(1000, 9999);

            $filename = "pdf-" . $transactionUniqueCode . "-" . $randomNumber . ".pdf";
            $filePath = storage_path('app/public/uplatnice/' . $filename);
    
            $bytesWritten = file_put_contents($filePath, $paymentSlipPdf);
            
            if ($bytesWritten === false) {
                Log::error("Failed to write PDF file for order: " . $orderId);
                return null;
            }
    
            Log::info("PDF created successfully for order: " . $orderId . ", path: " . $filePath);
            return $filePath;
        } catch (\Exception $e) {
            Log::error("Exception in stvoriuplatnicu2: " . $e->getMessage());
            return null;
        }
    }


    //dodano za uplatnice pocetak 
// Controller method to generate and store PDF file


public function stvoriuplatnicu(Request $request)
{
    try {
        $paymentSlipData = $request->input('paymentSlipData');
        $transactionUniqueCode = $request->input('transactionUniqueCode'); // Get transactionUniqueCode from request

        Log::info("Received paymentSlipData: " . $paymentSlipData);

        // Call the Python script and get the generated PDF
        $paymentSlipPdf = $this->runPythonScript($paymentSlipData);

        if ($paymentSlipPdf === null || empty($paymentSlipPdf)) {
            Log::error("PDF creation failed or empty");
            return response("PDF creation failed", 500);
        }

        // Generate a unique filename for the PDF based on transactionUniqueCode
        $filename = "pdf-" . $transactionUniqueCode . ".pdf";
        $filePath = storage_path('app/public/uplatnice/' . $filename); // Adjust the storage path as needed

        // Store the PDF file
        file_put_contents($filePath, $paymentSlipPdf);

        // Return the URL or file path to the stored PDF
        return response()->json([
            'pdfUrl' => asset('storage/uplatnice/' . $filename) // Example: '/storage/pdf-transactionUniqueCode.pdf'
        ]);

    } catch (Exception $e) {
        Log::error("Exception occurred: " . $e->getMessage());
        return response("Internal Server Error", 500);
    }
}
    
    private function runPythonScript($paymentSlipData)
    {
        $command = escapeshellcmd("/usr/bin/python3 " . public_path('uplatnica.py'));
    
        $process = proc_open($command, [
            ["pipe", "r"],
            ["pipe", "w"],
            ["pipe", "w"]
        ], $pipes);
    
        if (is_resource($process)) {
            fwrite($pipes[0], $paymentSlipData);
            fclose($pipes[0]);
    
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
    
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
    
            proc_close($process);
    
            if ($errors) {
                Log::error("Python script error: " . $errors);
            }
    
            return $output;
        }
    
        return null;
    }
    
      //dodano za uplatnice kraj
    
    

      public function getTotalCompletedOrders()
      {
          try {
              $completedOrders = Order::where('status', 'done');
              
              $totalCompletedOrders = $completedOrders->count();
              $totalPayed = $completedOrders->sum('payed');
      
      
              return ApiResponse::success([
                  'total_completed_orders' => $totalCompletedOrders,
                 'total_payed' => round($totalPayed, 2)
              ]);

              // ako ce trebat zaokruzit iznos 'total_payed' => round($totalPayed, 2)
          } catch (Exception $e) {
              return ApiResponse::failed($e);
          }
      }


}
