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

No matter how you authenticate, you need to create a User class that implements `UserInterface`:

```php
<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $id;
    private $username;
    private $name;

    public function getUsername() {
        return $this->username;
    }

    public function getRoles() {
        return ['ROLE_USER'];
    }

    public function getPassword() {}
    
    public function getSalt() {}
    
    public function eraseCredentials() {}

    // more getters/setters
}
```

Create a User Provider. Here you have to implement user credentials check:

```php
<?php
// src/AppBundle/Security/UserProvider.php

// ...

class UserProvider extends AbstractRemoteUserProvider
{
    /**
     * @var string
     */
    protected $userClassName;

    public function __construct($userClassName) {
        if (!class_exists($userClassName)) {
            throw new \InvalidArgumentException("Class `$userClassName` does not exists. 
                Invalid service configuration: services.remote_user_provider");
        }
        $this->userClassName = $userClassName;
    }

    public function loadUserByUsernameAndPassword($username, $password)
    {
        try {
            // Remote API call checking $username and $password here
            // Populate new User instance with response data

            return $user;
        } catch (\Exception $e) {
            throw new UsernameNotFoundException();
        }
    }
}
```

Configure authenticator and user provider services app/config/services.yml

```yaml
services:
    remote_user_provider:
        class: AppBundle\Security\UserProvider
        arguments: ["AppBundle\\Entity\\User"]

    remote_user_authenticator:
        class: RemoteUserBundle\Security\Guard\Authenticator
```

Configure security user provider app/config/security.yml

```yaml
security:
    providers:
        remote:
            id: remote_user_provider
```

Configure firewall guard app/config/security.yml

```yaml
security:
    firewalls:
        main:
            anonymous: ~
            # activate different ways to authenticate
            
            guard:
                authenticators:
                    - remote_user_authenticator
```

## Usage

POST request with `rua_username` and `rua_password` fields will initiate remote authorization call.

```bash
curl --request POST \
  --url http://site.com/ \
  --header 'content-type: multipart/form-data; boundary=---011000010111000001101001' \
  --form rua_username=username \
  --form rua_password=password
```

In case of invalid credentials or remote service unavailability you will recieve HTTP status code 403 Forbidden, otherwise 200 OK.
