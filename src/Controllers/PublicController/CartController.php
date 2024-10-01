<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers\PublicController;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Models\Cart;
use NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail;
use NadzorServera\Skijasi\Module\Commerce\Models\OrderDetail;

use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function browse(Request $request)
    {
        try {
            $user_id = auth()->id();

            $data['carts'] = Cart::with(['productDetail.product.productCategory', 'productDetail.discount'])->where('user_id', $user_id)->get()->toArray();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }


    public function removeFromCart(Request $request)
{
    DB::beginTransaction();
    try {
        $request->validate([
            'userid' => 'required',
            'tblpaymentsid' => 'required|integer',
        ]);

        $cart = Cart::where('user_id', $request->userid)
                    ->where('tblpaymentsid', $request->tblpaymentsid)
                    ->delete();

        DB::commit();

        return ApiResponse::success(['message' => 'Cart item removed successfully']);
    } catch (Exception $e) {
        DB::rollback();
        return ApiResponse::failed($e);
    }
}

    public function addplacanja(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail,id', 
                'userid' => 'required',
                'quantity' => 'required|min:0|integer',
                'userstatus' => 'string',
                'tblpaymentsid' => 'nullable|integer', 
            ], [
                'id.required' => 'You have to select one of the variants!',
            ]);
    
         
        $product_detail = ProductDetail::where('id', $request->id)
        ->where('name', $request->userstatus)
        ->first();
    
            if (!$product_detail) {
                return ApiResponse::failed('Product detail not found!');
            }
    
            if ($product_detail->quantity <= 0) {
                return ApiResponse::failed(__('skijasi_commerce::validation.stock_not_available'));
            }
    
            $cart = Cart::where('user_id', $request->userid)->where('product_detail_id', $request->id)->first();
    
            if (empty($cart)) {
                $cartData = [
                    'product_detail_id' => $request->id,
                    'user_id' => $request->userid,
                    'quantity' => $request->quantity,
                ];
            
                if ($request->has('tblpaymentsid')) {
                    $cartData['tblpaymentsid'] = $request->tblpaymentsid;
                }
            
                Cart::create($cartData);
            
            } else {
                if ($cart->quantity + $request->quantity > $product_detail->quantity) {
                    return ApiResponse::failed(__('skijasi_commerce::validation.stock_not_available'));
                }
                
                $updateData = [
                    'product_detail_id' => $request->id,
                    'user_id' => $request->userid,
                    'quantity' => $cart->quantity,
                ];
            
                if ($request->has('tblpaymentsid')) {
                    $updateData['tblpaymentsid'] = $request->tblpaymentsid;
                }
            
                Cart::where('user_id', $request->userid)
                    ->where('product_detail_id', $request->id)
                    ->update($updateData);
            }
    
            DB::commit();
    
            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();
    
            return ApiResponse::failed($e);
        }
    }


    


    public function add(Request $request)
    {
    
        DB::beginTransaction();
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail,id',
                'quantity' => 'required|min:1|integer',
            ], [
                'id.required' => 'You have to select one of the variants!',
            ]);
    
            $product_detail = ProductDetail::with('product')->where('id', $request->id)->first();
            if (!$product_detail) {
                return ApiResponse::failed('Product detail not found!');
            }
    
            // Check if there's enough stock
            if ($product_detail->quantity < $request->quantity) {
                Log::info('Stock not available. Product detail quantity: ' . $product_detail->quantity . ', Requested quantity: ' . $request->quantity);
                return ApiResponse::failed(__('skijasi_commerce::validation.stock_not_available'));
            }
    
            $cart = Cart::where('user_id', auth()->id())->where('product_detail_id', $request->id)->first();
    
            if (empty($cart)) {
                Cart::create([
                    'product_detail_id' => $request->id,
                    'user_id' => auth()->id(),
                    'quantity' => $request->quantity,
                ]);
            } else {
                $newQuantity = $cart->quantity;
                if ($product_detail->quantity < $newQuantity) {
                    Log::info('Not enough stock for cart update. Product detail quantity: ' . $product_detail->quantity . ', New cart quantity: ' . $newQuantity);
                    return ApiResponse::failed(__('skijasi_commerce::validation.stock_not_available'));
                }
                $cart->update([
                    'quantity' => $newQuantity,
                ]);
            }
    
            // Reduce the quantity only if the product category is not 30
            if ($product_detail->product->product_category_id == 30) {
                $product_detail->quantity -= $request->quantity;
                $product_detail->save();
            }
    
            DB::commit();
            Log::info('Add function completed successfully');
            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error in add function: ' . $e->getMessage());
            return ApiResponse::failed($e);
        }
    }
    
    public function edit(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Cart',
                'quantity' => 'required|min:0|integer',
            ]);

            $cart = Cart::where('id', $request->id)->where('user_id', auth()->user()->id)->first();

            $product_detail = ProductDetail::where('id', $cart->product_detail_id)->first();

            if ($request->quantity > $product_detail->quantity) {
                return ApiResponse::failed(__('skijasi_commerce::validation.stock_not_available'));
            }

            $cart = Cart::where('id', $request->id)->update([
                'quantity' => $request->quantity,
            ]);

            DB::commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

    public function editCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Cart',
                'product_detail_id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail,id',
                'quantity' => 'required|min:0|integer',
            ]);

            $cart = Cart::where('id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->first();

            $product_detail = ProductDetail::where('id', $cart->product_detail_id)
                ->first();

            if ($request->quantity > $product_detail->quantity) {
                return ApiResponse::failed(__('skijasi_commerce::validation.stock_not_available'));
            }

            $cart = Cart::where('id', $request->id)->update([
                'quantity' => $request->quantity,
                'product_detail_id' => $request->product_detail_id,
            ]);

            DB::commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Cart',
            ]);
    
            $cart = Cart::findOrFail($request->id);
    
            // Delete associated OrderDetails
            OrderDetail::where('product_detail_id', $cart->product_detail_id)->delete();
    
            // Delete the cart item
            $cart->delete();
    
            DB::commit();
    
            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();
    
            return ApiResponse::failed($e);
        }
    }

    public function validate(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required',
            ]);

            $id_list = explode(',', $request->ids);

            foreach ($id_list as $key => $id) {
                $cart = Cart::find($id);
                if (is_null($cart)) {
                    return ApiResponse::success(['cart' => false]);
                }
            }

            return ApiResponse::success(['cart' => true]);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function getTotalCartItems(Request $request)
    {
        try {
            $totalItems = Cart::sum('quantity');
            
            $totalPrice = Cart::join('skijasi_product_details', 'skijasi_carts.product_detail_id', '=', 'skijasi_product_details.id')
                ->selectRaw('SUM(skijasi_carts.quantity * skijasi_product_details.price) as total_price')
                ->value('total_price');
    
            return ApiResponse::success([
                'total_items' => $totalItems,
                'total_price' => round($totalPrice, 2)
            ]);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }
}
