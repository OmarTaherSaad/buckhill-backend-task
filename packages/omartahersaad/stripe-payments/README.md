
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

This package uses Stripe APIs, so you need to setup Stripe publishable & secret keys in your enviroment file under these names:
		`STRIPE_PUBLISHABLE_KEY=pk_Your Publishable Key`
		`STRIPE_SECRET_KEY=sk_Your Secret Key`

You may need to run `php artisan config:cache` if your configrations are already cached.
