<?php

// configure your app for the production environment
// enable the debug mode
$app['debug'] = true;
$app['twig.path'] = array(__DIR__ . '/../templates');
$app['twig.options'] = array('cache' => __DIR__ . '/../var/cache/twig');
