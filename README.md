# SmartyStreets

A PHP library for working w/ the SmartyStreets API.

## Install

Normal install via Composer.

## Usage

```php
use Travis\SmartyStreets;

$response = SmartyStreets::run([
	'auth_id' => 'foo',
	'auth_token' => 'bar',
    'street' => '1600 Pennsylvania Ave NW',
    'city' => 'Washington',
    'state' => 'DC',
    'zip' => '20500',
]);
```

Notice that the payload must include the ``auth_id`` and ``auth_token`` values.

## Caching

Because the API charges you per lookup, you will want to cache the responses.
