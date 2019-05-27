<?
require_once 'apiMethods.php'; /// подключаем все методы и свойства

if( count($_POST) > 0 && count($_GET) == 0) {
  
   $user = new writePost();
   $user::createPosts('Title', 'anons', 'text', 'tags', 'img', 'token');
    echo writePost::createPosts(

        $_POST['title'], 

        $_POST['anons'], 

        $_POST['text'], 

        $_POST['tags'], 

        $_FILES['img'],

        apache_request_headers()['token']
    );
}
// elseif( count($_GET) > 0 ){
//  // code
//     $watch = new watchPostSingle();
//     echo $watch::singlePosts($_GET['id']);
// }
elseif( count($_POST) == 0  ){
    $watch = new watchPosts();

   print_r( $watch::allPosts( $_GET['to'] ,  $_GET['do']) );
  


}else {
    echo "error connect - must item 'Title', 'anons', 'text', 'tags', 'img', 'token'";
}

function isJson($string) {
    json_decode($string);
    if(json_last_error() == JSON_ERROR_NONE){
        return "no";
    }else {
        return "yes";
    }
   }