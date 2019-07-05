## About Spicy Deli

API endpoints for mobile and desktop app to fetch and update data.<br><br>

Setting up Project
- Clone repo
- make .env file with proper db creds
- run composer install
- run 'php artisan migrate' (to create db schema and relationships)
- run 'php artisan db:seed --class=ProductCategoriesTableDataSeeder' (to run the seeder and prefil database with sample data)
- run 'php artisan serve'

As the project has only api endpoints, use postman or any other API development environment to test the routes listed below:

Routes available

- [GET] /api/products : 
    
    Parameters_name: None
    
    Return Type: JSON array of all products in the database.
    
    
- [GET] /api/products/{id} : 
    
    Parameters_name: id (product id:numeric) - Required
    
    Return Type: JSON array of the product that matches id.
    
- [POST] /api/products/delete : 
    
    Parameters_name: product_id (product id:numeric) - Required
    
    Return Type: HTTP 200 response if product deleted successfully.
    
- [POST] /api/products/create : 
    
    Parameters_names: <br>
    name (product name:string) - Required<br>
    price (product price:numeric) - Required<br>
    sku (product sku:string) - Required<br>
    categories (product categories:comma seperated category IDS) - Required<br>
    
    Return Type: HTTP 200 response if product created successfully. Else 400 response if data validation fails

- [POST] /api/products/update : 
    
    Parameters_names: <br>
    id (product id:numeric) - Required<br>
    name (product name:string) - optional<br>
    price (product price:numeric) - optional<br>
    sku (product sku:string) - optional<br>
    categories (product categories:comma seperated category IDS) - optional<br>
    
    Return Type: HTTP 200 response if product created successfully. Else 400 response if data validation fails

- [GET] /api/categories : 
      
      Parameters_name: None
      
      Return Type: JSON array of all categories in the database.
