<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $productName = $request->get('productName');
        // $arrProductName      = (array)$request->get('productName');
        $data = DB::table('products');
        // ->whereBetween('created_at', [$request['startDate'], $request['endDate']]);
        // ->whereBetween('created_at', '2020-01-01', '2020-01-31');
        // if($productName) {
        //     $data = $data->where('name','LIKE','%'.$productName.'%');
        // }
        $response = $data->paginate($request['size'] ?? 10)->toArray();
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $user = new Product;

        $name = (!empty($data['name'])) ? $data['name'] : '';
        $code = (!empty($data['code'])) ? $data['code'] : '';
        $metatitle = (!empty($data['metatitle'])) ? $data['metatitle'] : '';
        $description = (!empty($data['description'])) ? $data['description'] : '';
        $image = (!empty($data['image'])) ? $data['image'] : '';
        $promotion = (!empty($data['promotion'])) ? $data['promotion'] : '';
        $includedvat = (!empty($data['includedvat'])) ? $data['includedvat'] : '';
        $price = (!empty($data['price'])) ? $data['price'] : '';
        $quantity = (!empty($data['quantity'])) ? $data['quantity'] : '';
        $categoryid = (!empty($data['categoryid'])) ? $data['categoryid'] : '';
        $detail = (!empty($data['detail'])) ? $data['detail'] : '';
        $viewcount = (!empty($data['viewcount'])) ? $data['viewcount'] : '';
        $material = (!empty($data['material'])) ? $data['material'] : '';
        $color = (!empty($data['color'])) ? $data['color'] : '';
        $size = (!empty($data['size'])) ? $data['size'] : '';

        $user->name = $name;
        $user->code = $code;
        $user->metatitle = $metatitle;
        $user->description = $description;
        $user->image = $image;
        $user->promotion = $promotion;
        $user->includedvat = $includedvat;
        $user->price = $price;
        $user->quantity = $quantity;
        $user->categoryid = $categoryid;
        $user->detail = $detail;
        $user->viewcount = $viewcount;
        $user->material = $material;
        $user->color = $color;
        $user->size = $size;
        $user->status = $data['status'];
        $user->save();
    }
}
