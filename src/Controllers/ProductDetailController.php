<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail;

use Illuminate\Support\Str;

class ProductDetailController extends Controller
{
    public function add(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'product_id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Product,id',
                'discount_id' => 'nullable|exists:NadzorServera\Skijasi\Module\Commerce\Models\Discount,id',
                'name' => 'required|string|max:255',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                // 's_k_u' => 'nullable|string|max:255',
                'product_image' => 'required|string',
            ]);
            
            $sku = $this->generateSKU($request->name, $request->product_id);

            $product = ProductDetail::create([
                'product_id' => $request->product_id,
                'discount_id' => $request->discount_id,
                'name' => $request->name,
                'quantity' => $request->quantity,
                'price' => $request->price,
               'SKU' => $sku,
                'product_image' => $request->product_image,
            ]);
            DB::commit();

            return ApiResponse::success($product);
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

    public function edit(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail,id',
                'product_id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Product,id',
                'discount_id' => 'nullable|exists:NadzorServera\Skijasi\Module\Commerce\Models\Discount,id',
                'name' => 'required|string|max:255',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                's_k_u' => 'nullable|string|max:255',
                'product_image' => 'required|string',
            ]);

            $product = ProductDetail::where('id', $request->id)->update([
                'product_id' => $request->product_id,
                'discount_id' => $request->discount_id,
                'name' => $request->name,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'SKU' => $request->s_k_u,
                'product_image' => $request->product_image,
            ]);
            DB::commit();

            return ApiResponse::success($product);
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

             private function generateSKU($name, $productId)
            {
                $base = Str::slug(strtolower($name)) . '-' . $productId . '-' . date('Y');
                $sku = $base;
                $counter = 1;
            
                while (ProductDetail::where('SKU', $sku)->exists()) {
                    $sku = $base . '-' . $counter;
                    $counter++;
                }
                return strtolower($sku);
            }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail',
            ]);

            $product_details = ProductDetail::find($request->id);
            $product_details->delete();

            DB::commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }



    public function browse(Request $request)
    {
        try {
            // Validate incoming request parameters
            $request->validate([
                'page' => 'sometimes|required|integer',
                'limit' => 'sometimes|required|integer',
                'relation' => 'nullable',
            ]);
    
            // Fetch product details with optional relations
            if ($request->has('page') || $request->has('limit')) {
                $productDetails = ProductDetail::when($request->relation, function ($query) use ($request) {
                    return $query->with(explode(',', $request->relation));
                })->paginate($request->limit ?? 10);
            } else {
                $productDetails = ProductDetail::when($request->relation, function ($query) use ($request) {
                    return $query->with(explode(',', $request->relation));
                })->get();
            }
    
            // Structure the response data
            $data['productDetails'] = $productDetails->toArray();
    
            // Return a successful response
            return ApiResponse::success($data);
        } catch (Exception $e) {
            // Handle exceptions and return a failed response
            return ApiResponse::failed($e);
        }
    }
    
    
    


}
