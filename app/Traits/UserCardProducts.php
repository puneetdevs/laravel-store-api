<?php

namespace App\Traits;

use App\Models\Cart;
use App\Models\Product;

trait UserCardProducts
{
    public function getCartData($user_id)
    {
        $cartProducts = $response = [];
        $is_exist = Cart::where([['user_id', '=', $user_id]])->count();
        if ($is_exist) {
            $cartProducts = Cart::with(
                array(
                    'product',
                )
            )
                ->where('user_id', '=', $user_id)->get();

            $total_price = 0;
            $total_shipping_cost = 0;
            foreach ($cartProducts as $cartProduct) {
                $shipping_cost = $cartProduct->product->shipping_cost;
                $gross_price = $cartProduct->product->gross_price;
                $quantity = $cartProduct->quantity;
                $total_gross_price = $gross_price * $quantity;
                $data['shipping_cost'] = $total_shipping_cost + $shipping_cost;
                $data['total_price'] = $total_price + $total_gross_price;
                $data['cartItem'] = $cartProduct;
                $response[] = $data;
            }

        }

        return $response;
    }
}
