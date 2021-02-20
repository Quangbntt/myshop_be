<?php

namespace App\Http\Controllers\Branch;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $branch = new Branch;
        // $branch->name="dolce & gabbana";
        // $branch->image="";
        // $branch->type=1;
        // $branch->code="";
        // $branch->save();
        $data = DB::table('branches')->get();
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $branch = new Branch;
        $branches_name = (!empty($data['branches_name'])) ? $data['branches_name'] : '';
        $branches_image = (!empty($data['branches_image'])) ? $data['branches_image'] : '';
        $branches_type = (!empty($data['branches_type'])) ? $data['branches_type'] : 1;
        $branches_code = (!empty($data['branches_code'])) ? $data['branches_code'] : '';

        $branch->branches_name = $branches_name;
        $branch->branches_image = $branches_image;
        $branch->branches_type = $branches_type;
        $branch->branches_code = $branches_code;
        $branch->save();
    }

    public function delete(Request $request)
    {
        $user = new Branch;
        if($request['branches_id'] > 0) {
            $data = $user->where('branches_id', '=', $request['branches_id'])->delete();
        } else {
            $error = [
                "status"    => "error",
                "message"   => "Mã thương hiệu không tồn tại",
                "errorCode" => null,
            ];
        }
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
        $branches_name = (!empty($data['branches_name'])) ? $data['branches_name'] : '';
        $branches_image = (!empty($data['branches_image'])) ? $data['branches_image'] : '';
        $branches_type = (!empty($data['branches_type'])) ? $data['branches_type'] : 1;
        $branches_code = (!empty($data['branches_code'])) ? $data['branches_code'] : '';

        DB::table('branches')
        ->where('branches_id', '=', $data['branches_id'])
        ->update([
            'branches_name'  => $branches_name,
            'branches_image'  => $branches_image,
            'branches_type'     => $branches_type,
            'branches_code'   => $branches_code,
        ]);
        $res = Branch::where('branches_id', '=', $request['branches_id'])->get();

        return response()->json($res);
    }
}
