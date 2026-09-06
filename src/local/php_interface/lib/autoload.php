<?php

spl_autoload_register(
    function ($className): void {
        $filePath = __DIR__;

        $path = explode('\\', $className);
        unset($path[0]);

        foreach ($path as $dirOrFileName) {
            $filePath .= '/' . $dirOrFileName;
        }

        $filePath .= '.php';

        if (file_exists($filePath)) {
            include_once $filePath;
        }
    }
);
