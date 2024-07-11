<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers;

use Exception;
use Illuminate\Http\Request;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Models\Cart;

class CartController extends Controller
{
    public function browse(Request $request)
    {
        try {
            $request->validate([
                'page' => 'sometimes|required|integer',
                'limit' => 'sometimes|required|integer',
                'relation' => 'nullable',
            ]);
    
            $relations = $request->relation ? explode(',', $request->relation) : [];
            $relations[] = 'productDetail.product'; // Add this line
    
            if ($request->has('page') || $request->has('limit')) {
                $carts = Cart::with($relations)->paginate($request->limit ?? 10);
            } else {
                $carts = Cart::with($relations)->get();
            }
    
            $data['carts'] = $carts->toArray();
            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function read(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Cart,id',
                'relation' => 'nullable',
            ]);

            $cart = Cart::when($request->relation, function ($query) use ($request) {
                return $query->with(explode(',', $request->relation));
            })->where('id', $request->id)->first();
            $data['cart'] = $cart->toArray();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Cart,id',
            ]);

            $cart = Cart::findOrFail($request->id);
            $cart->delete();

            return ApiResponse::success(['message' => 'Zaduzenje/kosarica obrisana uspjesno']);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }


    public function edit(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Cart,id',
                'quantity' => 'required|integer|min:1',
                'product_detail_id' => 'required|exists:skijasi_product_details,id',
                
            ]);
    
            $cart = Cart::findOrFail($request->id);
            $cart->quantity = $request->quantity;
            $cart->product_detail_id = $request->product_detail_id;
            $cart->save();
    
            return ApiResponse::success(['message' => 'Cart updated successfully']);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }


}
