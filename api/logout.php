<?php
// Author: Ismaël
require_once 'util.php';
require_once 'auth.php';
require_once 'db.php';

setTicket(requireUser(), null);
jsonOut(['ok' => true]);
