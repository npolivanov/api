<? 
header('Content-Type: text/html; charset=utf-8');

/// все методы и свойства для экзампляров объекта

// connect to dateBase
class mySqliConnectDb {
    protected function connectDb(){
       $db = new mysqli('localhost', 'root', '', 'apiTable');
       return $db;
    }
}
/// шаблон ответа
class templateResponse {
    protected function __construct( ) {
        return;
    }
}

/// авторизация (возврат в json)
class AuthorithationUsers extends mySqliConnectDb {
    static public function usersScan ( $login, $password ) {
        $db = parent::connectDb();
        $query = "SELECT `login`, `password`, `token` FROM users WHERE `login` = '$login' and `password` = '$password'";
        $result = $db->query($query)->fetch_assoc();
        /// ответ от сервера
        http_response_code(200);
        $success  = 
        [ 'login' => $result['login'],
         'password' => $result['password'],
        'status code' => http_response_code(), 
        'status text' => 'Successful authorization',
        'body' => array(
            'status' => 'true',
            'token bearer' => $result['token']
            )
        ];
        http_response_code(401);
        $fail  = 
        [ 'status code' => http_response_code(), 
        'status text' => 'invalid authorization data',
        'body' => array(
            'status' => 'false',
            'masssage' => 'invalid authorization data'
            )
        ];
        return json_encode( $result ? $success :  $fail, JSON_PRETTY_PRINT );
    }
}

/// запись поста
class writePost extends mySqliConnectDb {
    static public function createPosts ( $title, $anons, $text, $tags, $image, $token ) {
        $db = parent::connectDb();
        $replace = $db->query("SELECT * FROM posts WHERE `title` = '$title'")->fetch_assoc();
        $tokenRight = $db->query("SELECT `token` FROM users WHERE `token`='$token' ")->fetch_assoc();
        if($tokenRight){

            if($title != '' && $anons != '' && $text != '' && ($image["type"] == "image/jpeg" || $image["type"] == "image/png")  && $image["size"] <= 2e+6 )
            {
                $src = $image['name'];
            
                if( $replace == NULL )
                {
                    $data = date("l dS of F Y h:i:s A");
                    $query = "INSERT INTO posts (`date`, `title`, `anons`, `text`, `tags`, `image`) VALUES ('$data', '$title', '$anons', '$text', '$tags', '$src')";
                    $response = $db->query($query);
                    $dir = "post_images/" . $src;
                    move_uploaded_file($image["tmp_name"], $dir);
                } else
                {
                    $massage = "Заголовок поста должен быть уникальным";   
                }



            } 
            elseif($image["type"] != "image/jpeg" || $image["type"] != "image/png" ){
                $massage = "Тип фото должен быть jpeg или png";
            }
            elseif($image["size"] > 2e+6)
            {
                $massage = "image/png не больше 2мб";
            }
            else {
                $massage = "поля title,  anons, text, img - не должны быть пустыми";
            }
        }else {
            $massage = 'У Вас нет права доступа на добавление постов';
        }
        http_response_code(200);
        if($response) {

            $id_post = $db->query("SELECT `id` FROM posts WHERE `title` = '$title'")->fetch_assoc();
            $success = [
            'body' => array(
                'status' => 'true',
                'post_id' => $id_post["id"]
                )
            ];
            return json_encode( $success, JSON_UNESCAPED_UNICODE );
        } else {
            http_response_code(400);
            $fail = [ 
            'body' => array(
                'status' => 'false',
                'masssage' => $massage
                )
            ];
            return json_encode( $fail, JSON_UNESCAPED_UNICODE );
        }
    }
}

/// edit posts
class editorPosts extends mySqliConnectDb {
    static public  function updatePosts( $id, $title, $anons, $text, $tags, $img, $token ) {
        $db = parent::connectDb();
        $query = "SELECT * FROM posts WHERE `id` = '$id'";
        $request = $db->query($query)->fetch_assoc();
        $tokenRight = $db->query("SELECT `token` FROM users WHERE `token`='$token' ")->fetch_assoc();
        /// проверка на существование поста
        if( $tokenRight ) {
                if(  $request ) {
                    if($title == '' && $anons == '' && $text == ''){
                        $massage = "поля title,  anons, text, img - не должны быть пустыми";
                    }else {
                        $dir = "post_images/" . $img["name"];
                        move_uploaded_file($img["tmp_name"], $dir);
                        $img = $img["name"];
            
                        $query = "UPDATE posts SET 
                        `title` = '$title',  
                        `anons` = '$anons',
                        `text` = '$text',
                        `tags` = '$tags',
                        `image`  = '$img'
                        WHERE `id` = '$id'
                        ";
                        $data = date("l dS of F Y h:i:s A");
                        $request1 = $db->query($query);
                        if($request1){
                            http_response_code(200);
                            $massage =  [
                                'body' => array(
                                    'status' => 'true',
                                    'post' => array(
                                        'title' => $title,
                                        'datatime' => $data,
                                        'anons' => $anons,
                                        'text' => $text,
                                        'tags' => $tags,
                                        'image' => $img
                                    )
                                )

                            ];
                            
                        }
                    }
                } else {
                $massage = "Поста не существует";
                }
        }else {
            $massage = "У Вас нет права доступа редактировать статью";
        }
        return  json_encode( $massage, JSON_UNESCAPED_UNICODE );
    }
}


