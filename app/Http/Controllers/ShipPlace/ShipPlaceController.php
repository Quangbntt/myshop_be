<?php

namespace App\Http\Controllers\ShipPlace;

use App\Http\Controllers\Controller;
use App\Models\ShipPlace;
use App\Models\User;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipPlaceController extends Controller
{
    public function getShipPlace(Request $request)
    {
        $id = $request->get('id');
        $data = User::with('shipPlace')
            ->where('id', $id)
            ->selectRaw('id, name, phone');
        $res = $data->paginate($request['per_page'] ?? 10);
        return response()->json($res);
    }
    public function listDefault(Request $request)
    {
        $id = $request->get('id');
        $data = DB::table('shipplace')
            ->selectRaw('shipplace.id, shipplace.address, users.name, users.phone')
            ->join('users', 'users.id', 'user_id')
            ->where('user_id', $id)
            ->where('default', 1)
            ->get();
        return response()->json($data);
    }
    public function delete(Request $request)
    {
        $shipPlace = new ShipPlace();
        $value =  DB::table('shipplace')
            ->where('id', '=', $request['id'])->get();
        if ($request['id'] > 0 && $value[0]->default == 0) {
            $data = $shipPlace->where('id', '=', $request['id'])->delete();
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                // 'data' => $data
            ]);
        } else {
            $response = array_merge([
                "status"    => "error",
                "message"   => "Có lỗi xảy ra",
                "code"      => 400,
            ]);
        }
        return response()->json($response, $response['code']);
    }
    public function create(Request $request)
    {
        $data = $request->all();
        $shipPlace = new ShipPlace();
        $user_id = (!empty($data['user_id'])) ? $data['user_id'] : '';
        $address = (!empty($data['address'])) ? $data['address'] : '';
        $lat = (!empty($data['lat'])) ? $data['lat'] : '';
        $long = (!empty($data['long'])) ? $data['long'] : '';

        $dataCheck = DB::table('shipplace')
            ->where('user_id', '=', $user_id)
            ->get()->toArray();
        $count = count($dataCheck);
        if (!empty($user_id) && !empty($shipPlace)) {
            $shipPlace->user_id         = $user_id;
            $shipPlace->address         = $address;
            $shipPlace->lat             = $lat;
            $shipPlace->long            = $long;
            $shipPlace->default         = $count > 0 ? 0 : 1;
            $shipPlace->save();
        } else {
            $result['message'] = "Tạo mới thất bại";
            return response()->json($result, 500);
        }
    }
    public function update(Request $request)
    {
        $data = $request->all();
        $user_id = (!empty($data['user_id'])) ? $data['user_id'] : '';
        $address = (!empty($data['address'])) ? $data['address'] : '';
        $lat = (!empty($data['lat'])) ? $data['lat'] : '';
        $long = (!empty($data['long'])) ? $data['long'] : '';

        // DB::table('shipplace')
        //     ->where('user_id', '=', $data['user_id'])
        //     ->update([
        //         'default'          => 0,
        //     ]);

        DB::table('shipplace')
            ->where('id', '=', $data['id'])
            ->update([
                'address'           => $address,
                'lat'               => $lat,
                'long'              => $long,
            ]);
        $res = ShipPlace::where('user_id', '=', $request['user_id'])->get();

        return response()->json($res);
    }
    public function default(Request $request)
    {
        $data = $request->all();
        DB::table('shipplace')
            ->where('user_id', '=', $data['user_id'])
            ->update([
                'default'          => 0,
            ]);

        DB::table('shipplace')
            ->where('id', '=', $data['id'])
            ->update([
                'default'          => 1,
            ]);
        $res = ShipPlace::where('user_id', '=', $request['user_id'])->get();

        return response()->json($res);
    }
}
