<?php 

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Max-Age: 86400');

    if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
        exit();
    }

    require('../lib/common.php');

    /*
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
    */

    // Convert POST, GET or JSON data to object.
    if (!empty($_POST)) { 
        $post = (object) $_POST;
    }
    elseif (!empty($_GET)) { 
        $post = (object) $_GET;
    }
    else {
        $post = json_decode(file_get_contents('php://input'));
    }    

    if ($post->key == $apikey) {
        if ($post->data != null) {

            $query = 'UPDATE Status SET data = :data, timestamp = :now WHERE ID = 1';
            $query_params = array(':data' => $post->data, ':now' => time());
            $stmt = queryDB($db, $query, $query_params);

            $out = [ 'result' => 'success', ];
            output($out);
        }
        else {
            $out = [ 'result' => 'error_no_data', ];
            output($out);
        }

    }
    else {
        $query = 'SELECT * FROM Status WHERE ID = 1';
        $stmt = queryDB($db, $query, $query_params);

        $row = $stmt->fetch();
        if ($row) { 
            $data = $row['data'];
            $time = $row['timestamp'];

            $out = [
            	'result' => 'success',
            	'data' => $data,
            	'age' => time() - $time,
            ];
        	output($out);
        }
        else {
            $out = [ 'result' => 'error_database', ];
            output($out);
        }
    }