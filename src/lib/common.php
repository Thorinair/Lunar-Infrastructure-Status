<?php 

    // Setup database connection parameters.
    require('db.php');
    require('key.php');

    /*
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
    */

    // Enable UTF-8 encoding.
    $dboptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
     
    // Attempt to connect to database. 
    try { 
        $db = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, $dboptions); 
    } 
    catch (PDOException $e) {         
        die('Database error. Failed to connect to database.');
    } 
     
    // Setup aditional attributes.
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
     
    // Disable magic quotes.
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) { 
        function undo_magic_quotes_gpc(&$array) { 
            foreach($array as &$value) { 
                if(is_array($value)) { 
                    undo_magic_quotes_gpc($value); 
                } 
                else { 
                    $value = stripslashes($value); 
                } 
            } 
        } 
     
        undo_magic_quotes_gpc($_POST); 
        undo_magic_quotes_gpc($_GET); 
        undo_magic_quotes_gpc($_COOKIE); 
    } 
     
    // Use UTF-8 in browser.
    header('Content-Type: text/html; charset=utf-8'); 
     
    // Start the session.
    session_start(); 

    if (isset($_COOKIE['agreed'])) {
        setcookie('agreed', '1', time() + EXPIRE_COOKIE, '/'); 
    }

    function queryDB($db, $query, $query_params) {
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
            return $stmt;
        } 
        catch (PDOException $e) { 
            dberror($e);
        }
    }

    function output($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        die('');
    } 

    function dberror($e = null) {
        if ($e) {
            $out = $e;
        }
        else {
            $out = [ 'result' => 'error_db', ];
        }
        output($out);
    } 

    // Don't close php tag to prevent redirect issues.