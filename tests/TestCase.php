<?php

namespace Tests;

use App\Http\Resources\ProductResource;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public  function create(string $model, array $attributes = []){
          $product= factory("App\$model")->create($attributes);

          return new ProductResource($product);
    }
}
