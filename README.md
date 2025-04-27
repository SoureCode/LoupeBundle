
# sourecode/loupe-bundle

This bundle provides an integration of the [Loupe](https://github.com/loupe-php/loupe) search engine.
It provides a simple way using attributes to configure any class and use it in your application.

For an example, have a look at the [`BasicTest.php`](./tests/BasicTest.php) test.

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
composer require sourecode/loupe-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require sourecode/loupe-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    \SoureCode\Bundle\Timezone\SoureCodeLoupeBundle::class => ['all' => true],
];
```

## Config

```yaml
# config/packages/soure_code_loupe.yaml
soure_code_loupe:
    classes: # required
        - App\Entity\Post
        - App\Entity\User
```

> [!IMPORTANT]
> Not affiliated with the loupe-php/loupe project.