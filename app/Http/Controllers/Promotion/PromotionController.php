<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $data = Promotion::with(['promotionDetail', 'promotionDetail.promotionProduct'])
            // ->with('promotionProduct')
            ->whereBetween('created_at', [$startDate . " 00:00:00", $endDate . " 23:59:59"])
            ->orderByDesc('created_at')
            ->paginate($request->get('size') ?? 10);
        return response()->json($data);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $product_id = (!empty($data['product_id'])) ? $data['product_id'] : [];
        $discount = (!empty($data['discount'])) ? $data['discount'] : '';
        $type = (!empty($data['type'])) ? $data['type'] : 1;
        $body = (!empty($data['body'])) ? $data['body'] : '';
        $status = (!empty($data['status'])) ? $data['status'] : 0;

        $promotion = new Promotion();
        $promotion->discount = $discount;
        $promotion->type = $type;
        $promotion->body = $body;
        $promotion->active = $status;
        $promotion->save();

        $res = Promotion::select('id')->orderByDesc('created_at')->limit(1)->get();


        if ($product_id[0] == 0) {
            $product = Product::select('product_id')->get();
            $productArr = [];
            foreach ($product as $k => $v) {
                array_push($productArr, $v->product_id);
            }
            foreach ($productArr as $k => $v) {
                $promotionDetail = new PromotionDetail();
                $promotionDetail->product_id = $v;
                $promotionDetail->discount = $discount;
                $promotionDetail->status = $status;
                $promotionDetail->promotion_id = $res[0]['id'];
                $promotionDetail->save();
            }
        } else {
            foreach ($product_id as $k => $v) {
                $promotionDetail = new PromotionDetail();
                $promotionDetail->product_id = $v;
                $promotionDetail->discount = $discount;
                $promotionDetail->status = $status;
                $promotionDetail->promotion_id = $res[0]['id'];
                $promotionDetail->save();
            }
        }
    }
    public function delete(Request $request)
    {
        $promotion_id = $request->get('promotion_id');
        $promotion = new Promotion();
        if ($promotion) {
            $data = $promotion->where('id', $promotion_id)->delete();
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                // 'data' => $data
            ]);
            $promotionDetail = new PromotionDetail();
            $promotionDetail->where('promotion_id', $promotion_id)->delete();
            return response()->json($response, $response['code']);
        } else {
            $error = [
                "status"    => "error",
                "message"   => "Mã khuyến mại không tồn tại",
                "errorCode" => null,
            ];
        }
    }
    public function changeStatus(Request $request)
    {
        $active = $request->get('active');
        $id = $request->get('id');
        $data = Promotion::where('id', '=', (int)$id)
            ->update([
                'active' => $active
            ]);
        PromotionDetail::where('promotion_id', $id)
            ->update([
                'status' => $active
            ]);
        $res = Promotion::where('id', "=", $id)->get();
        $query = [
            'data' => $res
        ];
        return response()->json($query);
    }
}
