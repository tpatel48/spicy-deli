<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\ProductCategorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            //fetch product categories
            foreach ($allProducts as $product) {
                $product->categories = $product->getCategories($product->id);
            }
            //return $allProducts;
            return response()->json($allProducts, 200);
        }catch (\Exception $e){
            Log::critical("ProductsController@index: ".$e->getMessage());
            return response()->json("Something went wrong!!!", 400);
        }
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
     * Store a newly created product
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $product_name = isset($input['name']) ? trim($input['name']) : null;
            $product_price = isset($input['price']) ? trim($input['price']) : null;
            $product_sku = isset($input['sku']) ? trim(strtoupper($input['sku'])) : null;
            $product_categories_values =  isset($input['categories']) ? trim($input['categories']) : null; //comma seperated

            //Check if all request parameters are present or not
            if($product_name === null || $product_price === null || $product_sku === null || $product_sku === null || $product_categories_values === null){
                return response()->json("Request parameters are insufficient, please check readme file.", 400);
            }

            //check if price is numeric or not
            if(!is_numeric($product_price)){
                return response()->json("Price format is incorrect.", 400);
            }

            //check if sku is unique
            $is_sku_unique = Product::checkIfSkuUnique($product_sku);

            if($is_sku_unique){
                //check categories
                $product_categories = explode(',', $product_categories_values);
                $check_categories = $this->checkCategories($product_categories);
                if($check_categories === false){
                    return response()->json("Categories provided are incorrect", 400);
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

                return response()->json("Product created with ID -> ".$product->id.".", 200);
            }else{
                return response()->json("Product with sku -> ".$product_sku." already exist.", 400);
            }
        }catch (\Exception $e){
            Log::critical("ProductsController@store: ".$e->getMessage());
            return response()->json("Something went wrong!!!", 400);
        }
    }

    /**
     * Display the specified product
     */
    public function show($id)
    {
        try {
            if(!Product::where('id',$id)->exists()){
                return response()->json("Product not found.", 400);
            }
            $product = Product::find($id);
            $product->categories = $product->getCategories($product->id);

            return $product;
        }catch (\Exception $e){
            Log::critical("ProductsController@show: ".$e->getMessage());
            return response()->json("Something went wrong!!!", 400);
        }
    }

    /**
     * Update the product
     *
     */
    public function update()
    {
        try {
            $input = request()->all();
            $product_id = isset($input['id']) ? $input['id'] : null;
            $product_name = isset($input['name']) ? trim($input['name']) : null;
            $product_price = isset($input['price']) ? trim($input['price']) : null;
            $product_sku = isset($input['sku']) ? trim(strtoupper($input['sku'])) : null;
            $product_categories_values =  isset($input['categories']) ? trim($input['categories']) : null; //comma seperated
            $product_categories = explode(',', $product_categories_values);

            //check if product exist
            if(!Product::where('id',$product_id)->exists()){
                return response()->json("Product not found.", 400);
            }else{
                $product = Product::find($product_id);
            }

            //check if sku is unique
            $is_sku_unique = Product::checkIfSkuUnique($product_sku,$product_id);

            if($is_sku_unique){
                //check categories
                if($product_categories_values != null){
                    $check_categories = $this->checkCategories($product_categories);
                    if($check_categories === false){
                        return response()->json("Categories provided are incorrect", 400);
                    }
                }

                //update product
                if($product_name != null){
                    $product->name = $product_name;
                }
                if($product_sku != null){
                    $product->sku = $product_sku;
                }
                if($product_price != null){
                    if(!is_numeric($product_price)){
                        return response()->json("Price format is incorrect.", 400);
                    }
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
                return response()->json("Product with ID -> ".$product->id." updated.", 200);
            }else{
                return response()->json("Product with sku -> ".$product_sku." already exist.", 400);
            }
        }catch (\Exception $e){
            Log::critical("ProductsController@update: ".$e->getMessage());
            return response()->json("Something went wrong!!!", 400);
        }
    }

    /**
     * Delete product
     */
    public function destroy()
    {
        try {
            $input = request()->all();
            $product_id = isset($input['product_id'])?$input['product_id']:null;
            if($product_id === null){
                return response()->json("Product id is required.", 400);
            }
            //delete product category association first
            ProductCategorie::where('product_id', '=', $product_id)->delete();

            //now delete product
            $affectedRows = Product::destroy($product_id);
            if($affectedRows > 0){
                return response()->json("Product id: ".$product_id." deleted.", 200);
            }else{
                return response()->json(null, 204);
            }
        }catch (\Exception $e){
            Log::critical("ProductsController@destroy: ".$e->getMessage());
            return response()->json("Something went wrong!!!", 400);
        }
    }
}
