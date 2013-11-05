# PHP-Rocker - Google login

Install this package in your [Rocker application](https://github.com/victorjonsson/PHP-Rocker) and your users will be able
 to authenticate with their user credentials at Google.

*This installation walk through takes for granted that you have some prior knowledge about [composer](http://getcomposer.org)*

### 1) Install PHP-Rocker

Here you can read more about [how to get started](https://github.com/victorjonsson/PHP-Rocker#installation) with PHP-Rocker

### 2) Add the Google login package

Add `"rocker/google-login" : "1.0.*"` to the application requirements in *composer.json* and call `composer update` in
the console.

### 3) Edit config.php

In the file config.php you change the authentication class to Rocker\\GoogleLogin\\Authenticator.

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
$ curl -H 'Authorization: google shawn1980@gmail.com:google-password' https://www.website.com/api/me

{
    "id" : 10341,
    "email" : "shawn1980@gmail.com",
    "nick" : "Shawn 1980",
    "meta" : {
        "created" : 1368864490
    }
}
```

*Tip! Use persistent caching (APC or file based caching) in PHP-Rocker to speed up the server response when requesting
operations that requires authentication*

## Optional configuration

You can add the following configuration to config.php if you want to restrict which authentication mechanisms
that should be enabled or which e-mail domains that should be allowed.

```
'google.login' => array(

    # Comma separated string telling PHP-Rocker that the user has to have an e-mail address
    # at one of the declared domains
    'allowed_domains' => 'somewebsite.com,otherwebsite.co.uk',

    # Comma separated string with authentication mechanism that should be disabled
    'disabled_auth_mechanisms' => 'basic,rc4',
    
    # Whether or not the user credentials should be base64_decoded by the Authentication 
    # class. This option should be set to true in case your'e using rocker.js to 
    # communicate with your Rocker server
    'base64_encoded' => true
)
```