// watch post single
class watchPostSingle extends mySqliConnectDb { 
    static public function singlePosts( $id ) {
        $db = parent::connectDb();
        $request = $db->query("SELECT * FROM posts WHERE `id` = '$id' ")->fetch_assoc();
        if($request) {
            return json_encode($request, JSON_UNESCAPED_UNICODE);
        } else {
            return "Нет такой статьи";
        }
    }
}



// просмотр всех постов
class watchPosts extends mySqliConnectDb { 
    static public function allPosts($to, $do ) {
        $db = parent::connectDb();
        $request = $db->query("SELECT id, title, anons, image, date FROM posts ORDER BY id DESC LIMIT $to, $do");
        $allPost = array();
        $arr = array();
        while( $item = mysqli_fetch_assoc($request)  ) {
            array_push($arr,  $item );
        }  
        return  json_encode($arr, JSON_UNESCAPED_UNICODE);
    }
}

/// запись комментярия
class writeComment extends mySqliConnectDb {
    static public function commentsPost ( $author, $comment, $id_posts ) {
        $db = parent::connectDb();
        $id_posts = (int) $id_posts;
        $request = $db->query("SELECT `id`  FROM posts WHERE `id` = $id_posts")->fetch_assoc();
        http_response_code(400);
        if(!$request) {
            return json_encode(array(
                'body' => array(
                    'status:' => false,
                    'message' => "Post not found"
                )
            ), JSON_UNESCAPED_UNICODE);
        }
        
        if ( strlen( $comment ) > 255 ) {
            return json_encode(array(
                'body' => array(
                    'status:' => false,
                    'message' => "комментарий должен быть не больше 255 символов"
                )
            ), JSON_UNESCAPED_UNICODE);
        }
        $response = $db->query("INSERT INTO comments (`author`, `comment`, `id_posts`) VALUES ('$author', '$comment', '$id_posts')");
        http_response_code(201);

        if( $response ) {
            return json_encode(array(
                'body' => array(
                    'status:' => true
                )
            ), JSON_UNESCAPED_UNICODE);
        }
    }
}

/// удаление комментария
class delComment extends mySqliConnectDb {
    static public function delCom( $token, $id_post, $id_comment ) {
        $db = parent::connectDb();
        http_response_code(404);
        $tokenRight = $db->query("SELECT `token` FROM users WHERE `token`='$token' ")->fetch_assoc();
        if( $tokenRight ) {
            return json_encode(array(
                'body' => array(
                    'status:' => false,
                    'message' => "У Вас нет права доступа для удаления комментария"
                )
            ), JSON_UNESCAPED_UNICODE);
        }

        $request = $db->query("SELECT `id` FROM comments WHERE `id_posts` = '$id_post'")->fetch_assoc();
        if(!$request) {
            return json_encode(array(
                'body' => array(
                    'message' => "Post not found"
                )
            ), JSON_UNESCAPED_UNICODE);
        }

        $request = $db->query("SELECT `id` FROM comments WHERE `id` = '$id_comment'")->fetch_assoc();
        if(!$request) {
            return json_encode(array(
                'body' => array(
                    'message' => "Comment not found"
                )
            ), JSON_UNESCAPED_UNICODE);
        }
        http_response_code(201);
        $res  = $db->query("SELECT `id` FROM comments WHERE `id_posts` = '$id_post' and `id` = '$id_comment'")->fetch_assoc();
        $request = $db->query("DELETE FROM comments WHERE `id` = '$id_comment'");
        if($request && $res ) {
            return json_encode(array(
                'body' => array(
                    'status' => true
                )
            ), JSON_UNESCAPED_UNICODE);
        }else{
            return "error";
        }

    }
}
/// поиск комментария
class searchMethod extends mySqliConnectDb {
    static public function searchPosts ( $tag ) {
        $db = parent::connectDb();
        $query = "SELECT * FROM posts WHERE `tags` LIKE '%$tag%'";
        $request = $db->query($query)->fetch_assoc();
        if($request) {
            return json_encode(array(
                'body' => $request
            ), JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(array(
                'body' => array(
                    'massages' => 'not found'
                )
            ), JSON_UNESCAPED_UNICODE);
        }
    } 
}


/// удаления поста
class deletePosts extends mySqliConnectDb {
    static public function deletePostQuery( $id, $token ) {
            $db = parent::connectDb();
            $tokenRight = $db->query("SELECT `token` FROM users WHERE `token`='$token' ")->fetch_assoc();
            if($tokenRight) {
                $query = $db->query("DELETE FROM posts WHERE `id` = $id");
            }else {
                $massage = "У Вас нет права доступа";
            }
            if($query) {
                return json_encode( "ok", JSON_UNESCAPED_UNICODE );
            }else {
              return json_encode( "нет такой статьи", JSON_UNESCAPED_UNICODE );
            }
    }
}

//// Регистрация
class registerUsers extends mySqliConnectDb {
    static public function regUsers ( $email, $pass ) {
        $db = parent::connectDb();
        if(!$db) {
            echo "error db";
        } else {
            $unics = $db->query("SELECT `login` FROM  users WHERE `login`='$email' ")->fetch_assoc();
            
            if( $unics ) {

                echo "Email такой уже существует!";
                return;
            }
            $tokenStr = md5(uniqid(rand(),1)) . md5(uniqid(rand(),1));
            $passHash = password_hash($pass, PASSWORD_DEFAULT);
    
            $query = "INSERT INTO users (`login`, `password`, `token`) VALUES ('$email', '$passHash', '$tokenStr')";
            $query = $db->query($query);
            if( $query ) {
                echo $tokenStr;
            } else {
                echo "error";
            }
        }
    }
}