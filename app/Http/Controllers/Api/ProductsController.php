<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductsController extends Controller
{

    public function index(){
        return ProductResource::collection(Product::paginate());

    }
    public function store(Request $request){

        $product= Product::create([
            'name'=>$request->name,
            'slug'=>Str::slug($request->name),
            'price'=>$request->price
        ]);
        return response()->json(new ProductResource($product),201);
    }

    public  function show($product){
        $product= Product::findorfail($product);
        return response()->json(new ProductResource($product));

    }

    public  function update(Request $request,$product){
        $product= Product::findorfail($product);
        $product->update($request->all());
        return response()->json(new ProductResource($product));

    }

    public  function destroy($product){
        $product= Product::findorfail($product);
        $product->delete();
        return response()->json(null,204);

    }

    public function list(){
        return response()->json(["data"=>Product::all()],200);
    }

    public function trash(){
        Product::all()->each(function($product){
            $product->delete();
        });

        return response()->json(["message"=>"Products trashed"],200);
    }

    public function getTrash(){

        $trashed =  Product::onlyTrashed()->get();
        return response()->json([
            'trashedGist' =>$trashed
        ],200);
    }

    public function trashSingle(Product $product){
        $product->delete();
        return response()->json(["message"=>"Product trashed"],200);

    }


}
