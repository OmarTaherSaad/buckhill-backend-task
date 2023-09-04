
**

# Buckhill Backend Task

## Installation Notes
1. Application
	As any Laravel application, you need to do the following steps:
	1. Configure the`.env` file according to your server.
	2. Create a database and configure it in `.env` variables:
`DB_CONNECTION`
`DB_HOST`
`DB_PORT`
`DB_DATABASE`
`DB_USERNAME`
`DB_PASSWORD`
	3. Run `composer install`.
	4. Run `php  artisan  migrate`.
	5. You may run `php artisan db:seed` for initial dummy data.
	6. Run `php artisan serve` and enjoy the app!


2. JWT Setup
This application uses JWT for authentication with asymmetric key, so you need to:
	
	 1. Generate private and public keys
	 2. Place them somewhere in the application folder
	 3. Add two environment variables:
		    	`JWT_PRIVATE_KEY_PATH=path from storage folder to private key file`
		    	`JWT_PUBLIC_KEY_PATH=path from storage folder to public key file`

## Endpoints Documentation
Every endpoint with every possible response for it is very well documented! You can check that out [here in the doucmentation](https://app.swaggerhub.com/apis-docs/OmarTaherSaad/BuckhillTaskPetShopOTS/1.0.0).