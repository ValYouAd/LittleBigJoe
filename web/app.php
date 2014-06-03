<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
$loader = new ApcClassLoader('sf2lbjprod', $loader);
$loader->register(true);

$settings = parse_ini_file(__DIR__.'/../ip.ini');

/*if (!in_array($_SERVER['REMOTE_ADDR'], $settings['ip'])
) {
header('HTTP/1.0 403 Forbidden');
    exit('Vous n\'etes pas autoris&eacute; &agrave; acc&eacute;der au site. <a href="http://dev.adentify.com/access.php">Autoriser mon PC.</a>');
}*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
