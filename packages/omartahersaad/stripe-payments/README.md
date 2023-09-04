
**

# Stripe Payment Package

## Installation Steps
1. Install the package as a local dependency:
	1. Add package folder to 'your_path' into your app.
	2. In your composer.json file, add this to "repositories":

		`"omartahersaad/stripe-payments": {`
			`"type": "path",`
			`"url": "packages/omartahersaad/stripe-payments"`
		`}`
	3. In your composer.json file, add this to "require":
		`"omartahersaad/stripe-payments": "*"`
2. Run `composer update`
3. Run `php artisan migrate` to run package migrations
4. You can run this if you want to publish package files to your app:
	 `php artisan vendor:publish --provider=OmarTaherSaad\StripePayments\StripePaymentServiceProvider`

## Configuration

- This package uses Stripe APIs, so you need to setup Stripe publishable & secret keys in your enviroment file under these names:
		`STRIPE_PUBLISHABLE_KEY=pk_Your Publishable Key`
		`STRIPE_SECRET_KEY=sk_Your Secret Key`

- You may need to run `php artisan config:cache` if your configrations are already cached.
- By default, package routes are prefixed with a prefix saved in config file as `path-prefix` , you can customize this by setting env. variable:
	`STRIPE_PATH_PREFIX=Your Prefix`

##  In-App Requirments
1. Your app must have `Order` model that contains `uuid` column for relations with payment requests.
2. Your app must have `Payment` model that contains these fields in order to save payment results:
	- `uuid`
	- `type`
	- `details`

By default, these models are expected to be `App\Models\Order` and `App\Models\Payment` but you can publish the package configuration file and update it with your custom models.

## Endpoints
This package is very simple to use, you have just one endpoint for submitting a payment, where you pass the amount and currency, along with order UUID, and we do the magic! we redirect the user to payment page and return him to the requested callback URL in the task requirments.

[Click here for the documentation.](https://app.swaggerhub.com/apis/OmarTaherSaad/StripePaymentPackageAPIs/1.0.0)
