<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Models\ProductReview;

class ReviewController extends Controller
{
    public function browse(Request $request)
    {
        try {
            $reviews = ProductReview::with(['product', 'user', 'orderDetail'])
                ->latest()
                ->paginate($request->limit);

            return ApiResponse::success(['reviews' => $reviews->toArray()]);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function read(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductReview,id',
            ]);

            $review = ProductReview::with(['product', 'user', 'orderDetail'])
                ->where('id', $request->id)
                ->firstOrFail();

            return ApiResponse::success(['review' => $review->toArray()]);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductReview,id',
            ]);

            $review = ProductReview::findOrFail($request->id);
            $review->delete();

            DB::commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }
}
