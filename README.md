#RingCentral Call Generator Recordings Downloader - BETA

A PHP command line application to transfer call recordings from RingCentral server to AWS S3.

#Introduction

This application contains two entry points. One is run_calllog.php which gets call logs from RingCentral server and save call logs as temp files in `_cache` folder. Another one is run_s3.php which retrieves recordings based on call logs get in last script and then send recordings to S3. Each script should be run in a regular timespan respectively.  

#How to use

Please follow below steps to run the application.

##Configuration

A .env file needs to be created at project root to provide required configurations. Following is an example.

```
Log_Level= 0
RC_AppKey= appKey
RC_AppSecret= appSecret
RC_Server= https://platform.devtest.ringcentral.com
RC_Username= 123456789
RC_Extension= 
RC_Password= password
RC_maxRetrieveTimespan = 60
RC_requestLimit = 30
RC_requestPool = 1
amazonAccessKey= awsKey
amazonSecretKey= awsSecret
amazonRegion= awsRegion
amazonS3Bucket= S3bucketname
```

Here are detailed explanation

###Log Level
```
Log_Level= 0

```

This application supports 3 log levels, including INFO(0), DEBUG(1) and ERROR(2). So developer/operator could specify what kind of messages goes into log file. Notice that the message types which are no less than the specified value will go into log.

###RingCentral App Credential
```
RC_AppKey= appKey
RC_AppSecret= appSecret
RC_Server= https://platform.devtest.ringcentral.com
```

Specify the credentials of your RingCentral App.

###RingCentral Account Credential
```
RC_Username= 123456789
RC_Extension= 
RC_Password= password
```

Specify the credentials of the RingCentral account which will be used to login and fetch call recordings. Notice that only account-level extension is allowed to fetch call recordings of all extensions in that account. Otherwise exception will be thrown.

###Max Retrieve Timespan
```
RC_maxRetrieveTimespan = 60
```

Specify the timespan(in seconds) used to fetch call logs at first run of `run_calllog.php`. So if it is set as 60, it means that application will try to fetch all the call logs recorded in past 60 seconds. Notice that this variable is only used at first run. After that, application will record last timestamp the call logs are fetched and use this timestamp as the starting point for next fetch.  

###Request Configurations
```
RC_requestLimit = 30
RC_requestPool = 1
```
Several factors need to be considered when setting these two variables. More info could be found in `How It Works` section. Bascially, `RC_requestLimit` should be set to `MaxAppRequestLimit - 10`. And make sure that the value of `RC_requestLimit/RC_requestPool` is an integer and less than 20.

###Amazon Account Credentials
```
amazonAccessKey= awsKey
amazonSecretKey= awsSecret
amazonRegion= awsRegion
amazonS3Bucket= S3bucketname
```

Specify the AWS account credentials. 

##Run App with Cron Job

It takes the advantage of cron job to run the application regularly. Two types of cron jobs are required.

###Fetch Call Logs

One type of the cron job is to run `run_calllog.php` to fetch call logs. Recommended execution timespan is 3 to 5 mins.

```
*/3 * * * * cd /home/ec2-user/RingCentral-Call-Generator-Recordings-Downloader && php run_calllog.php
```

###Send Recordings

Another type of cron job is to run `run_s3.php` to send recordings to s3. As many as the value of `RC_requestPool` cron jobs need to be setup as follows

```
*/1 * * * * cd /home/ec2-user/RingCentral-Call-Generator-Recordings-Downloader && php run_s3.php
```

Notice that the execution tiemspan should be set to every 1 min.

#Monitoring

The appliation will log all messages in `_cache/log` file. 