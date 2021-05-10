<?php

namespace App\Http\Controllers\FeedbackProduct;

use App\Http\Controllers\Controller;
use App\Models\FeedbackProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackProductController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $product_id = $request->get('product_id');
        $customer_id = $request->get('customer_id');
        $orders_id = $request->get('orders_id');

        if ($orders_id) {
            DB::table('orders')
                ->where('orders_id', '=', $orders_id)
                ->update([
                    //sau khi đánh giá sẽ không được đánh giá nữa
                    'orders_type'  => 3,
                ]);
        }

        $feedbackProduct = new FeedbackProduct();
        $comment = (!empty($data['comment'])) ? $data['comment'] : '';
        $rate = (!empty($data['rate'])) ? $data['rate'] : 5;
        $image = (!empty($data['image'])) ? $data['image'] : '';

        $feedbackProduct->comment = $comment;
        $feedbackProduct->rate = $rate;
        $feedbackProduct->images = serialize(array($image));
        $feedbackProduct->product_id = $product_id;
        $feedbackProduct->customer_id = $customer_id;
        $feedbackProduct->save();

        $dataRate = FeedbackProduct::select('feedback_products.product_id', 'rate')
            ->join('products', 'products.product_id', 'feedback_products.product_id')
            ->get();
        $arr = [];
        $i = 1;
        foreach ($dataRate as $k => $v) {
            if (array_key_exists($v->product_id, $arr)) {
                $arr[$v->product_id]['rate'] += ($v->rate);
                $arr[$v->product_id]['count'] += $i;
            } else {
                $arr[$v->product_id]['rate'] = $v->rate;
                $arr[$v->product_id]['count'] = $i;
            }
        }
        $arg = [];
        foreach ($arr as $k => $v) {
            $arg[$k] = round(($v['rate'] / $v['count']), 2);
        }
        foreach ($arg as $k => $v) {
            DB::table('products')
                ->where('products.product_id', $k)
                ->update([
                    'products.product_rate' => $v,
                ]);
        }
    }
    public function list(Request $request)
    {
        $product_id = $request->get('product_id');
        $data = FeedbackProduct::selectRaw('comment, feedback_products.created_at, customer_id, feedback_products.id, images, product_id, rate, users.user_image, users.name')
            ->join('users', 'users.id', 'customer_id')
            ->where('product_id', $product_id);
        $res = $data->paginate($request['size'] ?? 10);
        foreach ($res as $key => $item) {
            $item->images = unserialize($item->images);
        }
        return response()->json($res);
    }
}
