<?php
session_start();
$_SESSION['payment_redirect'] = true;
echo 'OK';
