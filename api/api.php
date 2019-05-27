<?
require_once 'apiMethods.php'; /// подключаем все методы и свойства

$user = new writePost();
echo $user::createPosts('Tyyrte', 'reter', 'text', 'tags', 'img');
