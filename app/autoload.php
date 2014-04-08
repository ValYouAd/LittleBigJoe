<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$loader->add('Zend_', __DIR__.'/../vendor/pierrickmartos/zend-gdata/library');
set_include_path(__DIR__.'/../vendor/pierrickmartos/zend-gdata/library'.PATH_SEPARATOR.get_include_path());

return $loader;
