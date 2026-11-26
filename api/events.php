<?php
// Author: Mauricio Letort

require_once 'util.php';
require_once 'auth.php';
require_once 'db.php';

$userId = requireUser();

$from = field('from');
$to = field('to');

jsonOut(listEvents($userId, $from, $to));