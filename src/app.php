<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;



$app = new Application();

$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
});

$app->register(new DoctrineServiceProvider, [
    'db.options' => [
        'driver' => 'pdo_pgsql',
        'host' => '172.25.0.3',
        'dbname' => 'db_silex',
        'user' => 'silex',
        'password' => 'silex',
        'port' => '5432'
    ],
]);

$app->register(new DoctrineOrmServiceProvider, [
    'db.orm.class_path'            => __DIR__.'/../vendor/doctrine/orm/lib',
    'db.orm.proxies_dir'           => __DIR__.'/../var/cache/doctrine/Proxy',
    'db.orm.proxies_namespace'     => 'DoctrineProxy',
    'db.orm.auto_generate_proxies' => true,
    'orm.em.options' => [
        'mappings' => [
            [
                'type' => 'annotation',
                'use_simple_annotation_reader' => false,
                'namespace' => 'Entities',
                'path' => __DIR__.'/Entities',
            ],
        ],
    ],
]);





$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider(), [
    'locale_fallbacks' => ['en'],
]);

$app->register(new FormServiceProvider());

$app->register(new ValidatorServiceProvider());

$app->register(new Silex\Provider\SessionServiceProvider());

return $app;
