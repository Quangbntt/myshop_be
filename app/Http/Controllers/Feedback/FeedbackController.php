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
        $data = DB::table('feedback')
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
        ->join('users', 'users.id', '=', 'feedback.feedback_customer_id')
        ->where('users.status', 1)
        ->get();
        $arrReturn['data']= $data;
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

        $feedback->customerid = $customerid;
        $feedback->comment = $comment;
        $feedback->rate = $rate;
        $feedback->save();
    }
}
