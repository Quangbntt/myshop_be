<?php

namespace App\Http\Controllers\UserSocial;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSocialController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $userSocial = new User();
        $email = (!empty($data['email'])) ? $data['email'] : '';
        $familyName = (!empty($data['familyName'])) ? $data['familyName'] : 3;
        $givenName = (!empty($data['givenName'])) ? $data['givenName'] : '';
        $uuId = (!empty($data['uuId'])) ? $data['uuId'] : '';
        $image = (!empty($data['image'])) ? $data['image'] : '';
        $name = (!empty($data['name'])) ? $data['name'] : '';

        $dataCheck = User::select('uuId')
            ->where('uuId', $uuId)
            ->groupBy('uuId')
            ->get();
        if (count($dataCheck) === 0) {
            // $userSocial->familyName         = $familyName;
            // $userSocial->givenName         = $givenName;
            $userSocial->uuId          = $uuId;
            $userSocial->name             = $name;
            $userSocial->user_image          = $image;
            $userSocial->email            = $email;
            $userSocial->save();
        }
        $data = User::where('uuId', $uuId)->get();
        return response()->json($data);
        // $message = "Tài khoản đã tồn tại";
        // $result['message'] = $message;
        // return response()->json($result, 401);
    }
}
