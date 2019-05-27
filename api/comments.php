<?
require "apiMethods.php";
if( count( $_POST ) > 0 ) {
    $comments = new writeComment();

  echo $comments::commentsPost( $_POST['author'], $_POST['text'], $_GET['id'] );

} else {
    echo "error 1";
}