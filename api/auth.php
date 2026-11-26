<?php
// Author: Ismaël
require_once 'util.php';
require_once 'db.php';

function makeTicket() {
    $b = random_bytes(16);
    return bin2hex($b);
}
function requireUser() {
    $ticket = field('ticket');
    if ($ticket == null){
        jsonError('bad ticket', 401);
    }
    $user = getUserByTicket($ticket);
    if ($user == null){
        jsonError('bad ticket', 401);
    }
    return $user['id'];
}
