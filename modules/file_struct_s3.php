<?php

$number = null;

if($callLog->direction == "Inbound") {
    $number = $callLog->to->phoneNumber;
}else{
    $number = $callLog->from->phoneNumber;
}

$extension = getExtension($number, $phoneNumbers, $accountExtensions);
if(!is_null($extension)){
    array_push($filePaths, $extension->name, substr($callLog->startTime, 0, 10), 
        $callLog->startTime."_".$callLog->recording->id.".".$recording['ext']);
}