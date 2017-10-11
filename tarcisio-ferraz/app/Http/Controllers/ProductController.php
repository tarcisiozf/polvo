<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller {

    public function selectAll() {
        return response()->json(Product::all());
    }

    public function create(Request $request) {
        
        $product = new Product();
        $product->name = $request->input('name');
        $product->sku = $request->input('sku');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        
        if ( $product->save() ) {
            return response()->json($product);
        } else {
            return response()->json("failed to insert", 500);
        }
    
    }

    public function update($id, Request $request) {
        
        $product = Product::where('id', '=', $id)->first();
    
        if ( $product === null ) {
            return response()->json("product not found", 404);
        }
        
        $product->name = $request->input('name');
        $product->sku = $request->input('sku');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        
        if ( $product->save() ) {
            return response()->json($product);
        } else {
            return response()->json("failed to insert", 500);
        }
    
    }

    public function delete($id, Request $request) {
        
        $product = Product::where('id', '=', $id)->first();
    
        if ( $product === null ) {
            return response()->json("product not found", 404);
        }
        
        if ( $product->delete() ) {
            return response()->json("deleted");
        } else {
            return response()->json("failed to insert", 500);
        }
    
    }
}