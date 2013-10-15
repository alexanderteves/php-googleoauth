<?php
    try {
        function __autoload($className) {
            require_once('src/' . $className . '.php');
        }
        
        $config = parse_ini_file('config.ini');

        $datastore = new SqliteStore($config);
        $google = new GoogleOauth($datastore, $config);
        $urlcreator = new UrlCreator($config);

        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if(isset($_GET['identifier']) && isset($_GET['scope'])) {
                    echo "Yes\n";
                    http_response_code(400);
                    echo json_encode(array('response' => 'Cannot use \'identifier\' and \'scope\' in the same request'), JSON_PRETTY_PRINT);
                    break;
                } else if(isset($_GET['identifier'])) {
                    $response = $google->getToken($_GET['identifier']);
                    echo json_encode(array('response' => $response), JSON_PRETTY_PRINT);
                    break;
                } else if(isset($_GET['scope'])) {
                    $response = $urlcreator->getUrl($_GET['scope']);
                    echo json_encode(array('response' => $response), JSON_PRETTY_PRINT);
                    break;
                } else {
                    $response = $google->getIdentifiers();
                    echo json_encode(array('response' => $response), JSON_PRETTY_PRINT);
                    break;
                }

            case 'POST':
                if(! isset($_POST['identifier']) || ! isset($_POST['grantToken'])) {
                    http_response_code(400);
                    echo json_encode(array('response' => 'Required parameters missing (Need \'identifier\' and \'grantToken\')'), JSON_PRETTY_PRINT);
                    break;
                }
                $response = $google->createToken($_POST['identifier'], $_POST['grantToken']);
                echo json_encode(array('response' => $response), JSON_PRETTY_PRINT);
                break;

            case 'DELETE':
                if(! isset($_GET['identifier'])) {
                    http_response_code(400);
                    echo json_encode(array('response' => 'Required parameter missing (Need \'identifier\')'), JSON_PRETTY_PRINT);
                    break;
                }
                $response = $google->deleteToken($_GET['identifier']);
                echo json_encode(array('response' => $response), JSON_PRETTY_PRINT);
                break;
        }
    } catch(Exception $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(array('response' => 'Server error, contact the admin'), JSON_PRETTY_PRINT);
    }
?>
