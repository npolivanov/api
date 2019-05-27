<?
require_once 'apiMethods.php'; /// подключаем все методы и свойства
header('Content-type: text/html; charset=UTF-8');
 
if( $_POST ) {
   $user = new AuthorithationUsers();
   echo $user::usersScan($_POST["login"], $_POST["password"]);
    
}else {
    echo "error connect - require params login and password, POST";
    var_dump($_REQUEST);
}