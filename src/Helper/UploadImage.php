<?php

namespace NadzorServera\Skijasi\Module\Commerce\Helper;

use Exception;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;


class UploadImage
{


    public static function createImage($base64, $name, $username)
    {
        try {
            $file_parts = explode(';base64,', $base64);
            if (count($file_parts) < 2) {
                return null; // Not a valid data URL
            }
    
            $file_type_aux = explode('/', $file_parts[0]);
            $file_type = end($file_type_aux);  // This will get the actual type of the file
            \Log::info("File type: $file_type"); // Log the file type
    
            $file_base64 = base64_decode($file_parts[1]);
            \Log::info("Decoded data length: " . strlen($file_base64)); // Log the length of decoded data
    
            // Construct the storage path
            $file ='profilephoto/korisnik/' . $name . '_' . $username . '_' . '.' . $file_type;
    
            if (Storage::put($file, $file_base64)) {
                return $file;
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }
    

    
    
    public static function createImageEdit($base64, $name, $username, $id)
    {
        try {
            $file_parts = explode(';base64,', $base64);
            if (count($file_parts) < 2) {
                return null; // Not a valid data URL
            }
            
            $file_type_aux = explode('/', $file_parts[0]);
            $file_type = end($file_type_aux);  // This will get the actual type of the file
            $file_base64 = base64_decode($file_parts[1]);
            
            // Construct the storage path
            $file = 'profilephoto/odobrenja/' . $name . '_' . $username . '_' . $id . '.' . $file_type;
    
            Storage::put($file, $file_base64);
    
            return $file;
        } catch (Exception $e) {
            return null;
        }
    }

    

    public static function deleteImage($file)
    {
        try {
            Storage::delete($file);

            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }
}
