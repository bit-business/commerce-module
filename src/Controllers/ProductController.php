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
             'items.*.price' => 'required|numeric|min:0|regex:/^\d*\.?\d{0,2}$/',
                'items.*.s_k_u' => 'nullable|string|max:255',
                'items.*.product_image' => 'required|string',
                'galleryimages' => 'nullable',
                'galleryimages.*' => 'string',
                'sakrij' => 'nullable',
                'zatvoriprijave' => 'nullable',
                'prijaveposebni' => 'nullable',
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

                'name_en' => $request->name_en,
                'name_it' => $request->name_it,

                'desc' => $request->desc,
                'desc_en' => $request->desc_en,
                'desc_it' => $request->desc_it,
                'desc2' => $request->desc2,
                'desc2_en' => $request->desc2_en,
                'desc2_it' => $request->desc2_it,
                'desc3' => $request->desc3,
                'desc3_en' => $request->desc3_en,
                'desc3_it' => $request->desc3_it,
                'desc4' => $request->desc4,
                'desc4_en' => $request->desc4_en,
                'desc4_it' => $request->desc4_it,
                'desc5' => $request->desc5,
                'desc5_en' => $request->desc5_en,
                'desc5_it' => $request->desc5_it,

                'form_id' => $request->form_id,
                'sakrij' => $request->sakrij,
                'zatvoriprijave' => $request->zatvoriprijave,
                'prijaveposebni' => $request->prijaveposebni,
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

                'name_en' => 'nullable|string|max:255',
                'name_it' => 'nullable|string|max:255',
                'desc_en' => 'nullable|string',
                'desc_it' => 'nullable|string',
                'desc2' => 'nullable|string',
                'desc2_en' => 'nullable|string',
                'desc2_it' => 'nullable|string',
                'desc3' => 'nullable|string',
                'desc3_en' => 'nullable|string',
                'desc3_it' => 'nullable|string',
                'desc4' => 'nullable|string',
                'desc4_en' => 'nullable|string',
                'desc4_it' => 'nullable|string',
                'desc5' => 'nullable|string',
                'desc5_en' => 'nullable|string',
                'desc5_it' => 'nullable|string',
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

            $product->name_en = $request->name_en;
            $product->name_it = $request->name_it;

            $product->desc_en = $request->desc_en;
            $product->desc2_en = $request->desc2_en;
            $product->desc3_en = $request->desc3_en;
            $product->desc4_en = $request->desc4_en;
            $product->desc5_en = $request->desc5_en;
            $product->desc_it = $request->desc_it;
            $product->desc2_it = $request->desc2_it;
            $product->desc3_it = $request->desc3_it;
            $product->desc4_it = $request->desc4_it;
            $product->desc5_it = $request->desc5_it;
            $product->form_id = $request->form_id;
            $product->galleryimages = json_encode($request->galleryimages);
            $product->sakrij = $request->sakrij;
            $product->zatvoriprijave = $request->zatvoriprijave;

            if ($request->prijaveposebni) {
                // Clean up email addresses
                $emails = array_map(
                    'trim',
                    explode(',', $request->prijaveposebni)
                );
                $product->prijaveposebni = implode(',', array_filter($emails));
            } else {
                $product->prijaveposebni = null;
            }
            
            
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
