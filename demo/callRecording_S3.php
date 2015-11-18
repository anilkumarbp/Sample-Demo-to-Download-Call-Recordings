<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use Aws\S3\S3Client;
use RingCentral\SDK\SDK;

require_once(__DIR__ . '/_bootstrap.php');


 try {

        $credentials_file = './config.json';
        // $credentials_file = count($argv) > 1 
        // ? $argv[1] : __DIR__ . '/_credentials.json';

        $credentials = json_decode(file_get_contents($credentials_file), true);

        // Create the S3 Client
        $client = S3Client::factory(array(
        'key' => $credentials['amazonAccessKey'],
        'secret' => $credentials['amazonSecretKey'],
        'command.params' => ['PathStyle' => true]
        ));
        
        // Register the stream wrapper from an S3Client object
        $client->registerStreamWrapper();

        // create a bucket
        $client->createBucket(array('Bucket' => 'myRecording'));

        // Constants
        $status = "Success";  
        $timePerRecording = 10;
        $flag = True;
        $pageCount = 1;
        $recordingCountPerPage = 100;


        $dateFrom = $credentials['dateFrom'];
        $dir = $credentials['dateFrom'];

        // Create SDK instance

        $rcsdk = new SDK($credentials['appKey'], $credentials['appSecret'], $credentials['server'], 'Demo', '1.0.0');

        $platform = $rcsdk->platform();

        // Authorize

        $platform->login($credentials['username'], $credentials['extension'], $credentials['password'], true);

        

        // Find call log records with recordings
        $csvDir = 'Csv/' . $dir;

        if (!file_exists($csvDir)) {
            mkdir($csvDir, 0777, true);
          }

        $fname = $csvDir . DIRECTORY_SEPARATOR . 'recordings_' . $dir . '.csv';

        $file = fopen($fname,'w');
        $fileHeaders = array("RecordingID","ContentURI","Filename","DownloadStatus");
        fputcsv($file, $fileHeaders);
        $fileContents = array();

        while($flag) {

        $apiResponse = $platform->get('/account/~/extension/~/call-log', array(
                                     'type'          => 'Voice',
                                     'withRecording' => 'True',
                                     'dateFrom' => $credentials['dateFrom'] . 'T' . $timeFrom,
                                     'dateTo' => $credentials['dateFrom'] . 'T' . $timeTo,
                                     'perPage' => 300,
                                     'page' => $pageCount
                                     ));

          // ApiResponse as jsonArray()
          $callLogRecords = $apiResponse->json()->records;
          $apiResponseJSONArray = $apiResponse -> jsonArray();
          $recordCountPerPage =  + $apiResponseJSONArray["paging"]["pageEnd"] - $apiResponseJSONArray["paging"]["pageStart"] + 1;
          print 'Number of Recordings for the page : ' . $recordCountPerPage . PHP_EOL;
        

          foreach ($callLogRecords as $i => $callLogRecord) {

            $recordingID = $callLogRecord->recording->id;
                        
            print "Downloading Call Log Record : ". $recordingID . PHP_EOL;

            $uri = $callLogRecord->recording->contentUri;

            print "Retrieving ${uri}" . PHP_EOL;

            $apiResponse = $platform->get($callLogRecord->recording->contentUri);
                        
            $ext = ($apiResponse->response()->getHeader('Content-Type')[0] == 'audio/mpeg')
            ? 'mp3' : 'wav';

            $start = microtime(true);

            $filename = "s3://myRecording/Recordings/${'dateFrom'}/recording_${'recordingID'}.${ext}";

            // Write the file to S3 Bucket
            file_put_contents($filename, $apiResponse->raw());

            if(filesize($filename) == 0) {
              $status = "failure";
            }
       

            print "Finished uploading the Recording :" . $recordingID . "to S3 Bucket" . PHP_EOL;

            $end=microtime(true);

            // Check if the recording completed wihtin 6 seconds.
            $time = ($end*1000 - $start*1000) / 1000;
            if($time < $timePerRecording) {
                sleep($timePerRecording-$time);
            }

            // write to csv                       
            $fileContents = array($recordingID, $uri, $filename, $status);
            fputcsv($file, $fileContents);  
          
          }


          // Check if there is next Page
          if(isset($apiResponseJSONArray["navigation"]["nextPage"])) {  

                    sleep(60);

                    $pageCount = $pageCount + 1;
                
            }
        else {
          if($timeTo != '23:59:59' ) {
            $timeFrom = $timeTo
            $timeTo = strtotime("+30 minutes", strtotime($timeFrom)) 
          }
          else {
            $flag = False;
          }
          
        }
        fclose($file);
        }
      
      } catch (Exception $e) {

            $message = $e->getMessage();

            print 'Expected HTTP Error: ' . $message . PHP_EOL;

      }

                      
?>
