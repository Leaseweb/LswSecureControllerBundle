LswSecureControllerBundle
==================

Provide '@Secure' annotation to secure actions in controllers by specifying required roles.

## Requirements

* PHP 5.3
* Symfony 2.3

## Installation

Installation is broken down in the following steps:

1. Download LswSecureControllerBundle using composer
2. Enable the Bundle

### Step 1: Download LswSecureControllerBundle using composer

Add LswSecureControllerBundle in your composer.json:

```js
{
    "require": {
        "leaseweb/secure-controller-bundle": "*",
        ...
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update leaseweb/secure-controller-bundle
```

Composer will install the bundle to your project's `vendor/leaseweb` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Lsw\SecureControllerBundle\LswSecureControllerBundle(),
    );
}
```

## Usage

Provide '@Secure' annotation to secure actions in controllers by specifying required roles.

## License

This bundle is under the MIT license.
