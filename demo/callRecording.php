<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use Aws\S3\S3Client;
use RingCentral\SDK\SDK;

echo "\n";
echo "------------Download Call Recordings to local file system ----------------";
echo "\n";


 try {


        // To parse the .env
        $dotenv = new Dotenv\Dotenv(getcwd());

        $dotenv->load();

        // Constants

          $recordingID = "";
          $status = "Success";
          $dir = $_ENV['RC_dateFrom'];
          $flag = True;
          $pageCount = 1;
          $recordingCountPerPage = 100;
          $timePerRecording = 10;

        // Create SDK instance

        $rcsdk = new SDK($_ENV['RC_AppKey'], $_ENV['RC_AppSecret'], $_ENV['RC_Server'], 'Demo', '1.0.0');

        $platform = $rcsdk->platform();

        // Authorize

        $platform->login($_ENV['RC_Username'], $_ENV['RC_Extension'], $_ENV['RC_Password'], true);

          // Writing the call-log response to json file
          $recordingsDir = getcwd() . DIRECTORY_SEPARATOR . 'Recordings/' . $dir;
          $jsonDir = getcwd() . DIRECTORY_SEPARATOR . 'Json/' . $dir;
          $csvDir = getcwd() . DIRECTORY_SEPARATOR . 'Csv/' . $dir;
          
          //Create the Directory
          if (!file_exists($recordingsDir) && !file_exists($jsonDir) && !file_exists($csvDir)) {
            mkdir($recordingsDir, 0777, true);
            mkdir($jsonDir, 0777, true);
            mkdir($csvDir, 0777, true);
          }

          $fname = $csvDir . DIRECTORY_SEPARATOR . 'recordings_' . $dir . '.csv';

          $file = fopen($fname,'w');
          $fileHeaders = array("RecordingID","ContentURI","Filename","DownloadStatus");
          fputcsv($file, $fileHeaders);
          $fileContents = array();

        $dateFrom = $_ENV['RC_dateFrom'];
        $timeFrom = '00:00:00';
        $timeTo = '23:59:59';

        // Read the contents from the Call-Log file :
        $callLogRecordsFromFile = file_get_contents(getcwd() . "/Call-Logs/${dateFrom}/call_log_${dateFrom}.json");

        $callLogRecords = json_decode($callLogRecordsFromFile, true);


        print "The type is : " . gettype($callLogRecords) . PHP_EOL;

          foreach ($callLogRecords as $callLogRecord) {

              if($callLogRecord["recording"]) {

                  $recordingID = $callLogRecord["recording"]["id"];
                              
                  print "Downloading Call Log Record : ". $recordingID . PHP_EOL;

                  $uri = $callLogRecord["recording"]["contentUri"];

                  // print "The contentURI is : ${uri}";

                  print "Retrieving ${uri}" . PHP_EOL;

                  $apiResponse = $platform->get($uri);
                              
                  $ext = ($apiResponse->response()->getHeader('Content-Type')[0] == 'audio/mpeg')
                  ? 'mp3' : 'wav';

                  // Start Time
                  $start = microtime(true);

                  // Write the recording 
                  file_put_contents("${recordingsDir}/recording_${'recordingID'}.${ext}", $apiResponse->raw());
                  $filename = "recording_${'recordingID'}.${ext}";

                  if(filesize("${recordingsDir}/recording_${'recordingID'}.${ext}") == 0) {
                    $status = "failure";
                  }

                  print "Finished downloading Recording for Call Log Record ${'recordingID'}" . PHP_EOL;

                  // write the recording metadata
                  file_put_contents("${jsonDir}/recording_${'recordingID'}.json", json_encode($callLogRecord));

                  print "Finished downloading Metadata for Call Log Record ${'recordingID'}" . PHP_EOL;

                  $end=microtime(true);

                  // Check if the recording completed wihtin 6 seconds.
                  $time = ($end*1000 - $start*1000) / 1000;
                  if($time < $timePerRecording) {
                      sleep($timePerRecording-$time);
                  }

                  // write to csv                       
                  $fileContents = array($recordingID, $uri, $filename, $status);
                  fputcsv($file, $fileContents);

                  print "Downloaded Call Log Record : ". ${'recordingID'} . PHP_EOL;                  

            }

            else {
              continue;
            }

          }
            
        fclose($file);

      
      } catch (Exception $e) {

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
                    
?>
