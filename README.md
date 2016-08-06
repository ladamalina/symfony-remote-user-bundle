# RemoteUserBundle

## Installation

Download the latest stable version of this bundle:

```bash
$ composer require ladamalina/remote-user-bundle
```

Enable the bundle:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Ladamalina\RemoteUserBundle\RemoteUserBundle(),
        );

        // ...
    }

    // ...
}
```
