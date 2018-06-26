<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../bootstrap/autoload.php';


/*
|--------------------------------------------------------------------------
| this is spider
|--------------------------------------------------------------------------
|
| if you use spider, you will die,bu I fuyukaidesu,because I don`t like 
|  that. You're going to be very dangerous,you have to belive me. so ,
|  Start your trip!
| 
|
*/

require __DIR__ .'/../phpspider/autoloader.php';


/*
|--------------------------------------------------------------------------
| this is beanbun
|--------------------------------------------------------------------------
|
| Beanbun is a simple extensible crawler framework that supports the daemon 
| pattern and the common pattern, the daemon pattern is based on the 
| Workerman, and the Downloader is based on the Guzzle.
| 
|
*/


require __DIR__ .'/../beanbun/autoload.php';


/*
|--------------------------------------------------------------------------
| this is querylist
|--------------------------------------------------------------------------
|
| QueryList uses jQuery selectors to do collection, allowing you to say 
| goodbye to complex regular expressions; QueryList has the same 
| DOM operating capability as jQuery, Http network operation ability, 
| chaotic code resolution, content filtering ability, and extensible
| ability; it can easily implement such ideas as analog landing, 
| forgery, HTTP agent and so on. Miscellaneous network requests; rich 
| plug-ins, support for multi-threaded acquisition and collection of 
| JavaScript dynamic rendering pages using PhantomJS.
*/

require __DIR__."/../querylist/autoload.php";


/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
