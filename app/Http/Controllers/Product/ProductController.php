<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sex = $request->get('sex');
        $product_id = $request->get('product');
        $category = $request->get('category');
        $branch_id = $request->get('branch');
        $price_from = $request->get('price_from') ?? 0;
        $price_to = $request->get('price_to') ?? 100000000;
        $data = DB::table('products')->whereBetween('product_price', [$price_from, $price_to]);
        if ($sex) {
            $data = $data->where('sex', $request['sex']);
        }
        if ($product_id) {
            $data = $data->whereIn('product_id', $product_id);
        }
        if ($branch_id) {
            $data = $data->whereIn('product_code', $branch_id);
        }
        if ($category) {
            $data = $data->join('categories', 'categories_id', 'product_category_id')->whereIn('categories.categories_parentid', $category);
        }


        $response = $data->paginate($request['size'] ?? 10);
        foreach ($response as $key => $item) {
            $item->product_more_image = unserialize($item->product_more_image);
        }
        return response()->json($response);
    }
    public function listProductAdmin(Request $request)
    {
        $sex = $request->get('sex');
        $product_id = $request->get('product');
        $category = $request->get('category');
        $branch_id = $request->get('branch');
        $price_from = $request->get('price_from') ?? 0;
        $price_to = $request->get('price_to') ?? 100000000;
        $data = DB::table('products')->whereBetween('product_price', [$price_from, $price_to]);
        if ($sex) {
            $data = $data->where('sex', $request['sex']);
        }
        if ($product_id) {
            $data = $data->whereIn('product_id', $product_id);
        }
        if ($branch_id) {
            $data = $data->whereIn('product_code', $branch_id);
        }
        if ($category) {
            $data = $data->join('categories', 'categories_id', 'product_category_id')->whereIn('categories.categories_parentid', $category);
        }
        $data = $data->get();
        foreach ($data as &$item) {
            $dataChild = DB::table('product_sizes')
                ->select(
                    'product_sizes.size_id as id',
                    'color',
                    'size_name as size_name',
                    'product_count as product_count',
                    'product_sizes.created_at as created_at',
                    'products.product_id as product_id'
                )->join('products', 'products.product_id', '=', 'product_sizes.product_id')->where('product_sizes.product_id', $item->product_id)->get();
            $item->product = $dataChild;
        }
        // $response = array_slice($data->toArray(), $request['page'] ?? 0, $request['limit'] ?? 10);
        $response = $data->toArray();
        foreach ($response as $key => $item) {
            $item->product_more_image = unserialize($item->product_more_image);
        }
        return response()->json($response);
    }
    public function slide()
    {
        $data = DB::table('products')
            ->select('product_id', 'product_name', 'product_description', 'product_image', 'product_price', 'product_status', 'product_rate')
            ->where('product_status', 1)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
        $arrReturn['data'] = $data;
        return response()->json($arrReturn);
    }
    public function featureProduct()
    {
        $data = DB::table('products')
            ->select('product_id', 'product_name', 'product_description', 'product_image', 'product_price', 'product_status', 'product_viewcount', 'product_rate')
            ->where('product_status', 1)
            ->orderByDesc('product_viewcount')
            ->limit(8)
            ->get();
        $arrReturn['data'] = $data;
        return response()->json($arrReturn);
    }
    public function hotProduct()
    {
        $data = DB::table('products')
            ->select('product_id', 'product_name', 'product_description', 'product_image', 'product_price', 'product_status', 'product_viewcount', 'product_rate')
            ->where('product_status', 1)
            ->orderByDesc('product_rate')
            ->limit(5)
            ->get();
        $arrReturn['data'] = $data;
        return response()->json($arrReturn);
    }
    public function recentProduct()
    {
        $data = DB::table('products')
            ->select('product_id', 'product_name', 'product_description', 'product_image', 'product_price', 'product_status', 'product_viewcount', 'product_rate')
            ->where('product_status', 1)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
        $arrReturn['data'] = $data;
        return response()->json($arrReturn);
    }
    public function sameProduct(Request $request)
    {
        $id = $request->get('id');
        $category_id = DB::table('products')->select('product_category_id')->where('product_id', $id)->get();
        $data = DB::table('products')
            ->where('product_category_id', $category_id[0]->product_category_id)
            ->where('product_id', '!=', $id)
            ->orderByDesc('created_at')
            ->limit(8)
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
        $product = new Product;

        $size_id = (!empty($data['size_id'])) ? $data['size_id'] : 0;
        $product_name = (!empty($data['product_name'])) ? $data['product_name'] : '';
        $product_code = (!empty($data['product_code'])) ? $data['product_code'] : '';
        $product_metatitle = (!empty($data['product_metatitle'])) ? $data['product_metatitle'] : '';
        $product_description = (!empty($data['product_description'])) ? $data['product_description'] : '';
        $product_more_image = (!empty($data['product_more_image'])) ? $data['product_more_image'] : '';
        $product_image = (!empty($data['product_image'])) ? $data['product_image'] : '';
        $product_promotion = (!empty($data['product_promotion'])) ? $data['product_promotion'] : '';
        $product_includedvat = (!empty($data['product_includedvat'])) ? $data['product_includedvat'] : 1;
        $product_price = (!empty($data['product_price'])) ? $data['product_price'] : '';
        $product_quantity = (!empty($data['product_quantity'])) ? $data['product_quantity'] : '';
        $product_category_id = (!empty($data['product_category_id'])) ? $data['product_category_id'] : '';
        $product_detail = (!empty($data['product_detail'])) ? $data['product_detail'] : 'NULL';
        $product_status = (!empty($data['product_status'])) ? $data['product_status'] : 1;
        $product_viewcount = (!empty($data['product_viewcount'])) ? $data['product_viewcount'] : 0;
        $product_rate = (!empty($data['product_rate'])) ? $data['product_rate'] : 5;
        $product_material = (!empty($data['product_material'])) ? $data['product_material'] : 0;
        $product_size = (!empty($data['product_size'])) ? $data['product_size'] : 0;
        $product_sex = (!empty($data['product_sex'])) ? $data['product_sex'] : 0;

        $product->size_id = $size_id;
        $product->product_name = $product_name;
        $product->product_code = $product_code;
        $product->product_metatitle = $product_metatitle;
        $product->product_description = $product_description;
        $product->product_more_image = serialize(array($product_more_image));
        $product->product_image = $product_image;
        $product->product_promotion = $product_promotion;
        $product->product_includedvat = $product_includedvat;
        $product->product_price = $product_price;
        $product->product_quantity = $product_quantity;
        $product->product_category_id = $product_category_id;
        $product->product_detail = $product_detail;
        $product->product_status = $product_status;
        $product->product_viewcount = $product_viewcount;
        $product->product_rate = $product_rate;
        $product->product_material = $product_material;
        $product->product_size = $product_size;
        $product->sex = $product_sex;
        $product->save();
    }
    public function update(Request $request)
    {
        $data = $request->all();

        $query = DB::table('products')
            ->select('product_quantity')
            ->where('product_id', '=', $data['product_id'])->get();

        $size_id = (!empty($data['size_id'])) ? $data['size_id'] : 0;
        $product_name = (!empty($data['product_name'])) ? $data['product_name'] : '';
        $product_code = (!empty($data['product_code'])) ? $data['product_code'] : '';
        $product_metatitle = (!empty($data['product_metatitle'])) ? $data['product_metatitle'] : '';
        $product_description = (!empty($data['product_description'])) ? $data['product_description'] : '';
        $product_more_image = (!empty($data['product_more_image'])) ? $data['product_more_image'] : '';
        $product_image = (!empty($data['product_image'])) ? $data['product_image'] : '';
        $product_promotion = (!empty($data['product_promotion'])) ? $data['product_promotion'] : '';
        $product_includedvat = (!empty($data['product_includedvat'])) ? $data['product_includedvat'] : 1;
        $product_price = (!empty($data['product_price'])) ? $data['product_price'] : '';
        $product_quantity = (!empty($data['product_quantity'])) ? $query[0]->product_quantity + $data['product_quantity'] : $query[0]->product_quantity;
        $product_category_id = (!empty($data['product_category_id'])) ? $data['product_category_id'] : '';
        $product_detail = (!empty($data['product_detail'])) ? $data['product_detail'] : 'NULL';
        $product_status = (!empty($data['product_status'])) ? $data['product_status'] : 1;
        $product_viewcount = (!empty($data['product_viewcount'])) ? $data['product_viewcount'] : 0;
        $product_rate = (!empty($data['product_rate'])) ? $data['product_rate'] : 5;
        $product_material = (!empty($data['product_material'])) ? $data['product_material'] : 0;
        $product_size = (!empty($data['product_size'])) ? $data['product_size'] : 0;
        $product_sex = (!empty($data['product_sex'])) ? $data['product_sex'] : 0;

        DB::table('products')
            ->where('product_id', '=', $data['product_id'])
            ->update([
                'size_id'  => $size_id,
                'product_name'  => $product_name,
                'product_code'     => $product_code,
                'product_metatitle'   => $product_metatitle,
                'product_description'  => $product_description,
                'product_more_image'  => serialize(array($product_more_image)),
                'product_image'     => $product_image,
                'product_promotion'   => $product_promotion,
                'product_includedvat'  => $product_includedvat,
                'product_price'  => $product_price,
                'product_quantity'     => $product_quantity,
                'product_category_id'   => $product_category_id,
                'product_detail'  => $product_detail,
                'product_status'  => $product_status,
                'product_viewcount'     => $product_viewcount,
                'product_rate'   => $product_rate,
                'product_material'  => $product_material,
                'product_size'     => $product_size,
                'sex'   => $product_sex,
            ]);
        $res = Product::where('product_id', '=', $request['product_id'])->get();

        return response()->json($res);
    }
    public function listProductName()
    {
        $data = DB::table('products')
            ->select('product_id as id', 'product_name as name')->get();
        $response = [
            'data' => $data
        ];
        return response()->json($response);
    }
    public function listProductNameFilter()
    {
        $data = DB::table('products')
            ->select('product_id as id', 'product_name as name')->get();
        $response = [
            'data' => $data
        ];
        return response()->json($response);
    }
    public function searchProduct(Request $request)
    {
        $name = $request->get('name');
        if ($name) {
            $data = DB::table('products')
                ->selectRaw('product_id as id, product_name as label');
            $data = $data->where('product_name', 'LIKE', '%' . $name . '%');
            $data = $data->paginate(10);
            return response()->json($data);
        } else {
            $arrReturn['data'] = [];
            return response()->json($arrReturn);
        }
    }
    public function searchProductClient(Request $request)
    {
        $name = $request->get('name');
        if ($name) {
            $data = DB::table('products')
                ->selectRaw('product_id as value, product_name as label');
            $data = $data->where('product_name', 'LIKE', '%' . $name . '%');
            $data = $data->paginate(10);
            return response()->json($data);
        } else {
            $arrReturn['data'] = [];
            return response()->json($arrReturn);
        }
    }
    public function productDetail(Request $request)
    {
        $id = $request->get('id');
        $data = DB::table('products')
            ->selectRaw('products.product_id, size_id, product_name, product_code, product_metatitle, product_description, product_more_image, product_image,
        product_promotion, product_includedvat, product_price, product_quantity, product_category_id, product_detail, product_status, product_viewcount,
        product_rate, product_material, product_size, products.created_at, sex, promotion_details.discount')
            ->join('promotion_details', 'promotion_details.product_id', 'products.product_id')
            // ->where('promotion_details.status', 1)
            ->where('promotion_details.product_id', $id)
            ->where('products.product_id', $id)
            ->orderByDesc('promotion_details.created_at')->first();
        $res = new Product();
        $res->product_id = $data->product_id;
        $res->size_id = $data->size_id;
        $res->product_name = $data->product_name;
        $res->product_code = $data->product_code;
        $res->product_metatitle = $data->product_metatitle;
        $res->product_description = $data->product_description;
        foreach (unserialize($data->product_more_image) as $index => $itemC) {
            $res->product_more_image = $itemC;
        }
        $res->product_image = $data->product_image;
        $res->product_promotion = $data->product_promotion;
        $res->product_includedvat = $data->product_includedvat;
        $res->product_price = $data->product_price;
        $res->product_quantity = $data->product_quantity;
        $res->product_category_id = $data->product_category_id;
        $res->product_detail = $data->product_detail;
        $res->product_status = $data->product_status;
        $res->product_viewcount = $data->product_viewcount;
        $res->product_rate = $data->product_rate;
        $res->product_material = $data->product_material;
        $res->product_size = $data->product_size;
        $res->created_at = $data->created_at;
        $res->sex = $data->sex;
        $res->discount = $data->discount;
        $dataSize = DB::table('product_sizes')
            ->select(
                // 'product_sizes.size_id as value',
                'size_name as label',
                'size_name as value',
            )->join('products', 'products.product_id', '=', 'product_sizes.product_id')->where('product_sizes.product_id', $id)->groupBy('size_name')->get();

        $res->size = $dataSize;

        $dataColor = DB::table('product_sizes')
            ->select(
                // 'product_sizes.size_id',
                'color as label',
                'color as value',
            )->join('products', 'products.product_id', '=', 'product_sizes.product_id')->where('product_sizes.product_id', $id)->groupBy('color')->get();
        $res->color = $dataColor;
        $dataCount = DB::table('product_sizes')
            ->select(
                'product_sizes.size_id as id',
                'product_count as product_count',
            )->join('products', 'products.product_id', '=', 'product_sizes.product_id')->where('product_sizes.product_id', $id)->get();
        $res->count = $dataCount;
        return $res;
    }
    public function findColor(Request $request)
    {
        $size_name = $request->get('size_name') ? $request->get('size_name') : '';
        $product_id = $request->get('product_id') ? $request->get('product_id') : '';
        $data = DB::table('product_sizes')
            ->select('color as value', 'color as label')
            ->where('product_id', $product_id)
            ->where('size_name', $size_name)
            ->get();
        $res['data'] = $data;
        return response()->json($res);
    }
    public function delete(Request $request)
    {
        $product = new Product;
        if ($request['product_id'] > 0) {
            $data = $product->where('product_id', '=', $request['product_id'])->delete();
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                // 'data' => $data
            ]);
            return response()->json($response, $response['code']);
        } else {
            $error = [
                "status"    => "error",
                "message"   => "Mã thương hiệu không tồn tại",
                "errorCode" => null,
            ];
        }
    }
    //Api tạo mới số lượng sản phẩm
    public function createChild(Request $request)
    {
        $data = $request->all();
        $product = new ProductSize();

        $product_id = (!empty($data['product_id'])) ? $data['product_id'] : 0;
        $color = (!empty($data['color'])) ? $data['color'] : '';
        $size_name = (!empty($data['size_name'])) ? $data['size_name'] : '';
        $product_count = (!empty($data['product_count'])) ? $data['product_count'] : '';

        $query = ProductSize::where('product_id', $product_id)->where('color', $color)->where('size_name', $size_name)->first();
        if (is_null($query)) {
            $product->product_id = $product_id;
            $product->color = $color;
            $product->size_name = $size_name;
            $product->product_count = $product_count;
            $product->save();
        } else {
            $message = "Sản phẩm đã tồn tại";
            $result['message'] = $message;
            return response()->json($result, 422);
        }
    }
    //Api cập nhật số lượng sản phẩm
    public function updateChild(Request $request)
    {
        $data = $request->all();

        $query = DB::table('product_sizes')
            ->select('product_count')
            ->where('size_id', '=', $data['id'])
            ->get();

        $color = (!empty($data['color'])) ? $data['color'] : '';
        $size_name = (!empty($data['size_name'])) ? $data['size_name'] : '';
        $product_count = (!empty($data['product_count'])) ? $query[0]->product_count + $data['product_count'] : $query[0]->product_count;

        DB::table('product_sizes')
            ->where('size_id', '=', $data['id'])
            ->update([
                'color'  => $color,
                'size_name'  => $size_name,
                'product_count'     => $product_count,
                'updated_at' => date("Y-m-d h:m:s")
            ]);
        $res = ProductSize::where('size_id', '=', $request['id'])->get();

        return response()->json($res);
    }
    //Api xóa số lượng sản phẩm
    public function deleteChild(Request $request)
    {
        $product = new ProductSize;
        if ($request['id'] > 0) {
            $data = $product->where('size_id', '=', $request['id'])->delete();
            $response = array_merge([
                'code'   => 200,
                'status' => 'success',
                // 'data' => $data
            ]);
            return response()->json($response, $response['code']);
        } else {
            $error = [
                "status"    => "error",
                "message"   => "Mã thương hiệu không tồn tại",
                "errorCode" => null,
            ];
        }
    }
}
