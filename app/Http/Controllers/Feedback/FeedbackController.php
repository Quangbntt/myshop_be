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
        // $feedback = new Feedback;
        // $feedback->customerid=2;
        // $feedback->comment="Tôi vô cùng hài lòng";
        // $feedback->rate=5;
        // $feedback->save();
        $data = DB::table('feedback')->get();
        return response()->json($data);
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
