<?
require_once 'apiMethods.php'; /// подключаем все методы и свойства

if( $_GET ) {
    $watch = new watchPostSingle();
    print_r( $watch::singlePosts( $_GET['id'] ) );
}