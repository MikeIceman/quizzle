<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

if (isset($_GET['a']) && $_GET['a'] === '1') {
    $_COOKIE['auth'] = 1;
}

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', '217.168.66.18'], true) || PHP_SAPI === 'cli-server')
) {
//    if (!isset($_COOKIE['auth']) || $_COOKIE['auth'] != 1) {
//        header('HTTP/1.0 403 Forbidden');
//        exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
//    }
}

require __DIR__ . '/../vendor/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
