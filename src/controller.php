<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Homepage with file selector form
$app->match('/', function (Request $request) use ($app) {
    $form = $app['form.factory']->createBuilder('form')
        ->add('image', 'file')
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        // get tmp filename
		$tmpimage = $_FILES["form"]["tmp_name"]["image"];
		
		// get original filename & extension
		$userimage = $_FILES["form"]["name"]["image"];
		$imgtype = pathinfo($userimage);
		$imgext = $imgtype['extension'];
				
		// rename wip image to snipme with correct extension, move from tmp folder to uploads
		$filename = "snipme.".$imgext;
		$uploadhome=__DIR__ . '/../web/uploads/';
		move_uploaded_file($tmpimage,$uploadhome.$filename);
		$wipfile = $uploadhome.$filename;

		// get height & width of wip image
		// calculate a snip at every (width x 2) length
		// round down snips
		list($width, $height) = getimagesize($wipfile);
		$bestheight = $width * 2;
		$nosnips = $height / $bestheight;
		$snips = floor($nosnips);

		// redirect to image viewing page
        return $app['twig']->render('showfile.twig', array (
			'userimagename'=>$userimage, 
			'ext'=>$imgext, 
			'width'=>$width, 
			'height'=>$height, 
			'bestheight'=>$bestheight, 
			'snips'=>$snips
		));
	} //form

    // display the bomepage with image selector form
    return $app['twig']->render('home.twig', array('form' => $form->createView()));
});

//Uploaded file display page
$app->get('/showfile', function() use ($app) {
	return $app['twig']->render('showfile.twig');
});

//Snip It
$app->get('/snip', function() use ($app) {
	// Create image instances
	$ext = $_REQUEST['ext'];
		$original = __DIR__.'/../web/uploads/snipme.'.$ext;
		$copy1 = __DIR__.'/../web/uploads/1snipme.'.$ext;
		file_put_contents($copy1, file_get_contents($original));
		$copy2 = __DIR__.'/../web/uploads/2snipme.'.$ext;
		file_put_contents($copy2, file_get_contents($original));
		$result = "Success";


	return $app['twig']->render('snips.twig', array('result' => $result));
});