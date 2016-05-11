<?php

try {

    // Retrieve previous authentication data
    $cacheDir = __DIR__ . DIRECTORY_SEPARATOR . '_cache';
    $file = $cacheDir . DIRECTORY_SEPARATOR . 'platform.json';

    if (!file_exists($cacheDir)) {
        mkdir($cacheDir);
    }

    function authorize($platform, $file) {
    }
    
    if (file_exists($file)) {
        $cachedAuth = json_decode(file_get_contents($file), true);
        $platform->auth()->setData($cachedAuth);
        
        if(!$platform->loggedIn()) {
            $platform->login($_ENV['RC_Username'], $_ENV['RC_Extension'], $_ENV['RC_Password']);
        }
    }else {
        $platform->login($_ENV['RC_Username'], $_ENV['RC_Extension'], $_ENV['RC_Password']);
    }
    file_put_contents($file, json_encode($platform->auth()->data(), JSON_PRETTY_PRINT));
    
    print 'Authorization was restored' . PHP_EOL;

} catch (Exception $e) {

    print 'Auth exception: ' . $e->getMessage() . PHP_EOL;
    throw $e;    
}	



