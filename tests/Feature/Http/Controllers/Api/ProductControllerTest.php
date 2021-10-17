<?php

namespace Tests\Feature\Http\Controllers\Api;
use App\Models\User;
use Tests\TestCase;
use Carbon\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;





class ProductControllerTest extends TestCase
{

    use RefreshDatabase;

    /** @test */

    public function non_auth_user_cannot_access_below_endpoints(){
       $index= $this->json('GET','api/products');
       $index->assertStatus(401);

        $store= $this->json('POST','api/products');
        $store->assertStatus(401);

        $show= $this->json('GET','api/products/-1');
        $show->assertStatus(401);

        $update= $this->json('PATCH','api/products/-1');
        $index->assertStatus(401);

        $destroy= $this->json('GET','api/products/-1');
        $destroy->assertStatus(401);
    }


    /** @test */

    public function can_return_collection_of_paginated_products(){

        $product= \App\Models\Product::factory(3)->create();
        $response= $this->actingAs(User::factory()->create(),'api')->json('GET','api/products');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'=>[
                    '*'=>['id','name','slug','price','created_at']
                ],
                'links'=>['first','last','prev','next'],
                'meta'=>['current_page','last_page','from','to','path','per_page','total']
            ]);

       // \Log::info($response->getContent());

    }

    /** @test  */
    public function can_create_a_product()
    {
        $faker=\Faker\Factory::create();
        $response = $this->actingAs(User::factory()->create(),'api')->json('POST','/api/products',[
            'name'=>$name= $faker->company,
            'slug' => Str::slug($name),
            'price'=>$price = random_int(10,100)
        ]);

        $response
            ->assertJsonStructure(['id','name','slug','price','created_at'])
            ->assertJson([
                'name'=>$name,
                'slug'=>Str::slug($name),
                'price'=>$price
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('products',[
            'name'=>$name,
            'slug'=>Str::slug($name),
            'price'=>$price
        ]);

    }

    /** @test  */

    public  function will_fail_with_a_404_if_product_to_be_viewed_not_found(){
        $response=$this->actingAs(User::factory()->create(),'api')->json('GET','api/products/-4');
        $response->assertStatus(404);
    }

    /** @test  */
    public  function can_return_a_product(){
        $product= \App\Models\Product::factory()->create();
        $response = $this->actingAs(User::factory()->create(),'api')->json('GET',"api/products/$product->id");
        $response->assertStatus(200)
        ->assertExactJson([
            'id'=>$product->id,
            'name'=>$product->name,
            'slug'=>$product->slug,
            'price'=>$product->price,
            'created_at'=>(string)$product->created_at,
        ]);
    }

    /** @test  */
    public function  will_fail_if_product_to_be_updated_not_found(){
        $response=$this->actingAs(User::factory()->create(),'api')->json('PATCH','api/products/-4');
        $response->assertStatus(404);
    }

    /** @test */
    public function can_update_a_product(){
        $product= \App\Models\Product::factory()->create();
        $response=$this->actingAs(User::factory()->create(),'api')->json('PATCH',"api/products/$product->id",[
            'name'=>$product->name . "_updated",
            'slug'=>Str::slug($product->name. '_updated'),
            'price'=>$product->price + 10
        ]);

        $response->assertStatus(200)
        ->assertExactJson([
            'id'=>$product->id,
            'name'=>$product->name ."_updated",
            'slug'=>Str::slug($product->name. '_updated'),
            'price'=>$product->price + 10,
            'created_at'=>(string)$product->created_at,
        ]);

         $this->assertDatabaseHas('products',[
             'id'=>$product->id,
             'name'=>$product->name ."_updated",
             'slug'=>Str::slug($product->name. '_updated'),
             'price'=>$product->price + 10,
             'created_at'=>(string)$product->created_at,
         ]);



    }

    /** @test */
    public function will_fail_if_product_to_be_deleted_is_not_found(){
        $response=$this->actingAs(User::factory()->create(),'api')->json('DELETE','api/products/-4');
        $response->assertStatus(404);
    }

    /** @test */
    public function delete_a_product(){
        $product= \App\Models\Product::factory()->create();
        $response=$this->actingAs(User::factory()->create(),'api')->json('DELETE',"api/products/$product->id");
        $response->assertStatus(204)
        ->assertSee(null);

        $this->assertDatabaseMissing('products',['id'=>$product->id]);


    }


}

