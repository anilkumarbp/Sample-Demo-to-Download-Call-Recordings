<?php

exec('rm -rf ' . __DIR__ . '/build');
@mkdir('./build');
$srcRoot = "./demo";
$buildRoot = "./build";
 
$phar = new Phar($buildRoot . "/RC_CallRecordings_Download.phar", 
    FilesystemIterator::CURRENT_AS_FILEINFO |  FilesystemIterator::KEY_AS_FILENAME, "RC_CallRecordings_Download.phar");

function listDir($root, $path, $phar)
{
    //print 'Entering ' . $root . $path . PHP_EOL;
    $it = new DirectoryIterator($root . $path);
    foreach ($it as $fileinfo) {
        $filename = $fileinfo->getFilename();
        if ($fileinfo->isDot() ||
            stristr($filename, 'Test.php') ||
            stristr($filename, '.git') ||
            stristr($filename, 'manual_tests') ||
            stristr($filename, 'tests') ||
            // stristr($filename, 'demo') ||
            stristr($filename, 'dist') ||
            stristr($filename, 'docs')
        ) {
            continue;
        } elseif ($fileinfo->isDir()) {
            listDir($root, $path . '/' . $filename, $phar);
        } else {
            $key = ($path ? $path . '/' : '') . $filename;
            $phar[$key] = file_get_contents($root . $path . '/' . $fileinfo->getFilename());
            //print '  ' . $key . ' -> ' . $path . '/' . $filename . PHP_EOL;
        }
    }
}

listDir(__DIR__ . '/', '', $phar);

$phar->setStub($phar->createDefaultStub("index.php"));
