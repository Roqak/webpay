<?php
$host="localhost";
$username="alumni";
$password="";
$db = "donations";
// global $connect;
$connect = mysqli_connect($host, $username, $password,$db);
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully";