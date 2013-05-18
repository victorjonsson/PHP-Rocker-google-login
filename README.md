
# PHP-Rocker - Google login

Install this package in your [Rocker application](https://github.com/victorjonsson/PHP-Rocker) and you will have a restful
API that can authenticate users that has logged in using their user credentials at Google.

*This installation walk through takes for granted that you have some prior knowledge about [composer](http://getcomposer.org)*

### 1) Install PHP-Rocker

Here you can read more about [how to get started](https://github.com/victorjonsson/PHP-Rocker#installation) with PHP-Rocker

### 2) Add the Google login package

Add `"rocker/google-login" : "1.0.*"` to the application requirements in *composer.json* and call `composer update` in
the console.

### 3) Edit config.php

In the file config.php you add your facebook application data and change the authentication class
to Rocker\\FacebookLogin\\Authenticator. You will also have to add the facebook connect operation.

```php
return array(
    ...

    'application.auth' => array(
        'class' => '\\Rocker\\GoogleLogin\\Authenticator',
        'mechanism' => 'google realm="your.website.com"'
    ),
);
```


### 4) Implementation

That's it! Now the user can authenticate with e-mail and password registered att Google. The user will be created if
he doesn't exist in the database when authenticated.


```bash
$ curl -u 'shawn1980@gmail.com' https://www.website.com/api/me

{
    "id" : 10341,
    "email" : "shawn1980@gmail.com",
    "nick" : "Shawn 1980",
    "meta" : {
        "created" : 1368864490
    }
}
```

*Tip! Use persistent caching (APC of file based caching) in PHP-Rocker to speed up the server response when requesting
operations that requires authentication*

## Optional configuration

You can add the following configuration to config.php to restrict authentication mechanisms and e-mail domains.

```
'google.login' => array(

    # Comma separated string telling PHP-Rocker that the user has to have an e-mail address
    # at one of the declared domains
    'allowed_domains' => 'somewebsite.com,otherwebsite.co.uk',

    # Comma separated string with authentication mechanism that should be disabled
    'disabled_auth_mechanisms' => 'basic,rc4'
)
```