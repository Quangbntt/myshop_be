<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Session;

class AuthController extends Controller
{
    public function __construct(User $User)
    {
        $this->user = $User;
    }

    public function login(Request $request)
    {
        extract($request->only(['username', 'password']));
        $result = [
            'message'      => '',
            'accessToken'  => '',
            'refreshToken' => '',
            'adm_name'     => '',
            'last_login'   => '',
            'adm_id'       => '',
        ];

        $username          = mb_strtolower($username, "UTF-8");
        $dataUser = $this->user->where('username', $username)
            ->first();
        if ($dataUser->status == 0) {
            $result['message'] = 'Tài khoản đang bị khóa';
            return response()->json($result, 401);
        }
        if (is_null($dataUser)) {
            $result['message'] = 'Không tồn tại tài khoản';
            return response()->json($result, 401);
        }

        $adm_hash     = $dataUser->hash;
        $password     = md5($password . $adm_hash);
        $adm_password = $dataUser->password;
        if ($password  == $adm_password) {
            $adm_id   = $dataUser->id;
            $strToken = base64_encode($dataUser->name) . '.' . base64_encode($password) . '.' . base64_encode($dataUser->id);
            $token    = md5($strToken);

            $dataUser->token = $token;

            $result['message']      = 'Đăng nhập thành công!';
            $result['adm_id']       = (int)$dataUser->id;
            $result['adm_name']     = $dataUser->name;
            $result['adm_phone']    = $dataUser->phone;
            $result['last_login']   = date('d-m-Y H:i:s');
            $result['accessToken']  = $token;
            $result['token']          = $token;
            $result['refreshToken'] = $token;

            return response()->json($result);
        } else {
            $result['message'] = 'Mật khẩu không đúng';
            return response()->json($result, 401);
        }
        // return response()->json($result);
    }

    public function loginAdmin(Request $request)
    {
        extract($request->only(['username', 'password']));
        $result = [
            'message'      => '',
            'accessToken'  => '',
            'refreshToken' => '',
            'adm_name'     => '',
            'last_login'   => '',
            'adm_id'       => '',
        ];

        $username          = mb_strtolower($username, "UTF-8");
        $dataUser = $this->user->where('username', $username)
            ->first();
        if ($dataUser->groupid == 3) {
            $result['message'] = 'Tài khoản không có quyền truy cập';
            return response()->json($result, 401);
        }
        if ($dataUser->status == 0) {
            $result['message'] = 'Tài khoản đang bị khóa';
            return response()->json($result, 401);
        }
        if (is_null($dataUser)) {
            $result['message'] = 'Không tồn tại tài khoản';
            return response()->json($result, 401);
        }

        $adm_hash     = $dataUser->hash;
        $password     = md5($password . $adm_hash);
        $adm_password = $dataUser->password;
        if ($password  == $adm_password) {
            $adm_id   = $dataUser->id;
            $strToken = base64_encode($dataUser->name) . '.' . base64_encode($password) . '.' . base64_encode($dataUser->id);
            $token    = md5($strToken);

            $dataUser->token = $token;

            $result['message']      = 'Đăng nhập thành công!';
            $result['adm_id']       = (int)$dataUser->id;
            $result['adm_name']     = $dataUser->name;
            $result['adm_phone']    = $dataUser->phone;
            $result['last_login']   = date('d-m-Y H:i:s');
            $result['accessToken']  = $token;
            $result['token']          = $token;
            $result['refreshToken'] = $token;

            return response()->json($result);
        } else {
            $result['message'] = 'Mật khẩu không đúng';
            return response()->json($result, 401);
        }
        // return response()->json($result);
    }

    public function logout()
    {
        $_SESSION['admin'] = '';
        return redirect('/')->with('notice', 'Bạn đã đăng xuất thành công khỏi hệ thống');
    }
}
