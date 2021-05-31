<?php

namespace App\Http\Controllers\Order;

use App\Export\OrderExport;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSize;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class OrderController extends Controller
{
    //Order status
    //  1: Đang giao
    //  2: Đã nhận
    //  3: Đã hủy
    //  4: Người gửi gửi hàng
    //Order type
    //  1: Đang trong giỏ hàng
    //  2: Đơn đã đặt
    public function getAdminOrder(Request $request)
    {
        $data = DB::table('orders')
            ->selectRaw('shipplace.address, users.phone, users.name, orders.orders_id, orders_status, product_cost, orders_quantity, orders.product_price, products.product_name, products.product_image, orders.created_at, product_sizes.color, product_sizes.size_name')
            ->join('product_sizes', 'size_id', 'product_size_id')
            ->join('shipplace', 'id', 'shipplace_id')
            ->join('products', 'products.product_id', 'product_sizes.product_id')
            ->join('users', 'users.id', 'orders.user_id')
            // ->where('orders_type', 2)
            ->whereBetween('orders.created_at', [$request['startDate'] . " 00:00:00", $request['endDate'] . " 23:59:59"]);
        if ($request->get('product_id')) {
            $data = $data->where('product_sizes.product_id', $request->get('product_id'));
        }
        if ($request->get('category')) {
            $data = $data->where('products.product_category_id', $request->get('category'));
        }

        $res = $data->orderByDesc('orders_id')->paginate($request['size'] ?? 10);
        return response()->json($res);
    }

    public function exportAdminOrder(Request $request)
    {
        $data = DB::table('orders')
            ->selectRaw('shipplace.address, users.phone, users.name, orders.orders_id, orders_status, product_cost, orders_quantity, orders.product_price, products.product_name, products.product_image, orders.created_at, product_sizes.color, product_sizes.size_name')
            ->join('product_sizes', 'size_id', 'product_size_id')
            ->join('shipplace', 'id', 'shipplace_id')
            ->join('products', 'products.product_id', 'product_sizes.product_id')
            ->join('users', 'users.id', 'orders.user_id')
            // ->where('orders_type', 2)
            ->whereBetween('orders.created_at', [$request['startDate'] . " 00:00:00", $request['endDate'] . " 23:59:59"])
            ->get();
        if ($request->download == 1) {
            return (new OrderExport($data))->download(sprintf('Received orders from [%s] to [%s].xlsx', $request['startDate'], $request['endDate']));
        }
        return response()->json($data);
    }

    public function getOrder(Request $request)
    {
        $id = $request->get('id');
        $data = DB::table('orders')
            ->selectRaw('product_sizes.size_id, products.product_id, orders.orders_id, orders_status, orders.orders_type, orders_quantity, orders.product_price, products.product_name, products.product_image, orders.created_at, product_sizes.color, product_sizes.size_name')
            ->join('product_sizes', 'size_id', 'product_size_id')
            ->join('shipplace', 'id', 'shipplace_id')
            ->join('products', 'products.product_id', 'product_sizes.product_id')
            ->where('orders.user_id', $id)
            // ->where('orders_type', 2)
            ->orderByDesc('orders.created_at');
        $res = $data->paginate($request['size'] ?? 10);
        return response()->json($res);
    }
    public function addProduct(Request $request)
    {
        $shipplace_id = $request->get('shipplace_id');
        $orders_type = $request->get('orders_type');
        $product_cost = $request->get('product_cost');
        $product_price = $request->get('product_price');
        $orders_status = $request->get('orders_status');
        $cart_id = $request->get('cart_id');
        $product_size_id = $request->get('product_size_id');
        $orders_quantity = $request->get('orders_quantity');
        $user_id = $request->get('user_id');
        $arrCart = [];
        for ($i = 0; $i < count($cart_id); $i++) {
            $cart = Cart::selectRaw('id, quantity')->where('id', $cart_id[$i])->first();
            array_push($arrCart, $cart->quantity);
            DB::table('carts')
                ->where('id', '=', $cart_id[$i])
                ->update([
                    'quantity'     => $orders_quantity[$i]
                ]);
            $search = DB::table('product_sizes')
                ->select('size_id', 'product_count')
                ->where('size_id', $product_size_id[$i])
                ->first();
            if ($search->product_count < $orders_quantity[$i]) {
                $orders_quantity = "";
                $message = "Số lượng sản phẩm còn lại không đủ";
            }
            $this->minusOrder($search, $orders_quantity[$i], $cart->quantity);
        }

        for ($i = 0; $i < count($product_cost); $i++) {
            $orders = new Order();
            $orders->shipplace_id           = $shipplace_id;
            $orders->product_cost           = $product_cost[$i];
            $orders->product_price          = $product_price[$i];
            $orders->orders_status          = $orders_status;
            $orders->product_size_id        = $product_size_id[$i];
            $orders->orders_quantity        = $orders_quantity[$i];
            $orders->user_id                = $user_id;
            $orders->orders_type            = $orders_type;
            $orders->save();
        }
        $rows = Cart::where('user_id', $user_id)->get();
        foreach ($rows as $row) {
            $row->status = 2;
            $row->save();
        }
    }
    public function minusOrder($search, $quantity, $cart_quantity)
    {
        $search->product_count = $search->product_count - $quantity + $cart_quantity;

        DB::table('product_sizes')
            ->where('size_id', '=', $search->size_id)
            ->update([
                'product_count'          => $search->product_count,
            ]);
    }
    public function delete(Request $request)
    {
        $orders = new Order();

        if ($request['id'] > 0) {
            $data = $orders->where('orders_id', '=', $request['id'])->delete();
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                // 'data' => $data
            ]);
        } else {
            $response = array_merge([
                "status"    => "error",
                "message"   => "Có lỗi xảy ra",
                "code"      => 400,
            ]);
        }
        return response()->json($response, $response['code']);
    }
    public function updateType(Request $request)
    {
        $data = $request->all();
        $orders_status = (!empty($data['orders_status'])) ? $data['orders_status'] : '';
        $orders_type = (!empty($data['orders_type'])) ? $data['orders_type'] : '';
        DB::table('orders')
            ->where('orders_id', '=', $data['orders_id'])
            ->update([
                'orders_status'     => $orders_status,
                'orders_type'       => $orders_type,
            ]);
        $res = Order::where('orders_id', '=', $request['orders_id'])->get();

        return response()->json($res);
    }
    public function updateStatus(Request $request)
    {
        $data = $request->all();
        $orders_id = $request->get('orders_id');
        if ($request['orders_status'] == 3) {
            $data = DB::table('orders')
                ->select('orders_quantity', 'product_size_id')
                ->where('orders_id', '=', $data['orders_id'])
                ->get();
            $product = DB::table('product_sizes')
                ->where('size_id', '=', $data[0]->product_size_id)->get();
            DB::table('product_sizes')
                ->where('size_id', '=', $data[0]->product_size_id)
                ->update([
                    'product_count'     => $data[0]->orders_quantity + $product[0]->product_count,
                ]);
            DB::table('orders')
                ->where('orders_id', '=', $orders_id)
                ->update([
                    'orders_status'     => $request['orders_status'],
                ]);
        } else {
            DB::table('orders')
                ->where('orders_id', '=', $data['orders_id'])
                ->update([
                    'orders_status'     => $request['orders_status'],
                ]);
        }

        $res = Order::where('orders_id', '=', $request['orders_id'])->get();
        return response()->json($res);
    }
    public function addOrder(Request $request)
    {
        $user_id = $request->get('user_id');
        $quantity = $request->get('quantity');
        $discount = $request->get('discount');
        $color = $request->get('color');
        $product_id = $request->get('product_id');
        $size_name = (int)($request->get('size_name'));
        $search = DB::table('product_sizes')
            ->select('size_id', 'product_count')
            ->where('color', $color)
            ->where('size_name', $size_name)
            ->where('product_id', $product_id)
            ->get();
        if ($search[0]->product_count < $quantity) {
            $quantity = "";
            $message = "Số lượng sản phẩm còn lại không đủ";
        }
        $this->minusProduct($search, $quantity);
        $query = Cart::where('user_id', $user_id)->where('product_id', $search[0]->size_id)->where('status', 1)->where('discount', $discount)->first();
        if ($query === null) {
            if ($quantity > 0) {
                $carts = new Cart();
                $carts->user_id             = $user_id;
                $carts->product_id          = $search[0]->size_id;
                $carts->quantity            = $quantity;
                $carts->discount            = $discount;
                $carts->status              = 1;
                $carts->save();
            } else {
                $result['message'] = $message;
                return response()->json($result, 500);
            }
        } else {
            DB::table('carts')
                ->where('id', '=', $query->id)
                ->update([
                    'quantity'          => $query->quantity + $quantity,
                ]);
        }
    }
    public function minusProduct($search, $quantity)
    {
        foreach ($search as $k => $v) {
            $v->product_count = $v->product_count - $quantity;
        }

        DB::table('product_sizes')
            ->where('size_id', '=', $search[0]->size_id)
            ->update([
                'product_count'          => $search[0]->product_count,
            ]);
    }
    public function listOrder(Request $request)
    {
        $data = DB::table('product_sizes')
            ->selectRaw('product_sizes.size_id, carts.discount, products.product_promotion, products.product_name, products.product_image, products.product_price, carts.quantity, carts.user_id, carts.id')
            ->join('carts', 'carts.product_id', 'product_sizes.size_id')
            ->join('products', 'products.product_id', 'product_sizes.product_id')
            ->where('carts.user_id', $request['user_id'])
            ->where('carts.status', 1)
            ->get();
        $res['data'] = $data;
        return response()->json($res);
    }
    public function deleteCart(Request $request)
    {
        $query = Cart::where('id', $request['id'])->first();
        $product_size = ProductSize::where('size_id', $query->product_id)->first();
        DB::table('product_sizes')
            ->where('size_id', '=', $query->product_id)
            ->update([
                'product_count'          => $query->quantity + $product_size->product_count,
            ]);
        $carts = new Cart();
        if ($request['id'] > 0) {
            $data = $carts->where('id', '=', $request['id'])->delete();
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                // 'data' => $data
            ]);
        } else {
            $response = array_merge([
                "status"    => "error",
                "message"   => "Có lỗi xảy ra",
                "code"      => 400,
            ]);
        }
        return response()->json($response, $response['code']);
    }
    public function report(Request $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        // $fromDate = Carbon::parse($startDate);
        // $toDate = Carbon::parse($endDate);
        // $dates = [];

        // while ($fromDate->lte($toDate)) {
        //     array_push($dates, $fromDate->format('Y-m-d'));
        //     $fromDate->addDay();
        // }
        $dataP = Product::selectRaw('product_id, product_name')->get();
        $dataClone = [];
        foreach ($dataP as $k => $v) {
            $dataClone[$v->product_id]['product_name'] = $v->product_name;
            $dataClone[$v->product_id]['quantity'] = 0;
        }
        $data = ProductSize::selectRaw('product_sizes.product_id, orders.orders_quantity, products.product_name')
            ->join('products', 'products.product_id', 'product_sizes.product_id')
            ->join('orders', 'orders.product_size_id', 'product_sizes.size_id')
            ->where('orders.orders_status', 2)
            ->whereBetween('orders.created_at', [$request['startDate'] . " 00:00:00", $request['endDate'] . " 23:59:59"])
            ->get();
        $dataFinal = [];
        $dataLabel = [];
        $dataValue = [];
        foreach ($data as $k => $v) {
            if (array_key_exists($v->product_id, $dataFinal)) {
                $dataFinal[$v->product_id]['quantity'] += $v->orders_quantity;
                $dataFinal[$v->product_id]['product_name'] = $v->product_name;
            } else {
                $dataFinal[$v->product_id]['quantity'] = $v->orders_quantity;
                $dataFinal[$v->product_id]['product_name'] = $v->product_name;
            }
        }
        $dataSuccess = $dataFinal + $dataClone;
        $color = ["#db4437", "#f4b400", "#0f9d58", "#ff6d00", "#46bdc6", "#ab30c4", "#c1bc1f", "#000000", "#3949ab", "#f975a8", "#4285f4"];
        $value = [];
        $values = [];

        foreach ($dataFinal as $k => $v) {
            array_push($dataLabel, $v['product_name']);
            array_push($value, $v['quantity']);
        }
        if (count($value) > 0) {
            foreach ($value as $k => $v) {
                array_push($values, round($v * 100 / array_sum($value), 2));
            }
            // $dataValue['label'] = '# of Votes';
            $dataValue['data'] = $values;
            $dataValue['backgroundColor'] = $color;
            $dataValue['borderWidth'] = 1;

            $customResponse = [
                'labels'   => $dataLabel,
                'datasets' => [$dataValue]
            ];
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                'data' => $customResponse
            ]);
        } else {
            foreach ($dataLabel as $k => $v) {
                array_push($values, 0);
            }
            $dataValue['data'] = [0];
            $dataValue['backgroundColor'] = $color;
            $dataValue['borderWidth'] = 1;

            $customResponse = [
                'labels'   => ["Không có"],
                'datasets' => [$dataValue]
            ];
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                'data' => $customResponse
            ]);
        }

        return response()->json($response, $response['code']);
    }
    public function reportBar(Request $request)
    {
        $data = ProductSize::selectRaw('product_sizes.product_id, orders.orders_quantity, products.product_name')
            ->join('products', 'products.product_id', 'product_sizes.product_id')
            ->join('orders', 'orders.product_size_id', 'product_sizes.size_id')
            ->where('orders.orders_status', 2)
            ->whereBetween('orders.created_at', [$request['startDate'] . " 00:00:00", $request['endDate'] . " 23:59:59"])
            ->get();
        $dataFinal = [];
        $dataLabel = [];
        $dataValue = [];
        foreach ($data as $k => $v) {
            if (array_key_exists($v->product_id, $dataFinal)) {
                $dataFinal[$v->product_id]['quantity'] += $v->orders_quantity;
                $dataFinal[$v->product_id]['product_name'] = $v->product_name;
            } else {
                $dataFinal[$v->product_id]['quantity'] = $v->orders_quantity;
                $dataFinal[$v->product_id]['product_name'] = $v->product_name;
            }
        }
        $color = ["#db4437", "#f4b400", "#0f9d58", "#ff6d00", "#46bdc6", "#ab30c4", "#c1bc1f", "#000000", "#3949ab", "#f975a8", "#4285f4"];
        $value = [];
        $values = [];

        foreach ($dataFinal as $k => $v) {
            array_push($dataLabel, $v['product_name']);
            array_push($value, $v['quantity']);
        }
        if (count($value) > 0) {
            foreach ($value as $k => $v) {
                array_push($values, $v);
            }
            $dataValue['data'] = $values;
            $dataValue['label'] = $dataLabel;
            $dataValue['backgroundColor'] = $color;
            $dataValue['borderWidth'] = 1;

            $customResponse = [
                'labels'   => $dataLabel,
                'datasets' => [$dataValue]
            ];
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                'data' => $customResponse
            ]);
        } else {
            foreach ($dataLabel as $k => $v) {
                array_push($values, 0);
            }
            $dataValue['data'] = [0];
            $dataValue['backgroundColor'] = $color;
            $dataValue['borderWidth'] = 1;

            $customResponse = [
                'labels'   => ["Không có"],
                'datasets' => [$dataValue]
            ];
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                'data' => $customResponse
            ]);
        }

        return response()->json($response, $response['code']);
    }
    public function countCart(Request $request)
    {
        $user_id = $request->get('user_id');
        $query = Cart::where('user_id', $user_id)->where('status', 1)->get();
        $data['data'] = $query;
        return response()->json($data);
    }
}
