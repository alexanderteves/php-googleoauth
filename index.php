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

    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $response = $google->getToken($_GET['identifier']);
            echo json_encode(array('response' => $response));
            break;

        case 'POST':
            if(! isset($_POST['identifier']) || ! isset($_POST['grantToken'])) {
                http_response_code(400);
                echo json_encode(array('response' => 'Required parameters missing (Need \'identifier\' and \'grantToken\')'));
                break;
            }
            $response = $google->createToken($_POST['identifier'], $_POST['grantToken']);
            echo json_encode(array('response' => $response));
            break;
    }
?>
