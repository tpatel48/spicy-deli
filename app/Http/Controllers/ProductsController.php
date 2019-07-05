<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\ProductCategorie;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $allProducts = Product::all();
            foreach ($allProducts as $product) {
                $product->categories = $product->getCategories($product->id);
            }
            return $allProducts;
        }catch (\Exception $e){
            return json_encode("Something went wrong. check -> ".$e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    //check if categories exist
    function checkCategories($product_categories){
        $result_flag = true;
        foreach ($product_categories as $cat_id){
            if(!Category::where('id',$cat_id)->exists()){
                $result_flag = false;
                break;
            }
        }
        return $result_flag;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = request()->all();
            $product_name = $input['name'];
            $product_price = $input['price'];
            $product_sku = $input['sku'];
            $product_categories_values =  $input['categories']; //comma seperated

            //check if sku is unique
            $is_sku_unique = Product::checkIfSkuUnique($product_sku);

            if($is_sku_unique){
                //check categories
                $product_categories = explode(',', $product_categories_values);
                foreach ($product_categories as $cat_id){
                    if(Category::find($cat_id)->get()->count() == 0){
                        return json_encode("Category with id -> ".$cat_id." does not exist.");
                    }
                }
                //create product
                $product = new Product();
                $product->name = $product_name;
                $product->sku = $product_sku;
                $product->price = $product_price;
                $product->created_at =  date('Y-m-d H:i:s');
                $product->updated_at =  date('Y-m-d H:i:s');
                $product->save();

                //create product categories
                foreach ($product_categories as $cat_id){
                    $product_category = new ProductCategorie();
                    $product_category->product_id = $product->id;
                    $product_category->category_id = $cat_id;
                    $product_category->save();
                }

                return json_encode("Product created with ID -> ".$product->id.".");

            }else{
                return json_encode("Product with sku -> ".$product_sku." already exist.");
            }
            //delete product category association first
            $affectedRows = ProductCategorie::where('product_id', '=', $product_id)->delete();

            //now delete product
            Product::destroy($product_id);
            return json_encode("Product id -> ".$product_id." deleted.");
        }catch (\Exception $e){
            return json_encode("Something went wrong. check -> ".$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $product = Product::find($id);
            $product->categories = $product->getCategories($product->id);

            return $product;
        }catch (\Exception $e){
            return json_encode("Something went wrong. check -> ".$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        try {
            $input = request()->all();
            $product_id = $input['id'];
            $product_name = $input['name'];
            $product_price = $input['price'];
            $product_sku = $input['sku'];
            $product_categories_values =  $input['categories']; //comma seperated
            $product_categories = explode(',', $product_categories_values);
            //check if product exist
            $product = Product::find($product_id);

            if($product === NULL){
                return json_encode("Product with id -> ".$product_id." does not exist.");
            }

            //check if sku is unique
            $is_sku_unique = Product::checkIfSkuUnique($product_sku,$product_id);

            if($is_sku_unique){
                //check categories
                if(isset($product_categories_values)){
                    $check_categories = $this->checkCategories($product_categories);
                    if($check_categories === false){
                        return json_encode("Categories provided are incorrect");
                    }
                }

                //update product
                if(isset($product_name)){
                    $product->name = $product_name;
                }
                if(isset($product_sku)){
                    $product->sku = $product_sku;
                }
                if(isset($product_price)){
                    $product->price = $product_price;
                }
                $product->updated_at =  date('Y-m-d H:i:s');
                $product->save();

                //create product categories if user sent them in request
                if(isset($check_categories)){
                    //First delete existing categories
                    $affectedRows = ProductCategorie::where('product_id', '=', $product_id)->delete();

                    //Then save new categories
                    foreach ($product_categories as $cat_id){
                        $product_category = new ProductCategorie();
                        $product_category->product_id = $product->id;
                        $product_category->category_id = $cat_id;
                        $product_category->save();
                    }
                }

                return json_encode("Product with ID -> ".$product->id." updated.");

            }else{
                return json_encode("Product with sku -> ".$product_sku." already exist.");
            }
            //delete product category association first
            $affectedRows = ProductCategorie::where('product_id', '=', $product_id)->delete();

            //now delete product
            Product::destroy($product_id);
            return json_encode("Product id -> ".$product_id." deleted.");
        }catch (\Exception $e){
            return json_encode("Something went wrong. check -> ".$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        try {
            $input = request()->all();
            $product_id = $input['product_id'];

            //delete product category association first
            $affectedRows = ProductCategorie::where('product_id', '=', $product_id)->delete();

            //now delete product
            Product::destroy($product_id);
            return json_encode("Product id -> ".$product_id." deleted.");
        }catch (\Exception $e){
            return json_encode("Something went wrong. check -> ".$e->getMessage());
        }
    }
}
