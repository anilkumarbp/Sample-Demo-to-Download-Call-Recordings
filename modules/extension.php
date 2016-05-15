<?php

try {

    $global_accountExtensions = requestMultiPages($platform, '/account/~/extension', array(
        'perPage' => 500,
        'page' => 1
    ));
    

    rcLog($global_logFile, 0, "Extension Loaded!");
    foreach ($global_accountExtensions as $extension) {
        rcLog($global_logFile, 0, $extension->extensionNumber . ":" . $extension->name);
    }
    
    $global_phoneNumbers = requestMultiPages($platform, '/account/~/phone-number', array(
        'usageType' => 'DirectNumber',
        'perPage' => 500,
        'page' => 1
    ));
    
    rcLog($global_logFile, 0, "Phone Numbers Loaded!");
    foreach ($global_phoneNumbers as $number) {
        rcLog($global_logFile, 0, $number->phoneNumber);
    }
    
} catch (Exception $e) {
    rcLog($global_logFile, 1, 'Error occurs when retrieving extension and phone numbers -> ' . $e->getMessage());
    throw $e;    
}	



