<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\UserSocial;
use App\Models\Ward;
use Illuminate\Support\Str;

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
        if ($name) {
            $data = $data->where('name', 'LIKE', '%' . $name . '%');
        }
        if ($phone) {
            $data = $data->where('phone', 'LIKE', '%' . $phone . '%');
        }
        if ($status) {
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
        $response = $data->paginate($request['size'] ?? 10);
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
        $hash = rand(10000, 99999);
        $uuId = Str::uuid()->toString();
        $username = (!empty($data['username'])) ? $data['username'] : '';
        $password = (!empty($data['password'])) ? md5($data['password'] . $hash) : '';
        $groupid = (!empty($data['groupid'])) ? $data['groupid'] : 3;
        $name = (!empty($data['name'])) ? $data['name'] : '';
        $address = (!empty($data['address'])) ? $data['address'] : '';
        $email = (!empty($data['email'])) ? $data['email'] : '';
        $phone = (!empty($data['phone'])) ? $data['phone'] : '';
        $status = (!empty($data['status'])) ? $data['status'] : 1;
        $province_id = (!empty($data['province_id'])) ? $data['province_id'] : '';
        $district_id = (!empty($data['district_id'])) ? $data['district_id'] : '';
        $ward_id = (!empty($data['ward_id'])) ? $data['ward_id'] : '';
        //1: nữ 2: Nam 3: Khác
        $sex = (!empty($data['sex'])) ? $data['sex'] : 3;
        $user_image = (!empty($data['user_image'])) ? $data['user_image'] : 'https://via.placeholder.com/100x100';
        $background_image = (!empty($data['background_image'])) ? $data['background_image'] : 'https://via.placeholder.com/400x200';
        $birthday = (!empty($data['birthday'])) ? $data['birthday'] : date("y-m-d");

        if ($province_id) {
            $dataProvince = Province::select('name')
                ->where('id', $province_id)
                ->firstOrFail();
            $dataDistrict = District::select('name')
                ->where('id', $district_id)
                ->firstOrFail();
            $dataWard = Ward::select('name')
                ->where('id', $ward_id)
                ->firstOrFail();
            $dataAdress = $dataWard->name . " - " . $dataDistrict->name . " - " . $dataProvince->name;
        }

        $dataPhone = DB::table('users')
            ->select('phone')
            ->where('phone', $phone)
            ->groupBy('phone')
            ->get();
        if (count($dataPhone) === 0) {
            $phone = $phone;
        } else {
            $phone = "";
            $message = "Số điện thoại đã tồn tại";
        }

        $dataUserName = DB::table('users')
            ->select('username')
            ->where('username', $username)
            ->groupBy('username')
            ->get();
        if (count($dataUserName) === 0) {
            $username = $username;
        } else {
            $username = "";
            $message = "Tên đăng nhập đã tồn tại";
            $result['message'] = $message;
            return response()->json($result, 422);
        }

        $dataEmail = DB::table('users')
            ->select('email')
            ->where('email', $email)
            ->groupBy('email')
            ->get();
        if (count($dataEmail) === 0) {
            $email = $email;
        } else {
            $email = "";
            $message = "Email đã tồn tại";
        }

        if (!empty($phone) && !empty($email)) {
            $user->username         = $username;
            $user->uuId             = $uuId;
            $user->token            = $uuId;
            $user->password         = $password;
            $user->groupid          = $groupid;
            $user->name             = $name;
            $user->address          = $address ? $address : $dataAdress;
            $user->email            = $email;
            $user->hash             = $hash;
            $user->phone            = $phone;
            $user->status           = $status;
            $user->user_image       = $user_image;
            $user->background_image = $background_image;
            $user->birthday         = $birthday;
            $user->sex              = $sex;
            $user->province_id      = $province_id;
            $user->district_id      = $district_id;
            $user->ward_id          = $ward_id;
            $user->save();
        } else {
            $result['message'] = "Tạo tài khoản không thành công";
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
        $hash = rand(10000, 99999);
        $username = (!empty($data['username'])) ? $data['username'] : '';
        $password = (!empty($data['password'])) ? md5($data['password'] . $hash) : '';
        $groupid = (!empty($data['groupid'])) ? $data['groupid'] : '';
        $name = (!empty($data['name'])) ? $data['name'] : '';
        $address = (!empty($data['address'])) ? $data['address'] : '';
        $email = (!empty($data['email'])) ? $data['email'] : '';
        $phone = (!empty($data['phone'])) ? $data['phone'] : '';
        $sex = (!empty($data['sex'])) ? $data['sex'] : 3;
        $user_image = (!empty($data['user_image'])) ? $data['user_image'] : '';
        $background_image = (!empty($data['background_image'])) ? $data['background_image'] : 'https://via.placeholder.com/400x200';
        $birthday = (!empty($data['birthday'])) ? $data['birthday'] : date("y-m-d");
        $token = md5(base64_encode($name) . '.' . base64_encode($password) . '.' . base64_encode($data['id']));
        $province_id = (!empty($data['province_id'])) ? $data['province_id'] : '';
        $district_id = (!empty($data['district_id'])) ? $data['district_id'] : '';
        $ward_id = (!empty($data['ward_id'])) ? $data['ward_id'] : '';

        $dataUserName = DB::table('users')
            ->select('username')
            ->where('username', $username)
            ->groupBy('username')
            ->get();
        if (count($dataUserName) === 0) {
            $username = $username;
        } else {
            $username = "";
            $message = "Tên đăng nhập đã tồn tại";
            $result['message'] = $message;
            return response()->json($result, 401);
        }

        DB::table('users')
            ->where('id', '=', $data['id'])
            ->update([
                'username'          => $username,
                'password'          => $password,
                'token'             => $token,
                'groupid'           => $groupid,
                'name'              => $name,
                'user_image'        => $user_image,
                'background_image'  => $background_image,
                'birthday'          => $birthday,
                'address'           => $address,
                'email'             => $email,
                'hash'              => $hash,
                'phone'             => $phone,
                'sex'               => $sex,
                'province_id'       => $province_id,
                'district_id'       => $district_id,
                'ward_id'           => $ward_id,
                'updated_at'        => date("y-m-d h:m:s"),
                'status'            => $data['status']
            ]);
        $res = User::where('id', '=', $request['id'])->get();

        return response()->json($res);
    }

    //update client
    public function updateClient(Request $request)
    {

        $data       = $request->all();
        $name       = (!empty($data['name'])) ? $data['name'] : '';
        $about_me   = (!empty($data['about_me'])) ? $data['about_me'] : 'NULL';
        $address    = (!empty($data['address'])) ? $data['address'] : '';
        $email      = (!empty($data['email'])) ? $data['email'] : '';
        $phone      = (!empty($data['phone'])) ? $data['phone'] : '';
        $sex        = (!empty($data['sex'])) ? $data['sex'] : 3;
        $user_image = (!empty($data['user_image'])) ? $data['user_image'] : '';
        $familyName = (!empty($data['familyName'])) ? $data['familyName'] : '';
        $givenName = (!empty($data['givenName'])) ? $data['givenName'] : '';
        $image = (!empty($data['image'])) ? $data['image'] : '';
        $birthday   = (!empty($data['birthday'])) ? $data['birthday'] : date("y-m-d");
        $province_id = (!empty($data['province_id'])) ? $data['province_id'] : '';
        $district_id = (!empty($data['district_id'])) ? $data['district_id'] : '';
        $ward_id = (!empty($data['ward_id'])) ? $data['ward_id'] : '';

        $dataPhone = DB::table('users')
            ->select('phone')
            ->where('phone', $phone)
            ->where('id', '!=', $data['id'])
            ->groupBy('phone')
            ->get();
        if (count($dataPhone) === 0) {
            $phone = $phone;
        } else {
            $phone = "";
            $message = "Số điện thoại đã tồn tại";
            $result['message'] = $message;
            return response()->json($result, 401);
        }

        $dataEmail = DB::table('users')
            ->select('email')
            ->where('email', $email)
            ->where('id', '!=', $data['id'])
            ->groupBy('email')
            ->get();
        if (count($dataEmail) === 0) {
            $email = $email;
        } else {
            $email = "";
            $message = "Email đã tồn tại";
            $result['message'] = $message;
            return response()->json($result, 401);
        }

        if (strlen($data['id']) > 10) {
            DB::table('user_socials')
                ->where('uuId', '=', $data['id'])
                ->update([
                    'email'             => $email,
                    'familyName'        => $familyName,
                    'givenName'         => $givenName,
                    'name'              => $name,
                    'image'             => $image,
                    'address'           => $address,
                    'phone'             => $phone,
                    'sex'               => $sex,
                    'province_id'       => $province_id,
                    'district_id'       => $district_id,
                    'ward_id'           => $ward_id,
                    'updated_at'        => date("y-m-d h:m:s")
                ]);
            $res = UserSocial::where('uuId', '=', $request['id'])->get();

            return response()->json($res);
        }
        DB::table('users')
            ->where('id', '=', $data['id'])
            ->update([
                'about_me'          => $about_me,
                'name'              => $name,
                'user_image'        => $user_image,
                'birthday'          => $birthday,
                'address'           => $address,
                'email'             => $email,
                'phone'             => $phone,
                'sex'               => $sex,
                'province_id'       => $province_id,
                'district_id'       => $district_id,
                'ward_id'           => $ward_id,
                'updated_at'        => date("y-m-d h:m:s"),
                'status'            => $data['status']
            ]);
        $res = User::where('id', '=', $request['id'])->get();

        return response()->json($res);
    }

    public function detail(Request $request)
    {
        $id = $request['id'];
        if (strlen($id)) {
            $res = User::where('uuId', '=', $request['id'])->get();
            return response()->json($res);
        }
        $res = User::where('id', '=', $request['id'])->get();
        return response()->json($res);
    }
    public function detailClient(Request $request)
    {
        $id = $request['id'];
        if (strlen($id)) {
            $res = User::where('id', '=', $request['id'])->get();
            return response()->json($res);
        }
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
        if ($request['id'] > 0) {
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
    public function getUserInfor(Request $request)
    {
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
    public function getUserPhone(Request $request)
    {
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
    public function changeStatus(Request $request)
    {
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
