<?
require "apiMethods.php";

if( count( $_GET ) > 0 ) {
    $search = new searchMethod();
    echo $search::searchPosts($_GET['tag']);
}else {
    echo "none";
}