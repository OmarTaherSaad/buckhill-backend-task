
**

# Buckhill Backend Task

## Installation Notes
1. JWT Setup
This application uses JWT for authentication with asymmetric key, so you need to:
	
	 1. Generate private and public keys
	 2. Place them somewhere in the application folder
	 3. Add two environment variables:
		    	`JWT_PRIVATE_KEY_PATH=path from storage folder to private key file`
		    	`JWT_PUBLIC_KEY_PATH=path from storage folder to public key file`

## Endpoints Documentation
Every endpoint with every possible response for it is very well documented! You can check that out [here in the doucmentation](https://app.swaggerhub.com/apis-docs/OmarTaherSaad/BuckhillTaskPetShopOTS/1.0.0).