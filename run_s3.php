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
require('./modules/auth.php');

foreach(glob($global_cacheDir."/calllog*.json") as $fileName) {
    $fo = fopen($fileName, 'r'); 
    $length = filesize($fileName);
    if(!flock($fo, LOCK_EX | LOCK_NB)){
        continue;
    }else {
        rcLog($global_logFile, 1, 'Start to transfer recordings in file '.$fileName);

        $callLogs = json_decode(fread($fo, $length), true);
        $errorArray = array();
        foreach($callLogs as $callLog) {
            try{
                $recording = retrieveRecording($platform, $callLog);
                require('./modules/save_recording_s3.php');
            }catch(Exception $e){
                $callLog['error'] = $e->getMessage();
                array_push($errorArray, $callLog);
            }
        }
        if(count($errorArray) > 0) {
            $ferror = fopen($global_cacheDir.'/calllog_error_'.date('Ymd_His', time()).'.json', 'w+');
            flock($ferror, LOCK_EX); 
            fwrite($ferror, json_encode($errorArray, JSON_PRETTY_PRINT));
            fflush($ferror);
            flock($ferror, LOCK_UN); 
            fclose($ferror); 
        }
        flock($fo, LOCK_UN); 
        fclose($fo); 
        unlink($fileName);
        break;
    }
}
