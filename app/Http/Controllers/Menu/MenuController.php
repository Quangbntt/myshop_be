<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function getMenu() {
        $arrReturn = array();
        $arrModulePa = array();
        $arrModuleChild = array();

        $data = DB::table('menus')
        ->orderBy('menus_id')->get();

            if(count($data)) {
               foreach ($data as $key => $row) {
                    $id = $row->menus_id;
                        $arrModulePa[] = array(
                            'id'            =>$row->menus_id,
                            'name'          =>$row->menus_name,
                            'path'          =>$row->menus_path,
                            'css'           =>$row->menus_css,
                            'permissions'   =>''
                        );
               }

            }
        foreach ($arrModulePa as $key => $value) {
            $id = $value['id'];
            if(isset($arrModuleChild[$id])){
                $value['children'] = $arrModuleChild[$id];
            }
            $arrReturn[] =  $value;
        }
        return $arrReturn;

    }
}
