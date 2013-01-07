# SmartyStreets for Laravel 3

SmartyStreets is an online API for standardizing mailing addresses.  This package will throw addresses against the API and return the cleaned address, and will store the result in your database so you never get charged for the same lookup twice.

## Install

In your ``application/bundles.php`` add the following:

```
'smartystreets' => array('auto' => true),
```

### Configuration

Copy the sample config file to ``application/config/smartystreets.php``, and input the necessary information.

### Migrations

* Setup framework: ``$ php artisan migrate:install``
* Add new table: ``$ php artisan migrate smartystreets``

## Usage

```
$array = SmartyStreets::run(array(
    'street' => '',
    'city' => '',
    'state' => '',
    'zip' => '',
));
```

The response array will contain the cleaned address, and additional information from the lookup record.  The method uses a database table to cache responses for a year before looking the same address up again.