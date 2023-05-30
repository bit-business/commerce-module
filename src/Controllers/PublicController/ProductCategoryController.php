<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers\PublicController;

use Exception;
use Illuminate\Http\Request;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    public function browse(Request $request)
    {
        try {
            $categories = ProductCategory::with('children')->get();
            $data['product_categories'] = $categories->toArray();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function read(Request $request)
    {
        try {
            $request->validate([
                'slug' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductCategory',
            ]);

            $categories = ProductCategory::with(['children'])->where('slug', $request->slug)->first();
            $data['product_categories'] = $categories->toArray();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }
}
