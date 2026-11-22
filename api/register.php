<?php
// Author: Ismaël
require_once 'util.php';
require_once 'auth.php';
require_once 'db.php';

$username = field('username');
$password = field('password');
if ($username == null || trim($username) == "" || $password == null || trim($password) == ""){
    jsonError('missing fields', 400);
}
if (getUserByName($username)){
    jsonError('name taken', 400);
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$id = createUser($username, $hash);
$ticket = makeTicket();
setTicket($id, $ticket);
jsonOut(['ticket' => $ticket]);
