<?php
require_once 'lib/autoload.php';
spl_autoload_register('autoLoadClassName');

function autoLoadClassName($className){
    $found = false;
    $prefix = "MyProject\\Rafael\\";
    $length = strlen($prefix);

    if (strncmp($prefix, $className, $length) !== 0) {
        return;
    }

    $relative_class = substr($className, $length);
    $file = __DIR__.'/'.str_replace('\\', '/', $relative_class).'.php';

    if(is_file($file)){
        require_once ($file);
        $found = true;
    }

    if(!$found){
        throw new Exception("Unable to load: ".$file);
    }
    return true;
}

