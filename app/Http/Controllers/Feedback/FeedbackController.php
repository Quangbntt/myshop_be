<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('feedbacks')
            ->select(DB::raw(
                'feedback_id,
                feedback_comment,
                feedback_customer_id,
                feedback_rate,
                users.name,
                users.address,
                users.status,
                users.user_image'
            ))
            ->join('users', 'users.id', '=', 'feedbacks.feedback_customer_id')
            ->where('users.status', 1)
            ->get();
        $arrReturn['data'] = $data;
        return response()->json($arrReturn);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $feedback = new Feedback;

        $customerid = (!empty($data['customerid'])) ? $data['customerid'] : '';
        $comment = (!empty($data['comment'])) ? $data['comment'] : '';
        $rate = (!empty($data['rate'])) ? $data['rate'] : '';

        $data = Feedback::select('feedback_customer_id')->get()->toArray();
        $id = [];
        foreach ($data as $k => $v) {
            array_push($id, $v['feedback_customer_id']);
        }
        if (in_array($customerid, $id)) {
            $response = array_merge([
                'code'   => 500,
                'status' => 'Bạn đã đánh giá rồi',
                // 'data' => $data
            ]);
        } else {
            $feedback->feedback_customer_id = $customerid;
            $feedback->feedback_comment = $comment;
            $feedback->feedback_rate = $rate;
            $feedback->save();
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                // 'data' => $data
            ]);
        }
        return response()->json($response, $response['code']);
    }
}
