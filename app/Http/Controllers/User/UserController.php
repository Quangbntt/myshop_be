<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->get('name');
        $phone = $request->get('phone');
        $status = $request->get('status');
        $arrName      = (array)$request->get('name');
        $data = DB::table('users')
        ->leftjoin('roles', 'users.groupid', '=', 'roles.roles_id')
        ->whereBetween('created_at', [$request['startDate'], $request['endDate']]);
        if($name) {
            $data = $data->where('name','LIKE','%'.$name.'%');
        }
        if($phone) {
            $data = $data->where('phone','LIKE','%'.$phone.'%');
        }
        if($status) {
            switch ($status) {
                case 0:
                    $data = $data;
                    break;
                case 1:
                    $data = $data->where('status', 1);
                    break;
                case 2:
                    $data = $data->where('status', 0);
                    break;
                default:
                    break;
            }
        }
        $response = $data->paginate($request['size'] ?? 10)->toArray();
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // Tạo mới User
    public function create(Request $request)
    {
        $data = $request->all();
        $user = new User;
        $hash = rand(10000,99999);
        $username = (!empty($data['username'])) ? $data['username'] : '';
        $password = (!empty($data['password'])) ? md5($data['password'] . $hash) : '';
        $groupid = (!empty($data['groupid'])) ? $data['groupid'] : '';
        $name = (!empty($data['name'])) ? $data['name'] : '';
        $address = (!empty($data['address'])) ? $data['address'] : '';
        $email = (!empty($data['email'])) ? $data['email'] : '';
        $phone = (!empty($data['phone'])) ? $data['phone'] : '';

        $dataPhone = DB::table('users')
        ->select('phone')
        ->where('phone', $phone)
        ->groupBy('phone')
        ->get();
        if(count($dataPhone) === 0) {
            $phone = $phone;
        } else {
            $phone = "";
            $message = "Số điện thoại đã tồn tại";
        }

        $dataEmail = DB::table('users')
        ->select('email')
        ->where('email', $email)
        ->groupBy('email')
        ->get();
        if(count($dataEmail) === 0) {
            $email = $email;
        } else {
            $email = "";
            $message = "Email đã tồn tại";
        }

        if(!empty($phone) && !empty($email)){
            $user->username = $username;
            $user->token = '';
            $user->password = $password;
            $user->groupid = $groupid;
            $user->name = $name;
            $user->address = $address;
            $user->email = $email;
            $user->hash = $hash;
            $user->phone = $phone;
            $user->status = $data['status'];
            $user->save();
        }
        else {
            $result['message'] = $message;
		    return response()->json($result, 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->all();
        $hash = rand(10000,99999);
        $username = (!empty($data['username'])) ? $data['username'] : '';
        $password = (!empty($data['password'])) ? md5($data['password'] . $hash) : '';
        $groupid = (!empty($data['groupid'])) ? $data['groupid'] : '';
        $name = (!empty($data['name'])) ? $data['name'] : '';
        $address = (!empty($data['address'])) ? $data['address'] : '';
        $email = (!empty($data['email'])) ? $data['email'] : '';
        $phone = (!empty($data['phone'])) ? $data['phone'] : '';
        $token = md5(base64_encode($name).'.'.base64_encode($password).'.'.base64_encode($data['id']));

        DB::table('users')
        ->where('id', '=', $data['id'])
        ->update([
            'username'  => $username,
            'password'  => $password,
            'token'     => $token,
            'groupid'   => $groupid,
            'name'      => $name,
            'address'   => $address,
            'email'     => $email,
            'hash'      => $hash,
            'phone'     => $phone,
            'status'    => $data['status']
        ]);
        $res = User::where('id', '=', $request['id'])->get();

        return response()->json($res);

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $user = new User;
        if($request['id'] > 0) {
            $data = $user->where('id', '=', $request['id'])->delete();
            // $response = DB::table('users')->get();
            // return reponse()->json($response);
        } else {
            $error = [
                "status"    => "error",
                "message"   => "Mã người dùng không tồn tại",
                "errorCode" => null,
            ];
        }
    }
    // Api tìm kiếm khách hàng theo tên
    public function getUserInfor(Request $request) {
        $data = DB::table('users')
        ->select(
            'id',
            'name'
        )
        ->get();
        $query = [
            'data' => $data
        ];
        return response()->json($query);
    }
    // Api tìm kiếm khách hàng theo số điện thoại
    public function getUserPhone(Request $request) {
        $data = DB::table('users')
        ->select(
            'id',
            'phone as name'
        )
        ->get();
        $query = [
            'data' => $data
        ];
        return response()->json($query);
    }
    //Api đổi trạng thái tài khoản
    public function changeStatus(Request $request) {
        $status = $request->get('status');
        $id = $request->get('id');
        $data = DB::table('users')
        ->where('id', '=', (int)$id)
        ->update([
            'status' => $status
        ]);
        $res = DB::table('users')
        ->where('id', "=", $id)->get();
        $query = [
            'data' => $res
        ];
        return response()->json($query);
    }
}
