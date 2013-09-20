<?php
    
    function __autoload($className) {
        require_once('src/' . $className . '.php');
    }
    
    /**
    * Just a dummy dispatcher for testing purposes
    */

    $config = parse_ini_file('config.ini');

    $datastore = new SqliteStore();
    //$datastore = new JsonStore();
    $google = new GoogleOauth($datastore, $config);

    if($_GET) {
        echo $google->getToken($_GET['identifier']);
    }

    if($_POST) {
        echo $google->createToken($_POST['identifier'], $_POST['grantToken']);
    }
?>
