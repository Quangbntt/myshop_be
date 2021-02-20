<?php

namespace App\Http\Controllers\Role;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function getAll(Request $request)
    {
        $data = DB::table('roles')
        ->select(
            'role_id',
            'role_name'
        )
        ->get();
        return response()->json($data);
    }
}
