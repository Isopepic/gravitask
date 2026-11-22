<?php
// Author: Ismaël
require_once 'util.php';
require_once 'auth.php';
require_once 'db.php';

$username = field('username');
$password = field('password');
if (!$username || !$password) {
    jsonError('missing fields', 400);
}
$user = getUserByName($username);
if (!$user || !password_verify($password, $user['password_hash'])) {
    jsonError('wrong login', 401);
}
$ticket = makeTicket();
setTicket($user['id'], $ticket);
jsonOut(['ticket' => $ticket]);
