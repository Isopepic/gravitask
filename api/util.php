<?php
// Author: Ismaël
date_default_timezone_set('America/Toronto');

function field($name) {

    if (isset($_POST[$name])){
        $r = $_POST[$name];
        return trim($r);
    }
    return null;
}
function jsonOut($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
function jsonError($msg, $code) {
    http_response_code($code);
    jsonOut(['error' => $msg]);
}
