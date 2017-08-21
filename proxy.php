<?php

set_time_limit(0);
ignore_user_abort();
header( 'Content-type: text/html; charset=utf-8' );

// Turn off output buffering
ini_set('output_buffering', 'off');
// Turn off PHP output compression
ini_set('zlib.output_compression', false);
// Implicitly flush the buffer(s)
ini_set('implicit_flush', true);
ob_implicit_flush(true);
// Clear, and turn off output buffering
while (ob_get_level() > 0) {
    // Get the curent level
    $level = ob_get_level();
    // End the buffering
    ob_end_clean();
    // If the current level has not changed, abort
    if (ob_get_level() == $level)
        break;
}

if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}


$error = isset($_REQUEST['pwd']) && isset($_REQUEST['usrname']) && isset($_REQUEST['recipient']) && isset($_REQUEST['msg']) && !empty($_REQUEST['pwd']) && !empty($_REQUEST['usrname']) && !empty($_REQUEST['recipient']) && !empty($_REQUEST['msg']);
if ($error) {

    //check for class loaded or not
    if(!class_exists('Aashu\WAY2SMSClient')){
        require_once __DIR__.'/src/WAY2SMSClient.php';
    }

    $obj = new Aashu\WAY2SMSClient();
    if (!is_bool($t = $obj->login($_REQUEST['usrname'], $_REQUEST['pwd'])))
        echo $t;
    $mob = explode(',', $_REQUEST['recipient']);
    $msg = $_REQUEST['msg'];
    foreach ($mob as $p) {
//            echo 'phone :' . $p . '  msg:  ' . $msg . '  result: invalid mobile number.';
        if (strlen($p) != 10 || !is_numeric($p) || strpos($p, ".") != false) {
            echo 'phone :' . $p . '  msg:  ' . $msg . '  result: invalid mobile number.';
            continue;
        }
        echo $obj->send($p, $msg);    
    	flush(); 
        sleep(1);
    }
} else {
    echo 'please fill all fields.';
}
