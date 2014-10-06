<?php

/*
 * This file is generated by S. Das.
 * Do not copy it without permission.
 */
//Contact at: tapan84silchar[at]gmail.com
//include '../../../Macros/CommonFunctions.php';
include '../../../config/config.php';
include '../Database/admin_permission_manager.php';
session_start();
$pageResultString = "Error!! Please try again.";
if (isset($_POST["user_nm"])&& isset($_POST["faculty_id"])) {
    $user_nm=$_POST["user_nm"];
    $faculty_id=$_POST["faculty_id"];
    $result=grant_permission($user_nm,$faculty_id);
    if($result=="DONE"){
            $pageResultString='New permission granted.';
    }else if($result=="DBCONNECTION_ERROR"){
        $pageResultString="Error!!! DB connection error.";
    }else if($result=="NOT_FOUND"){
        $pageResultString='<span style="color: #990000">Already granted.</span>';
    }else{
        $pageResultString="Error!!! unkonwn error.";
    }
}
echo $pageResultString;
//echo 'hii';
?>
