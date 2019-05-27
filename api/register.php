<?
require "apiMethods.php";

if(  $_POST ) {
   $reg = new registerUsers();
    echo $reg::regUsers($_POST['login'], $_POST['password']);
}else {
    echo "none";
}