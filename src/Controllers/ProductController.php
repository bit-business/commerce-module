<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Models\Product;
use NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail;

use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function browse(Request $request)
    {
        try {
            $request->validate([
                'page' => 'sometimes|required|integer',
                'limit' => 'sometimes|required|integer',
                'relation' => 'nullable',
            ]);

            if ($request->has('page') || $request->has('limit')) {
                $products = Product::when($request->relation, function ($query) use ($request) {
                    return $query->with(explode(',', $request->relation));
                })->paginate($request->limit);
            } else {
                $products = Product::when($request->relation, function ($query) use ($request) {
                    return $query->with(explode(',', $request->relation));
                })->get();
            }

            $data['products'] = $products->toArray();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function browseBin(Request $request)
    {
        try {
            $request->validate([
                'page' => 'sometimes|required|integer',
                'limit' => 'sometimes|required|integer',
                'relation' => 'nullable',
            ]);

            if ($request->has('page') || $request->has('limit')) {
                $products = Product::onlyTrashed()->when($request->relation, function ($query) use ($request) {
                    return $query->with(explode(',', $request->relation));
                })->paginate($request->limit ?? 10);
            } else {
                $products = Product::onlyTrashed()->when($request->relation, function ($query) use ($request) {
                    return $query->with(explode(',', $request->relation));
                })->get();
            }

            $data['products'] = $products->toArray();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function add(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'product_category_id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductCategory,id',
                'name' => 'required|string|max:255',
                // 'slug' => 'required|string|max:255|unique:NadzorServera\Skijasi\Module\Commerce\Models\Product',
                'product_image' => 'required|string',
                'desc' => 'nullable|string',
                'desc2' => 'nullable|string',
                'desc3' => 'nullable|string',
                'desc4' => 'nullable|string',
                'desc5' => 'nullable|string',
                'form_id' => 'nullable',
                'items' => 'required|array',
                'items.*.discount_id' => 'nullable|integer|exists:NadzorServera\Skijasi\Module\Commerce\Models\Discount,id',
                'items.*.name' => 'required|string|max:255',
                'items.*.quantity' => 'required|integer|min:0',
                'items.*.price' => 'required|integer|min:0',
                'items.*.s_k_u' => 'nullable|string|max:255',
                'items.*.product_image' => 'required|string',
                'galleryimages' => 'nullable',
                'galleryimages.*' => 'string',
                'sakrij' => 'nullable',
                'zatvoriprijave' => 'nullable',
            ]);

            $slug = $this->generateSlug($request->name);

            Product::create([
                'product_category_id' => $request->product_category_id,
                'name' => $request->name,
                'slug' => $slug,
                'product_image' => $request->product_image,
                'mjesto' => $request->mjesto,
                'datum_pocetka' => $request->datum_pocetka,
                'datum_kraja' => $request->datum_kraja,
                'desc' => $request->desc,
                'desc2' => $request->desc2,
                'desc3' => $request->desc3,
                'desc4' => $request->desc4,
                'desc5' => $request->desc5,
                'form_id' => $request->form_id,
                'sakrij' => $request->sakrij,
                'zatvoriprijave' => $request->zatvoriprijave,
                'galleryimages' => json_encode($request->galleryimages),
            ]);

            $product = Product::select('id')->latest()->first();

            foreach ($request->items as $key => $item) {
                ProductDetail::create([
                    'product_id' => $product->id,
                    'discount_id' => $item['discount_id'] ?? null,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'SKU' => $item['s_k_u'] ?? null,
                    'product_image' => $item['product_image'],
                ]);
            }

            DB::commit();

            return ApiResponse::success($product);
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

    private function generateSlug($name)
    {
        $base = Str::slug(strtolower($name));
        $slug = $base;
        $counter = 1;
    
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
    
        return $slug;
    }


    public function restore(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Product',
            ]);

            $product = Product::withTrashed()->find($request->id);
            $product->productDetails()->restore();
            $product->restore();
            DB::commit();

            return ApiResponse::success($product);
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

    public function restoreMultiple(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required',
            ]);

            $id_list = explode(',', $request->ids);

            DB::beginTransaction();

            foreach ($id_list as $key => $id) {
                $products = Product::withTrashed()->findOrFail($id);
                $products->productDetails()->restore();
                $products->restore();
            }

            DB::commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

    public function read(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Product',
                'relation' => 'nullable',
            ]);

            $product = Product::when($request->relation, function ($query) use ($request) {
                return $query->with(explode(',', $request->relation));
            })->where('id', $request->id)->first();
            $data['product'] = $product->toArray();
            

            $galleryImages = json_decode($product->galleryimages, true);
            $data['product']['galleryimages'] = $galleryImages;
    

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function edit(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Product,id',
                'product_category_id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\ProductCategory,id',
                'name' => 'required|string|max:255',
                'product_image' => 'required|string',
                'desc' => 'nullable|string',
                'desc2' => 'nullable|string',
                'desc3' => 'nullable|string',
                'desc4' => 'nullable|string',
                'desc5' => 'nullable|string',
                'form_id' => 'nullable',
                'galleryimages' => 'nullable',
                'galleryimages.*' => 'string',
            ]);

            $product = Product::where('id', $request->id)->first();
            $product->product_category_id = $request->product_category_id;
            $product->datum_pocetka = $request->datum_pocetka;
            $product->datum_kraja = $request->datum_kraja;
            $product->mjesto = $request->mjesto;
            $product->name = $request->name;
            $product->product_image = $request->product_image;
            $product->desc = $request->desc;
            $product->desc2 = $request->desc2;
            $product->desc3 = $request->desc3;
            $product->desc4 = $request->desc4;
            $product->desc5 = $request->desc5;
            $product->form_id = $request->form_id;
            $product->galleryimages = json_encode($request->galleryimages);
            $product->sakrij = $request->sakrij;
            $product->zatvoriprijave = $request->zatvoriprijave;
            
            $product->save();

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
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Product',
            ]);

            $products = Product::findOrFail($request->id);
            $products->productDetails()->delete();
            $products->delete();

            DB::commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

    public function forceDelete(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\Product',
            ]);

            $product = Product::withTrashed()->find($request->id);
            $product->productDetails()->forceDelete();
            $product->forceDelete();
            DB::commit();

            return ApiResponse::success($product);
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required',
            ]);

            $id_list = explode(',', $request->ids);

            DB::beginTransaction();

            foreach ($id_list as $key => $id) {
                $products = Product::findOrFail($id);
                $products->productDetails()->delete();
                $products->delete();
            }

            DB::commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }

    public function forceDeleteMultiple(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required',
            ]);

            $id_list = explode(',', $request->ids);

            DB::beginTransaction();

            $products = Product::withTrashed()->whereIn('id', $id_list)->get();

            foreach ($products as $product) {
                $product->productDetails()->forceDelete();
                $product->forceDelete();
            }

            DB::commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollback();

            return ApiResponse::failed($e);
        }
    }
}
