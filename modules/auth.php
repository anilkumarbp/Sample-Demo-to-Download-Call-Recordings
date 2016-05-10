<?php

try {

    // Retrieve previous authentication data
    $cacheDir = __DIR__ . DIRECTORY_SEPARATOR . '_cache';
    $file = $cacheDir . DIRECTORY_SEPARATOR . 'platform.json';

    if (!file_exists($cacheDir)) {
        mkdir($cacheDir);
    }

    $cachedAuth = array();

    function authorize($platform, $cachedAuthData, $file) {
        $auth = $platform->login($_ENV['RC_Username'], $_ENV['RC_Extension'], $_ENV['RC_Password']);
        $platform->auth()->setData($cachedAuthData);
        file_put_contents($file, json_encode($platform->auth()->data(), JSON_PRETTY_PRINT));
    }
    
    if (file_exists($file)) {
        $cachedAuth = json_decode(file_get_contents($file), true);
        unlink($file); // dispose cache file, it will be updated if script ends successfully
        $platform->auth()->setData($cachedAuth);
        
        if(!$platform->loggedIn()) {
            authorize($platform, $cachedAuth, $file);
        }
    }else {
        authorize($platform, $cachedAuth, $file);
    }
    print 'Authorization was restored' . PHP_EOL;

} catch (Exception $e) {

    print 'Auth exception: ' . $e->getMessage() . PHP_EOL;
    print 'Authorized' . PHP_EOL;
    throw $e;    
}	



