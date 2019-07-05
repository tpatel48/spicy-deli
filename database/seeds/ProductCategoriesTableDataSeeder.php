<?php

use Illuminate\Database\Seeder;

class ProductCategoriesTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //seeding Categories start
        $seed_categories_array = ['Ethiopia','Meat','Beef','Chili pepper','China','Fish','Tofu','Sichuan pepper','Peru','Potato','Yellow Chili pepper'];

        foreach ($seed_categories_array as $category){
            DB::table('categories')->insert([
                'name' => $category,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        //seeding Categories End

        //Seeding Products start
        $seed_products_array = [
            [
                'name' => "Sik Sik Wat",
                'sku' => 'DISH999ABCD',
                'price' => '13.49'
            ],
            [
                'name' => "Huo Guo",
                'sku' => 'DISH234ZFDR',
                'price' => '11.99'
            ],
            [
                'name' => "Cau-Cau",
                'sku' => 'DISH775TGHY',
                'price' => '15.29'
            ]
        ];

        foreach ($seed_products_array as $product){
            DB::table('products')->insert([
                'name' => $product['name'],
                'sku' => $product['sku'],
                'price' => $product['price'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        //Seeding Products end

        //Seeding Productcategories start
        $seed_productcategories_array = [
            [
                'product_id' => "1",
                'category_ids' => [1,2,3,4]
            ],
            [
                'product_id' => "2",
                'category_ids' => [5,2,3,6,7,8]
            ],
            [
                'product_id' => "3",
                'category_ids' => [9,10,11]
            ],
        ];

        foreach ($seed_productcategories_array as $productcategory){
            foreach ($productcategory['category_ids'] as $category_id){
                DB::table('product_categories')->insert([
                    'product_id' => $productcategory['product_id'],
                    'category_id' => $category_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        //Seeding Productcategories end

    }
}
