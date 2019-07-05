<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $allCategories = Category::all();
            return response()->json($allCategories, 200);
        }catch (\Exception $e){
            Log::critical("ProductsController@index: ".$e->getMessage());
            return response()->json("Something went wrong!!!", 400);
        }
    }
}
