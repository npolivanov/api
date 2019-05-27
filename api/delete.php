<?
require_once 'apiMethods.php'; /// подключаем все методы и свойства
var_dump($_GET);
if( $_GET ) {
	$del = new deletePosts();
	echo $del::deletePostQuery($_GET['id']);
}else {
    echo "error connect";
}