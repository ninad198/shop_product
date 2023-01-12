## Shop Product Management

A Laravel Shop Product Management System to Add/Manage Shops and Products

## Installation Instructions

- `git clone https://github.com/ninad198/shop_product.git`
- `composer update` or `composer install --ignore-platform-reqs`
- rename the `.env.example` file as `.env`
- set 
    
       DB_DATABASE=YOURDBNAME
       DB_USERNAME=YOURDBUSERNAME
       DB_PASSWORD=YOURDBPASSWORD
	   
	   
- Run Below Commands 
      
	- `php artisan key:generate`
	- `php artisan migrate`
	- `php artisan db:seed --class=ShopSeeder`
	- `php artisan config:cache` 
	
	
Yup that's it Please RUN `http://localhost/shop_product` URL.