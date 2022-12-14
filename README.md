# GeoHome

## Pre-requisites
1. Account on...

## Installation
// Composer Installation

## Raw Usage
```php
<?php

use Jasn\GeoHome\GeoHome;

include('vendor/autoload.php');

$username = 'username'; // Your account username, commonly your email
$password = 'password'; // Your account password

$GeoHome = new GeoHome($username, $password);

$electricity_usage = $GeoHome->getMeterReadings('Electricity')->getFormattedUsage();

// Prints #,###w (E.g. 1,234w) where w = watts
print_r($electricity_usage);
```

## Usage Documentation
See [Wiki](https://github.com/Jas-n/GeoHome/wiki) for in-depth usage documentation
