<?php

array_push($filePaths, substr($callLog->startTime, 0, 10), 
        $callLog->startTime."_".$callLog->recording->id.".".$recording['ext']);