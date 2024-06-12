<?php

use App\App;

const DS = DIRECTORY_SEPARATOR;
define('PATH_ROOT', dirname(__DIR__) . DS);
define('STRIPE_PK', 'pk_test_51PMWfP2NO9IvY9DLNEmYuF9jzYHE24NnIOsXih2gcO1B4xa2TJTLZJjDmlPYHSIWORAiSAkb5yGB785ck9ftUytG00IWNkAGKI');
define('STRIPE_SK', 'sk_test_51PMWfP2NO9IvY9DLAnMt3vYLXVxpJLBAyMND0y2RWCpbMK8wpRFnZOC0dOEN1mtpXByxEHx2ggQBADQ2EAQyvOkc00zn7rghlW');


require_once PATH_ROOT . 'vendor/autoload.php';

App::getApp()->start();
