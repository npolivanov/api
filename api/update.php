<?
require "apiMethods.php";
if( count($_POST) > 0 ) {
    $update = new editorPosts();
   echo $update::updatePosts(
        $_GET['id'],
        $_POST['title'],
        $_POST['anons'], 
        $_POST['text'], 
        $_POST['tags'],
        $_FILES['img'],
        apache_request_headers()['token']
);
}else {
    echo "error connect";
}