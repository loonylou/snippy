<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;

//Register Forms
use Silex\Provider\FormServiceProvider;
$app->register(new FormServiceProvider());

//Fix forms error
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array(),
));

//Register Twig
use Silex\Provider\TwigServiceProvider;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app['debug'] = true;

return $app;
