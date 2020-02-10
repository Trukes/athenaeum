---
description: How to setup Console
---
# Setup

## Register Service Provider

Register `ConsoleServiceProvider` inside your `configs/app.php`. 

```php
return [

    // ... previous not shown ... //

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        \Aedart\Console\Providers\ConsoleServiceProvider::class

        // ... remaining services not shown ... //
    ],
];
```

## Publish Assets

Run `vendor:publish` to publish this package's assets.

```console
php artisan vendor:publish
```

The following configuration files should be added inside your `configs/` directory:

- `commands.php`
- `schedule.php`

### Publish Assets for Athenaeum Core Application

If you are using the [Athenaeum Core Application](../core/), then run the following command to publish assets:

```console
php {your-cli-app} vendor:publish-all
```
