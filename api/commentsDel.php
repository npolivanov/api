<?
require "apiMethods.php";

if( count( $_GET ) > 0 ) {
    $delComment = new delComment();
    echo $delComment::delCom(apache_request_headers()['token'], $_GET['id'], $_GET['id_comment']);
}else {
    echo "error";
}