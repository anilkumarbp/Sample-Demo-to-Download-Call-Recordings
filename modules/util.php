<?php

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
        
        $totalPages = $apiResponseJSONArray->paging->totalPages;
        $page = $apiResponseJSONArray->paging->page;
        if($page <= $totalPages) {
            $pageCount = $pageCount + 1;
            if($page == $totalPages) {
                $flag = false;
            }
        }
    }
    
    return $results;
}