<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK;
use \Dropbox as dbx;

echo "\n";
echo "------------Download Call Recordings to Dropbox ----------------";
echo "\n";


try {


      // To parse the .env
      $dotenv = new Dotenv\Dotenv(getcwd());
      $dotenv->load();

      // Dropbox Authentication
      $appInfo = dbx\AppInfo::loadFromJson(array(
      'key' =>    $_ENV["dropBoxkey"],
      'secret' => $_ENV["dropBoxsecret"]
        ));
      
      $webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");

      $authorizeUrl = $webAuth->start();

      echo "1. Go to: " . $authorizeUrl . "\n";
      echo "2. Click \"Allow\" (you might have to log in first).\n";
      echo "3. Copy the authorization code.\n";
      $authCode = \trim(\readline("Enter the authorization code here: "));

      list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
      print "Access Token: " . $accessToken . "\n";
          
      $dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");

      // RC call logs    
      $callRecordings = $platform->get('/account/~/extension/~/call-log', array(
        'type' => 'Voice',
        'withRecording' => 'True'))
        ->json()->records;


        $timePerRecording = 6;

        foreach ($callRecordings as $i => $callRecording) {
        
        
        if(property_exists($callRecording,'recording')) {

        $id = $callRecording ->recording ->id;
        print "Downloading Call Log Record ${id}" . PHP_EOL;

        $uri = $callRecording->recording->contentUri;
        print "Retrieving ${uri}" . PHP_EOL;


        $apiResponse = $platform->get($callRecording->recording->contentUri);
          
        $ext = ($apiResponse->response()->getHeader('Content-Type')[0] == 'audio/mpeg')
        ? 'mp3' : 'wav';


        $start = microtime(true);

        // Store the file locally 
        file_put_contents("/Recordings/sample_${id}.${ext}", $apiResponse->raw());
       
          // Push the file to DropBox
        $f = fopen("/Recordings/sample_${id}.${ext}", "rb");     
        $result = $dbxClient->uploadFile("/sample.mp3", dbx\WriteMode::add(), $f);
        fclose($f);

          $end=microtime(true);
          
          //Delete the local copy of the file 
          unlink("/Recordings/sample_${id}.${ext}");

          $time = ($end*1000 - $start * 1000);
          
          if($time < $timePerRecording) {

            sleep($timePerRecording-$time);

          }

        }
        
        else{

          print "does not have recording" . PHP_EOL;

        }

      }

} catch (HttpException $e) {


            $message = $e->getMessage();

            print 'Expected HTTP Error: ' . $message . PHP_EOL;

            $apiResponse = $e->apiResponse();
            print 'The Request is :' . PHP_EOL;
            print_r($apiResponse->request());
            print PHP_EOL; 
            print 'The Response is :' . PHP_EOL;
            print_r($apiResponse->response());
            print PHP_EOL; 
            
            // Another way to get message, but keep in mind, that there could be no response if request has failed completely
            print '  Message: ' . $e->apiResponse->response()->error() . PHP_EOL;

}
