<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers;

use Exception;
use Illuminate\Http\Request;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Models\Cart;
use NadzorServera\Skijasi\Models\UserRole;

class CartController extends Controller
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
            $roleId = $userRole->pluck('role_id')->first();
            $orderField = $request->order_field ?? 'id';
            $orderDirection = $request->order_direction ?? 'asc';
            $search = $request->search;
    
            $query = Cart::when($request->relation, function ($query) use ($request) {
                return $query->with(explode(',', $request->relation));
            });
    
            if (!in_array($roleId, [1, 4])) {
                $query->where('user_id', auth()->user()->id);
            }
    
            if ($search) {
                $searchTerms = explode(' ', $search);
                $query->where(function ($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->where(function ($query) use ($term) {
                            $query->where('id', 'LIKE', "%{$term}%")
                                ->orWhereHas('user', function ($q) use ($term) {
                                    $q->where('username', 'LIKE', "%{$term}%")
                                      ->orWhere('name', 'LIKE', "%{$term}%");
                                })
                                ->orWhereHas('productDetail.product', function ($q) use ($term) {
                                    $q->where('name', 'LIKE', "%{$term}%");
                                });
                        });
                    }
                });
            }
    
            $carts = $query->orderBy($orderField, $orderDirection)
                           ->paginate($request->limit ?? 10);
    
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
