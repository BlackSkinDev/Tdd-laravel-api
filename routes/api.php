<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::namespace('Api')->group(function (){
    Route::resource('/products','ProductsController',['except'=>'edit','create']);
    Route::get('/v1/products','ProductsController@list');
    Route::get('/v1/products/trash/{product}','ProductsController@trashSingle');
    Route::get('/v1/products/delete','ProductsController@trash');
    Route::get('/v1/products/trashed-products','ProductsController@getTrash');
});
