<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->get('name', false);
        // $arrName      = (array)$request->get('name');

        if ($name) {
            $name = array_map(function ($item) {
                $item = (int) $item;
                return $item;
            }, $name);
            $data = DB::table('categories')
                ->select(
                    'categories_id as id',
                    'categories_name as name',
                    'categories_parentid as parentid',
                    'categories_displayorder as displayorder',
                    'categories_showonhome as showonhome',
                    'categories_status as status',
                    'created_at as created_at'
                )
                ->whereIn('categories_id', $name)
                ->where('categories_parentid', 0)->get();
        } else {
            $data = DB::table('categories')
                ->select(
                    'categories_id as id',
                    'categories_name as name',
                    'categories_parentid as parentid',
                    'categories_displayorder as displayorder',
                    'categories_showonhome as showonhome',
                    'categories_status as status',
                    'created_at as created_at'
                )
                ->where('categories_parentid', 0)->orderBy('categories_parentid')->get();
        }
        foreach ($data as &$item) {
            $dataChild = DB::table('categories')
                ->select(
                    'categories_id as id',
                    'categories_name as name',
                    'categories_parentid as parentid',
                    'categories_displayorder as displayorder',
                    'categories_showonhome as showonhome',
                    'categories_status as status',
                    'created_at as created_at'
                )
                ->where('categories_parentid', $item->id)->get();
            $item->product = $dataChild;
        }

        $res = array_slice($data->toArray(), $request['page'] ?? 0, $request['limit'] ?? 10);
        return response()->json($res);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $category = new Category;

        $name = (!empty($data['name'])) ? $data['name'] : '';
        $metatitle = (!empty($data['metatitle'])) ? $data['metatitle'] : '';
        $parentid = (!empty($data['parentid'])) ? $data['parentid'] : '';
        $displayorder = (!empty($data['displayorder'])) ? $data['displayorder'] : '';
        $showonhome = (!empty($data['showonhome'])) ? $data['showonhome'] : '';

        $category->name = $name;
        $category->metatitle = $metatitle;
        $category->parentid = $parentid;
        $category->displayorder = $displayorder;
        $category->showonhome = $showonhome;
        $category->status = $data['status'];
        $category->save();
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $name = (!empty($data['name'])) ? $data['name'] : '';
        $metatitle = (!empty($data['metatitle'])) ? $data['metatitle'] : '';
        $parentid = (!empty($data['parentid'])) ? $data['parentid'] : '';
        $displayorder = (!empty($data['displayorder'])) ? $data['displayorder'] : '';
        $showonhome = (!empty($data['showonhome'])) ? $data['showonhome'] : '';

        DB::table('categories')
            ->where('id', '=', $data['id'])
            ->update([
                'name' => $name,
                'metatitle' => $metatitle,
                'parentid' => $parentid,
                'displayorder' => $displayorder,
                'showonhome' => $showonhome,
                'status' => $data['status']
            ]);
        $res = Category::where('id', '=', $request['id'])->get();

        return response()->json($res);
    }
    // Api tìm loại sản phẩm theo tên
    public function getCategoryInfor()
    {
        $data = DB::table('categories')
            ->select(
                'categories_id as id',
                'categories_name as name'
            )
            ->where('categories_parentid', 0)
            ->get();
        $response = [
            'data' => $data
        ];
        return response()->json($response);
    }
    //Api đổi trạng thái loại sản phẩm
    public function changeStatus(Request $request)
    {
        $status = $request->get('status');
        $id = $request->get('id');
        $data = DB::table('categories')
            ->where('categories_id', '=', (int)$id)
            ->update([
                'categories_status' => $status
            ]);
        $res = DB::table('categories')
            ->where('categories_id', "=", $id)->get();
        $query = [
            'data' => $res
        ];
        return response()->json($query);
    }
}
