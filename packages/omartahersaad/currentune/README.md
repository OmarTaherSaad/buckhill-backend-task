**

# Currency Exchange Package [CurrenTune]

## Installation Steps
1. Install the package as a local dependency:
	1. Add package folder to 'your_path' into your app.
	2. In your composer.json file, add this to "repositories":

		`"omartahersaad/currentune": {`
			`"type": "path",`
			`"url": "your-path/to/omartahersaad/currentune"`
		`}`
	3. In your composer.json file, add this to "require":
		`"omartahersaad/currentune": "*"`
2. Run `composer update`
3. Run `php artisan migrate` to run package migrations
4. You can run this if you want to publish package files to your app:
	 `php artisan vendor:publish --provider=OmarTaherSaad\CurrenTune\CurrenTuneServiceProvider`

## Configuration

- By default, the main route for currency conversion has a path saved in package config file under the key `conversion_route_path` .You can publish the package config file and edit this to your desired route path.

## Notes
This package relies on rates from [European Central Bank](https://www.ecb.europa.eu/stats/policy_and_exchange_rates/euro_reference_exchange_rates/html/index.en.html).

## Endpoints
This package expose an API GET endpoint, where you pass the amount and currency to convert to, Please check our documentation to see good examples of how this works.
[Click here for the documentation.](https://app.swaggerhub.com/apis-docs/OmarTaherSaad/CurrenTunePackageAPIs/1.0.0)