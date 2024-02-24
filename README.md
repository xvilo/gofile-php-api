# GoFile PHP API Client
> ⚠️ This package is work in progress and basic features are not yet implemented.

## Getting started

### Installation
Run `composer require xvilo/gofile-php-api`

### Usage
```
<?php

include 'vendor/autoload.php';

$client = new \Xvilo\GoFile\Client();
dd($client->meta->getServer());

array:2 [
  "status" => "ok"
  "data" => array:1 [
    "server" => "store9"
  ]
]

```