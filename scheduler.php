<?php

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_GET['path'], '/'));
//$input = json_decode(file_get_contents('php://input'),true);

print "Method: ". $method ."<br/>";
print "Request: <br/>";
var_dump($request);

if ($method == 'GET') {

    switch ($request[0]) {
        case "schedule":
            return "{}"
            break;
        case "file":
            break;
        default:
            http_response_code(404);
            break;
    }
}
/*
switch ($method) {
  case 'GET':
    $sql = "select * from `$table`".($key?" WHERE id=$key":''); break;
  case 'PUT':
    $sql = "update `$table` set $set where id=$key"; break;
  case 'POST':
    $sql = "insert into `$table` set $set"; break;
  case 'DELETE':
    $sql = "delete `$table` where id=$key"; break;
}




} elseif ($method == 'POST') {
  echo mysqli_insert_id($link);
} else {
  echo mysqli_affected_rows($link);
}

$file = realpath(dirname(__FILE__)) .'/files/2017-open-apereo-corne_oosthuizen.pptx';

print $file;

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}
*/
?>