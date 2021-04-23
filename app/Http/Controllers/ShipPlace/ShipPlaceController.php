<?php

namespace App\Http\Controllers\ShipPlace;

use App\Http\Controllers\Controller;
use App\Models\ShipPlace;
use Illuminate\Http\Request;

class ShipPlaceController extends Controller
{
    public function getShipPlace(Request $request)
    {
        $id = $request->get('id');
        $data = ShipPlace::selectRaw('id, uer_id, address, users.name, users.phone, created_at, default')
            ->join('users', 'users.id', 'shipplace.id')
            ->where('id', $id);
        $res = $data->paginate($request['size'] ?? 10);
        return response()->json($res);
    }
}
