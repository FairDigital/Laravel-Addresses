[![Latest Stable Version](https://poser.pugx.org/fairdigital/laravel-addresses/v/stable)](https://packagist.org/packages/fairdigital/laravel-addresses)
[![Total Downloads](https://poser.pugx.org/fairdigital/laravel-addresses/downloads)](https://packagist.org/packages/fairdigital/laravel-addresses)
[![License](https://poser.pugx.org/fairdigital/laravel-addresses/license)](https://packagist.org/packages/fairdigital/laravel-addresses)

# Laravel Addresses

Simple address and contact management for Laravel 5 with automatical geocoding to add longitude and latitude. Uses the famous [Countries](https://github.com/webpatser/laravel-countries) package by Webpatser.

## Important Notice

**This package is a work in progress**, please use with care and feel free to report any issues or ideas you may have!


## Installation

Require the package from your `composer.json` file

```php
"require": {
	"fairdigital/laravel-addresses": "dev-master"
}
```

and run `$ composer update` or both in one with `$ composer require fairdigital/laravel-addresses`.

Next register the following service providers to your `config/app.php` file if Laravel<5.5

```php
'providers' => [
    // Illuminate Providers ...
    // App Providers ...
    FairDigital\Addresses\AddressesServiceProvider::class,

];
```

## Configuration & Migration

```bash
$ php artisan vendor:publish --provider="FairDigital\Addresses\AddressesServiceProvider"
```

This will create a `config/addresses.php` and the migration files, that you'll have to run like so:

```bash
$ php artisan countries:migration
$ php artisan migrate
```

## Usage

First, add our `HasAddresses` trait to your model.
        
```php
<?php namespace App\Models;

use FairDigital\Addresses\Traits\HasAddresses;

class Post extends Model
{
    use HasAddresses;

    // ...
}
?>
```

##### Add an Address to a Model
```php
$post = Post::find(1);
$post->addAddress([
    'street'     => '123 Example Drive',
    'city'       => 'Vienna',
    'post_code'  => '1110',
    'country'    => 'AT', // ISO-3166-2 or ISO-3166-3 country code
    'is_primary' => true, // optional flag
]);
```

Alternativly you could do...

```php
$address = [
    'street'     => '123 Example Drive',
    'city'       => 'Vienna',
    'post_code'  => '1110',
    'country'    => 'AT', // ISO-3166-2 or ISO-3166-3 country code
    'is_primary' => true, // optional flag
];
$post->addAddress($address);
```

Available attributes are `street`, `city`, `post_code`, `state`, `country`, `state`, `note` (for internal use), `is_primary`, `is_billing` & `is_shipping`. Optionally you could also pass `lng` and `lat`, in case you deactivated the included geocoding functionality and want to add them yourself.

##### Check if Model has an Address
```php
if ($post->hasAddress()) {
    // Do something
}
```

##### Get all Addresses for a Model
```php
$addresses = $post->addresses()->get();
```

##### Get primary/billing/shipping Addresses
```php
$address = $post->getPrimaryAddress();
$address = $post->getBillingAddress();
$address = $post->getShippingAddress();
```

##### Update an Address for a Model
```php
$address = $post->addresses()->first(); // fetch the address

$post->updateAddress($address, $new_attributes);
```

##### Delete an Address from a Model
```php
$address = $post->addresses()->first(); // fetch the address

$post->deleteAddress($address); // delete by passing it as argument
```

##### Delete all Addresses from a Model
```php
$post->flushAddresses();
```

## License

Licensed under [MIT license](http://opensource.org/licenses/MIT).

## Author

**Handcrafted with love by [FairDigital](https://fairdigital.com.au) in Melbourne, Australia.**