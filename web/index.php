<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

//Fix forms error
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array(),
));

//Register Forms
use Silex\Provider\FormServiceProvider;
$app->register(new FormServiceProvider());

$app['debug'] = true;

//Register Twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

//Set testpage for checking mechanics are working
$app->get('/test', function() {
    return 'This is the Snippy Test Page!';
});

//Homepage with file selector form
$app->match('/', function (Request $request) use ($app) {
    $form = $app['form.factory']->createBuilder('form')
        ->add('image', 'file')
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        // do something with the data
		$image = $_FILES["form"]["tmp_name"]["image"];
		$filename = $_FILES["form"]["name"]["image"];
		
		$uploadhome=__DIR__ . '/uploads/';
		move_uploaded_file($image,$uploadhome.$filename);
		
		$wipfile = $uploadhome.$filename;
		list($width, $height) = getimagesize($wipfile);
		$bestheight = $width * 2;
		$nosnips = $height / $bestheight;
		$snips = floor($nosnips);

		// redirect somewhere
        return $app['twig']->render('showfile.twig', array ('filename'=>$filename, 'width'=>$width, 'height'=>$height, 'snips'=>$snips));
	}

    // display the form
    return $app['twig']->render('home.twig', array('form' => $form->createView()));
});

//Set uploaded file display page
$app->get('/showfile', function() use ($app) {
	return $app['twig']->render('showfile.twig');
});

$app->run();
