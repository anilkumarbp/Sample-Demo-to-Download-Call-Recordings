<?php

$cacheDir = __DIR__ . DIRECTORY_SEPARATOR . '_cache';
$appDataFile = $cacheDir . DIRECTORY_SEPARATOR . 'app_data.json';

if (!file_exists($cacheDir)) {
    mkdir($cacheDir);
}

$appData = array(
    'lastRunningTime' => null
);

if (file_exists($appDataFile)) {
    $appData = json_decode(file_get_contents($appDataFile), true);
}

function requestMultiPages($platform, $url, $options) {
    
    $results = array();
    
    $pageCount = 1;
    $flag = true;
    
    while($flag) {

        $apiResponse = $platform->get($url, $options);
        $apiResponseJSONArray = $apiResponse->json();
        $records = $apiResponseJSONArray->records;
        
        foreach ($records as $record) {
            array_push($results, $record);
        } 
        
        if(property_exists($apiResponseJSONArray->paging, 'totalPages')) {
            $totalPages = $apiResponseJSONArray->paging->totalPages;
            $page = $apiResponseJSONArray->paging->page;
            if($page <= $totalPages) {
                $pageCount = $pageCount + 1;
                if($page == $totalPages) {
                    $flag = false;
                }
            }
        }else {
            if(isset($apiResponseJSONArray->navigation->nextPage)){
                $pageCount = $pageCount + 1;
            }else{
                $flag = false;
            }
        }
    }
    
    return $results;
}