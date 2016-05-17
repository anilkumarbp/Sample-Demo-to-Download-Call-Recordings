<?php

$totalFileCount = $_ENV['RC_requestPool'];
$callLogInEachFile = floor(count($global_callLogs)/$totalFileCount);

function saveCallLogToFile($callLogs, $filePath){
    $fo = fopen($filePath, 'w+'); 
    flock($fo, LOCK_EX); 
    fwrite($fo, json_encode($callLogs, JSON_PRETTY_PRINT));
    fflush($fo);
    flock($fo, LOCK_UN); 
    fclose($fo); 
}

function getCallLogItems($callLogs, $accountExtensions, $phoneNumbers){
    $callLogItems = array();
    foreach ($callLogs as $callLog) {
        $filePath = '';
        require('./modules/file_struct_s3.php');
        array_push($callLogItems, array(
            'recordingId' => $callLog->recording->id,
            'filePath' => $filePath,
            'recordingUrl' => $callLog->recording->contentUri
        ));
    }
    return $callLogItems;
}

$filePrefix = 'calllog_'.date('Ymd_His', $global_currentTime);
try{
    $count = 0;
    while($count < $totalFileCount - 1){
        $slice = array_slice($global_callLogs, $count * $callLogInEachFile, $callLogInEachFile);
        $filePath = $global_cacheDir.'/'.$filePrefix.'_'.$count.'.json';
        saveCallLogToFile(getCallLogItems($slice, $global_accountExtensions, $global_phoneNumbers), $filePath);
        $count++;
    }
    
    $count = $totalFileCount - 1;
    $slice = array_slice($global_callLogs, $count * $callLogInEachFile);
    $filePath = $global_cacheDir.'/'.$filePrefix.'_'.$count.'.json';
    saveCallLogToFile(getCallLogItems($slice, $global_accountExtensions, $global_phoneNumbers), $filePath);
}catch(Exception $e) {
    //In exceptions, we delete all created files in this cycle.
    foreach(glob($global_cacheDir."/".$filePrefix."*.json") as $fileName) {
        unlink($fileName);
    }
    throw $e;
}


