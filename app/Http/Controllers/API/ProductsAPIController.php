<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductsAPIController extends AppBaseController
{
    public function index()
    {
        $productCount = Product::all()->count();
        if (!$productCount) {
            return $this->sendError('Products not found', 404);
        }

        $products = Product::all();

        return $this->sendResponse($products, 'Products retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:50',
            'description' => 'required|max:255',
            'net_price' => 'required|numeric|between:0,900000',
            'vat' => 'nullable|numeric|between:0,90000',
            'shipping_cost' => 'nullable|numeric|between:0,900000',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 400);
        }

        $validated = $validator->validated();

        $validated['user_id'] = Auth::id();

        if (isset($validated['vat']) && !empty($validated['vat'])) {
            $validated['gross_price'] = $validated['net_price'] + ($validated['net_price'] * $validated['vat']) / 100;
        } else {
            $validated['gross_price'] = $validated['net_price'];
        }

        $product = Product::create($validated);

        if (!$product) {
            return $this->sendError('Product does not created');
        }

        return $this->sendResponse($product, 'Product created successfully');
    }

    public function show($id)
    {
        /** @var Product $user */

        $productCount = Product::where('id', '=', $id)->count();

        $product = Product::where('id', '=', $id)->get();

        if (!$productCount) {
            return $this->sendError('Product not found', 404);
        }

        return $this->sendResponse($product, 'Product retrieved successfully');
    }

    public function update($id, Request $request)
    {
        $user_id = Auth::id();
        $productCount = Product::where([['id', '=', $id], ['user_id', '=', $user_id]])->count();

        if (!$productCount) {
            return $this->sendError('Product not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|max:50',
            'description' => 'nullable|max:255',
            'net_price' => 'nullable|numeric|between:0,900000',
            'vat' => 'nullable|numeric|between:0,90000',
            'shipping_cost' => 'nullable|numeric|between:0,900000',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 400);
        }

        $validated = $validator->validated();

        $product = Product::where([['id', '=', $id], ['user_id', '=', $user_id]])->first();

        if ($request->get('net_price')) {
            $product->net_price = $net_price = $request->get('net_price');
            $product->gross_price = $net_price;
            if ($request->get('vat')) {
                $product->net_price = $vat = $request->get('vat');
                $product->gross_price = $net_price + ($net_price * $vat) / 100;
            }
        }
        if ($request->get('title')) {
            $product->title = $request->get('title');
        }
        if ($request->get('description')) {
            $product->description = $request->get('description');
        }
        if ($request->get('shipping_cost')) {
            $product->shipping_cost = $request->get('shipping_cost');
        }

        $product->save();

        if (!$product) {
            return $this->sendError('Product does not updated');
        }

        return $this->sendResponse($product, 'Product updated successfully');
    }

    public function destroy($id)
    {
        /** @var Product $user */
        $product = Product::where('id', '=', $id);

        $productCount = Product::where('id', '=', $id)->count();

        if (!$productCount) {
            return $this->sendError('Product not found', 404);
        }

        $product->delete();

        return $this->sendSuccess('Product deleted successfully');
    }
}
