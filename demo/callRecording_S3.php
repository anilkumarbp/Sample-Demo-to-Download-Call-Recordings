<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use Aws\S3\S3Client;
use RingCentral\SDK\SDK;

echo "\n";
echo "------------Download Call Recordings to Amazon S3  ----------------";
echo "\n";

 try {

        // $credentials_file = './config.json';
        // $credentials_file = count($argv) > 1 
        // ? $argv[1] : __DIR__ . '/_credentials.json';

        $dotenv = new Dotenv\Dotenv(__DIR__.'/../');

        $dotenv->load();

        // $credentials = json_decode(file_get_contents($credentials_file), true);

        // Create the S3 Client
        $client = S3Client::factory(array(
        'key' => $_ENV['amazonAccessKey'],
        'secret' => $_ENV['amazonSecretKey'],
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


        $dateFrom = $_ENV['dateFrom'];
        $dir = $_ENV['dateFrom'];

        // Create SDK instance

        $rcsdk = new SDK($_ENV['RC_AppKey'], $_ENV['RC_AppSecret'], $_ENV['RC_Server'], 'Demo', '1.0.0');

        $platform = $rcsdk->platform();

        // Authorize

        $platform->login($_ENV['RC_Username'], $_ENV['RC_Extension'], $_ENV['RC_Password'], true);

        

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
            // set the flag equals false
            $flag = False;
          }
          
        }

        fclose($file);
  
    } catch (Exception $e) {

            $message = $e->getMessage();

            print 'Expected HTTP Error: ' . $message . PHP_EOL;

      }

                      
?>
