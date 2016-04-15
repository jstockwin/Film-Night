<?php
include "../setup.php";

$whitelist = array(
    '127.0.0.1',
    '178.62.48.243',
    '::1'
);

if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
    die('Only the server may access this page.');
}

echo selectFilms();
 ?>
