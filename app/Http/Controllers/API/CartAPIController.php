<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartAPIController extends AppBaseController
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 400);
        }

        $validated = $validator->validated();

        $validated['user_id'] = Auth::id();

        $isProductExist = Product::where('id', '=', $validated['product_id'])->count();

        if (!$isProductExist) {
            return $this->sendError('Product does not exist for this id');
        }

        $isProductExistInCart = Cart::where([['product_id', '=', $validated['product_id']], ['user_id', '=', $validated['user_id']]])->count();

        if ($isProductExistInCart) {
            $prevItem = Cart::where([['product_id', '=', $validated['product_id']], ['user_id', '=', $validated['user_id']]])->first();
            $validated['quantity'] = $prevItem->quantity + 1;
            $prevItem->update($validated);
            $cart = $prevItem = Cart::where([['product_id', '=', $validated['product_id']], ['user_id', '=', $validated['user_id']]])->first();
        } else {
            $validated['quantity'] = 1;
            $cart = cart::create($validated);
        }

        if (!$cart) {
            return $this->sendError('cart does not created');
        }

        return $this->sendResponse($cart, 'Product Added to cart successfully');
    }
}
