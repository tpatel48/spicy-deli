<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProductCategorie;
use App\Category;

class Product extends Model
{
    public function categories()
    {
        return $this->morphToMany('App\Category', 'categories');
    }

    public function getCategories($id){
        $categories = [];
        $productCategories = ProductCategorie::where('product_id', $id)->get();
        foreach ($productCategories as $category){
            $cat_name = Category::find($category->category_id)->name;
            array_push($categories,$cat_name);
        }
        return $categories;
    }

    public static function checkIfSkuUnique($product_sku,$product_id = null){

        if(Product::where('sku',$product_sku)->where('id', '!=' , $product_id)->exists()){
            return false;
        }else{
            return true;
        }
    }
}
