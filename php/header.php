<?php
ob_start();
session_start();
session_write_close();
include_once 'lib.php';
$ch = dbConnect();
?>

<!DOCTYPE html>
<html>
<head>
    <title>SCU Chat</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="chat.css">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
    <script src="chat.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <script src="http://code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
</head>
<body>
