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

    public function show(Request $request)
    {
        /** @var Cart $user */

        $user_id = Auth::id();

        $isUserExist = User::where('id', '=', $user_id)->count();
        if (!$isUserExist) {
            return $this->sendError('user does not xist', 404);
        } else {
            $user = User::where('id', '=', $user_id)->first();
        }

        $cartCount = Cart::where('user_id', '=', $user_id)->count();
        if (!$cartCount) {
            return $this->sendError('User do not have added product in cart', 404);
        }

        $data = $user->getCartData($user_id);

        return $this->sendResponse($data, 'User Cart data retrieved successfully');
    }

}
