# Arbory CMS : Payment management module

## TODO:
   - [ ] Load automatically admin modules (menu?)

## Installation

1. Before installing this package you must require specific version of related package :

   `composer require symfony/event-dispatcher:^2.8`
   
   More info. on: https://github.com/thephpleague/omnipay


2. Add a ServiceProvider to your providers array in config/app.php:

   ```php
   'providers' => [ 
       ...,
       'Ignited\LaravelOmnipay\LaravelOmnipayServiceProvider'
   ]
   ```

3. Add the Omnipay facade to your facades array in config/app.php:
   ```php
   'aliases' => [
       ...,
       'Omnipay' => 'Ignited\LaravelOmnipay\Facades\OmnipayFacade'
   ]
   ```


4. Publish configuration files:

   `php artisan vendor:publish --provider="Ignited\LaravelOmnipay\LaravelOmnipayServiceProvider" --tag=config`

5. Run migrations

   `php artisan migrate`

