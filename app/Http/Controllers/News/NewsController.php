<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('news')->limit(3)->get();
        $res['data'] = $data;
        return response()->json($res);
    }
    public function listAll(Request $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $data = DB::table('news')
            ->whereBetween('created_at', [$startDate . " 00:00:00", $endDate . " 23:59:59"]);
        $res = $data->paginate($request['size'] ?? 10);
        return response()->json($res);
    }
    public function listAllDesc(Request $request)
    {
        $data = DB::table('news');
        $res = $data->orderByDesc('created_at')->paginate($request['size'] ?? 10);
        return response()->json($res);
    }
    public function listAllClient(Request $request)
    {
        if ($request['page'] == 1) {
            $data = DB::table('news')->offset(3)->limit($request['size'])->get();
            $res['data'] = $data;
        } else {
            $data = DB::table('news')->offset($request['size'] + 3)->limit($request['size'])->get();
            $res['data'] = $data;
        }
        $total = $data = DB::table('news')->get();
        $res['total'] = count($total) - 3;
        return response()->json($res);
    }
    public function create(Request $request)
    {
        $data = $request->all();
        $new = new News();

        $title = (!empty($data['title'])) ? $data['title'] : '';
        $review = (!empty($data['review'])) ? $data['review'] : '';
        $body = (!empty($data['body'])) ? $data['body'] : '';
        $image = (!empty($data['image'])) ? $data['image'] : '';

        $new->title = $title;
        $new->review = $review;
        $new->body = $body;
        $new->image = $image;
        $new->save();
    }
    public function delete(Request $request)
    {
        $new = new News();
        if ($request['id'] > 0) {
            $data = $new->where('id', '=', $request['id'])->delete();
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                // 'data' => $data
            ]);
            return response()->json($response, $response['code']);
        } else {
            $error = [
                "status"    => "error",
                "message"   => "Tin tức không tồn tại",
                "errorCode" => 500,
            ];
            return response()->json($error, $error['errorCode']);
        }
    }
    public function detail(Request $request)
    {
        $id = $request->get('id');
        $data = News::where('id', $id)->first();
        return response()->json($data);
    }
}
