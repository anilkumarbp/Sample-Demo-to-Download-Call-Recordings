<?php

try {

    $accountExtensions = requestMultiPages($platform, '/account/~/extension', array(
        'perPage' => 500,
        'page' => 1
    ));
    
    print("--------Extensions are loaded!--------\n");
    foreach ($accountExtensions as $extension) {
        print($extension->extensionNumber . ":" . $extension->name);
        print("\n");
    }
    
    $phoneNumbers = requestMultiPages($platform, '/account/~/phone-number', array(
        'usageType' => 'DirectNumber',
        'perPage' => 500,
        'page' => 1
    ));
    
    print("--------Phone numbers are loaded!--------\n");
    foreach ($phoneNumbers as $number) {
        print($number->phoneNumber);
        print("\n");
    }
    
} catch (Exception $e) {

    print 'Error occurs when retrieving extension and phone numbers -> ' . $e->getMessage() . PHP_EOL;
    throw $e;    
}	



