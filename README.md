LswSecureControllerBundle
==================

Provide '@Secure' annotation to secure actions in controllers by specifying required roles. 

NB: Instead of this bundle you may want to use the [@Security annotation](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/security.html) provided by the SensioFrameworkExtraBundle (Symfony 2.4+ feature)

NB: This bundle was created because the [JMSSecurityExtraBundle](https://github.com/schmittjoh/JMSSecurityExtraBundle) is no 
longer provided in Symfony 2.3 (due to a license incompatibility) and this was the only feature we needed.

[![Build Status](https://travis-ci.org/LeaseWeb/LswSecureControllerBundle.png?branch=master)](https://travis-ci.org/LeaseWeb/LswSecureControllerBundle)

## Requirements

* PHP 5.3
* Symfony 2.8

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

As an example we show how to use the '@Secure' annotation in the AcmeDemoBundle to secure the "hello world"
page requiring the role "ROLE_TEST" to execute.

In ```src/Acme/DemoBundle/Controller/SecuredController.php``` you should add the following line on 
top, but under the namespace definition:

``` php
use Lsw\SecureControllerBundle\Annotation\Secure;
```

To require the "ROLE_TEST" for "helloAction" in the "SecuredController" you should add the line
```@Secure(roles="ROLE_TEST")``` to the DocBlock of the "helloAction" like this: 

``` php
    /**
     * @Secure(roles="ROLE_TEST")
     * @Route("/hello", defaults={"name"="World"}),
     * @Route("/hello/{name}", name="_demo_secured_hello")
     * @Template()
     */
    public function helloAction($name)
    {
        return array('name' => $name);
    }
```

Or to the DocBlock of the controller like this: 

``` php
    /**
     * @Secure(roles="ROLE_TEST")
     */
    class AdminController extends Controller
    {
      ...
    }
```

If the user does not have the role the following error should appear when accessing the action:

```
Current user is not granted required role "ROLE_TEST".
403 Forbidden - AccessDeniedHttpException
1 linked Exception:
```

If you put the "@Secure" annotation on an action that is not behind a firewall you get this error:

```
@Secure(...) annotation found without firewall on "helloAction" in 
".../src/Acme/DemoBundle/Controller/DemoController.php"
500 Internal Server Error - AuthenticationCredentialsNotFoundException
```

Note that you can configure the firewall in ```app/config/security.yml```.

## Credits

This would not have been possible without [Matthias Noback](https://github.com/matthiasnoback) his excellent posts:

 - [Symfony2 & Doctrine Common: creating powerful annotations](http://php-and-symfony.matthiasnoback.nl/2011/12/symfony2-doctrine-common-creating-powerful-annotations/)
 - [Prevent Controller Execution with Annotations and Return a Custom Response](http://php-and-symfony.matthiasnoback.nl/2012/12/prevent-controller-execution-with-annotations-and-return-a-custom-response/)

## Contributors

 - [GregoireHebert](https://github.com/GregoireHebert)
 - [mevdschee](https://github.com/mevdschee)

## License

This bundle is under the MIT license.
