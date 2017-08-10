# payments
Payment management module

- Installation

Add a ServiceProvider to your providers array in config/app.php:

'providers' => [

'Ignited\LaravelOmnipay\LaravelOmnipayServiceProvider',

]
Add the Omnipay facade to your facades array:

'Omnipay' => 'Ignited\LaravelOmnipay\Facades\OmnipayFacade',
Finally, publish the configuration files:

php artisan vendor:publish --provider="Ignited\LaravelOmnipay\LaravelOmnipayServiceProvider" --tag=config

Before installing require - composer require symfony/event-dispatcher:^2.8
More info. on: https://github.com/thephpleague/omnipay
