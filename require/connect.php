<?php 
    $mysqli             = mysqli_connect('localhost','root','','booking_php');
    if($mysqli->connect_error){
        die('ERROR : (' . $mysqli->connect_errno . ')' . $mysqli->connect_error);
    }
?>