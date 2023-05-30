<?php

namespace Database\Seeders\Skijasi\Commerce;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use NadzorServera\Skijasi\Models\Menu;

class CommerceMenusSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     *
     * @throws Exception
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            $menus = [
                0 => [
                    'key' => 'commerce-module',
                    'display_name' => 'Commerce Menu',
                ],
            ];

            $new_menus = [];
            foreach ($menus as $key => $value) {
                $menu = Menu::where('key', $value['key'])
                        ->first();

                if (isset($menu)) {
                    continue;
                }

                Menu::create($value);
            }
        } catch (Exception $e) {
            throw new Exception('Exception occur '.$e);
            DB::rollBack();
        }

        DB::commit();
    }
}
