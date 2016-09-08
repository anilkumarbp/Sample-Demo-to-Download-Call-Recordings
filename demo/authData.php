<?php

require_once(__DIR__ . '/_bootstrap.php');

use RingCentral\SDK\SDK;



		// To parse the .env
		$dotenv = new Dotenv\Dotenv(getcwd());

		$dotenv->load();

		$rcsdk = new SDK($_ENV['RC_AppKey'], $_ENV['RC_AppSecret'], $_ENV['RC_Server'], 'Demo', '1.0.0');
		
		$platform = $rcsdk->platform();

		try {

		// Retrieve previous authentication data

		$cacheDir = __DIR__ . DIRECTORY_SEPARATOR . '_cache';
		$file = $cacheDir . DIRECTORY_SEPARATOR . 'platform.json';

		if (!file_exists($cacheDir)) {
    		mkdir($cacheDir);
		}

		$cachedAuth = array();

		if (file_exists($file)) {
    		$cachedAuth = json_decode(file_get_contents($file), true);
    		unlink($file); // dispose cache file, it will be updated if script ends successfully
		}

		$auth = $platform->login($_ENV['RC_Username'], $_ENV['RC_Extension'], $_ENV['RC_Password']);
		
		$platform->auth()->setData($cachedAuth);

    	$platform->refresh();

    	print 'Authorization was restored' . PHP_EOL;


		// Save authentication data

    	file_put_contents($file, json_encode($platform->auth()->data(), JSON_PRETTY_PRINT));

	} catch (Exception $e) {

    	print 'Auth exception: ' . $e->getMessage() . PHP_EOL;

    	$auth = $platform->login($_ENV['RC_Username'], $_ENV['RC_Extension'], $_ENV['RC_Password']);

    	print_r($auth);

    	print 'Authorized' . PHP_EOL;

	}	



