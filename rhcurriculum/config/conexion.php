<?php
    $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);               
    $db-> connect();   
    //echo $db;
    //echo DB_SERVER.' '.DB_USER.' '.DB_PASS.' '.DB_DATABASE;	
    $error=$db->error;
?>