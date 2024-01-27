<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers\PublicController;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Helper\UploadImage;

use NadzorServera\Skijasi\Models\User;
use NadzorServera\Skijasi\Module\Commerce\Models\ZboroviModel;



class UserController extends Controller
{
    public function edit(Request $request)
    {
        DB::beginTransaction();

        try {
          

            $user = auth()->user();
      

            if ($request->has('additional_info')) {
                $user->additional_info = $request->additional_info;
            }
    
            if ($request->has('datumrodjenja')) {
                $user->datumrodjenja = $request->datumrodjenja;
            }
            if ($request->has('brojmobitela')) {
                $user->brojmobitela = $request->brojmobitela;
            }
            if ($request->has('drzava')) {
                $user->drzava = $request->drzava;
            }
            if ($request->has('grad')) {
                $user->grad = $request->grad;
            }
            if ($request->has('postanskibroj')) {
                $user->postanskibroj = $request->postanskibroj;
            }
            if ($request->has('adresa')) {
                $user->adresa = $request->adresa;
            }
            if ($request->has('oib')) {
                $user->oib = $request->oib;
            }
            if ($request->has('spol')) {
                $user->spol = $request->spol;
            }

            if ($request->has('avatar')) {
               /* if ($user->avatar != 'files/shares/default-user.png') {
                    UploadImage::deleteImage($user->avatar);
                }
                $filename = UploadImage::createImage($request->avatar);
                $user->avatar = $filename;
                */
                
                    // Instead of immediately updating the avatar, store it as a temporary avatar awaiting approval
                    $filename = UploadImage::createImageEdit($request->avatar, $user->name, $user->username, $user->id);
                    $user->new_avatar = $filename; 
                    $user->avatar_approved = true; 
                
            }
            

            $user->save();

            DB::commit();

            return ApiResponse::success(['user' => auth()->user()]);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }


    public function prijavnicaedit(Request $request)
    {
        DB::beginTransaction();

        try {
          

            $user = auth()->user();
      

            if ($request->has('additional_info')) {
                $user->additional_info = $request->additional_info;
            }
    
            if ($request->has('datumrodjenja')) {
                $user->datumrodjenja = $request->datumrodjenja;
            }
            if ($request->has('brojmobitela')) {
                $user->brojmobitela = $request->brojmobitela;
            }
            if ($request->has('drzava')) {
                $user->drzava = $request->drzava;
            }
            if ($request->has('grad')) {
                $user->grad = $request->grad;
            }
            if ($request->has('postanskibroj')) {
                $user->postanskibroj = $request->postanskibroj;
            }
            if ($request->has('adresa')) {
                $user->adresa = $request->adresa;
            }
            if ($request->has('oib')) {
                $user->oib = $request->oib;
            }
            if ($request->has('spol')) {
                $user->spol = $request->spol;
            }

            if ($request->has('podrucnizbor')) {
                $user->department = $request->podrucnizbor;
            }

            if ($request->has('avatar')) {
         
                    // Instead of immediately updating the avatar, store it as a temporary avatar awaiting approval
                    $filename = UploadImage::createImageEdit($request->avatar, $user->name, $user->username, $user->id);
                    $user->new_avatar = $filename; 
                    $user->avatar_approved = true; 
                
            }
            if ($request->has('zahtjev_approved')) {
                $user->zahtjev_approved = 1; 
            }
            

            $user->save();

            DB::commit();

            return ApiResponse::success(['user' => auth()->user()]);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }
    

    public function changePassword(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'old_password' => 'required|string',
                'password' => 'required|confirmed|string',
            ]);

            $user = auth()->user();
            $user->password = Hash::make($request->password);
            $user->save();

            DB::commit();

            return ApiResponse::success(['user' => auth()->user()]);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }




    public function countUsers() {
        $totalUsers = User::count();
        return response()->json(['totalUsers' => $totalUsers]);
    }

    public function fetchZborovi() {
        $departments = ZboroviModel::all();
        return response()->json($departments);
    }
    
}
