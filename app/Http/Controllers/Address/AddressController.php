<?php

namespace App\Http\Controllers\Address;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request) {
        $id = $request->get('id');
        $data = Province::with('district.ward')->get();
        $res['data'] = $data;
        return response()->json($res);
    }
}
