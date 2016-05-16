<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK\SDK;


require('./vendor/autoload.php');

date_default_timezone_set ('UTC');


// To parse the .env file
$dotenv = new Dotenv\Dotenv(getcwd());

$dotenv->load();

require('./modules/_bootstrap.php');

$rcsdk = new SDK($_ENV['RC_AppKey'], $_ENV['RC_AppSecret'], $_ENV['RC_Server'], 'App', '1.0');
$platform = $rcsdk->platform();

require('./modules/init.php');
require('./modules/util.php');

rcLog($global_logFile, 0, 'Application Start');

require('./modules/auth.php');
require('./modules/extension.php');
require('./modules/calllog.php');

if(count($global_callLogs) > 0) {
    rcLog($global_logFile, 0, 'Start to retrieve recordings!');
}

$count = 0;
$totalFileSize = 0;
foreach ($global_callLogs as $callLog) {
    $startTime = microtime(true);
    try{
        $recording = retrieveRecording($platform, $callLog);
        $filePaths = array();
        require('./modules/file_struct_s3.php');
        require('./modules/save_recording_s3.php');
    }catch(Exception $e) {
        rcLog($global_logFile, 1, 'Error occurs when sending recording '.$callLog->recording->id.' -> ' . $e->getMessage());
    }
    $count++;
    if($count == 19) {
        $endTime = microtime(true);
        rcLog($global_logFile, 0, 'Save 20 recordings(Size of '.$totalFileSize.') for '.(($startTime-$endTime)*1000). 'milliseconds.');
        break;
    }
}

file_put_contents($global_appDataFile, json_encode($global_appData, JSON_PRETTY_PRINT));

