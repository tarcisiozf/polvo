<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderController extends Controller {

    public function selectAll() {

        $orders = Order::all();

        foreach($orders as $order) {
            
            $order->products = [];
            
            $items = OrderProduct::where('id_order', '=', $order->id)->get();
            
            foreach($items as $item) {

                $product = Product::where('id', '=', $item->id_product)->first();
                $product->amount = $item->amount;

                // $order->products[] = $product;
            }

        }

        return response()->json($orders);
    }

    public function create(Request $request) {
        
        $order = new Order();
        
        if ( ! $order->save() ) {
            return response()->json("failed to insert", 500);
        }

        $order->products = [];

        foreach($request->input('products') as $item) {
            
            $orderProduct = new OrderProduct();
            $orderProduct->id_order = $order->id;
            $orderProduct->id_product = $item['id_product'];
            $orderProduct->amount = $item['amount'];
            if ( ! $orderProduct->save() ) {
                return response()->json("failed to insert product", 500);
            }
            
            $order->products[] = $orderProduct;
        }
        
        return response()->json($order);
    }

    public function update($id, Request $request) {
        
        $order = Order::where('id', '=', $id)->first();
        
        if ( $order === null ) {
            return response()->json("order not found", 404);
        }
    
        OrderProduct::where('id_order', '=', $order->id)->delete();
        
        foreach($request->input('products') as $item) {
            
            $orderProduct = new OrderProduct();
            $orderProduct->id_order = $order->id;
            $orderProduct->id_product = $item['id_product'];
            $orderProduct->amount = $item['amount'];
            if ( ! $orderProduct->save() ) {
                return response()->json("failed to insert product", 500);
            }
            
        }
        
        return response()->json($order);
    }

    public function delete($id, Request $request) {
        
        $order = Order::where('id', '=', $id)->first();
    
        if ( $order === null ) {
            return response()->json("order not found", 404);
        }

        OrderProduct::where('id_order', '=', $order->id)->delete();
        
        if ( $order->delete() ) {
            return response()->json("deleted");
        } else {
            return response()->json("failed to insert", 500);
        }
    
    }
}