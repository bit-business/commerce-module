<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail;

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
                'price' => 'required|integer|min:0',
                's_k_u' => 'nullable|string|max:255',
                'product_image' => 'required|string',
            ]);

            $product = ProductDetail::create([
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
                'price' => 'required|integer|min:0',
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
}
