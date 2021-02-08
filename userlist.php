<?php
$site = $_SERVER['SCRIPT_FILENAME']; // имя этого файла
$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']; // урл для асинхронного соединения
/* массив с посетителями */
$users = [
'default'=>'default'
];
/* если это произошло, значит браузер закрыт - удаляем текущего пользователя из массива */
if (isset($_POST['key'])) {
    $key = $_POST['key'];
    $userId = $_POST['userId'];
    $user = "/
'$key'=>'$userId',/";
    $str = file_get_contents($site, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $str = preg_replace($user, '', $str, 1);
    file_put_contents($site, $str, LOCK_EX);
    die();
}
/* проверяем корректность апи пользователя */
if(filter_var(userId(),FILTER_VALIDATE_IP)) {
    $userId = userId(); // валидный апи
}
else {
    $userId = rand(10000, 99999); // не валидный - придумаем что-нибудь
}
/* проверяем проверяем есть ли пользователь с таким же апи */
if($key = array_search($userId, $users)) {
    // если есть, то даем ему старый айди из массива
}
else { // вновь зашедший посетитель даем 5-ти значный айди и вносим в массив
    $key = rand(10000, 99999);
    $user = "'$key'=>'$userId',".PHP_EOL."'default'=>'default'";
    $str = file_get_contents($site, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);  
    $str = preg_replace("/'default'=>'default'/", $user, $str, 1);
    file_put_contents($site, $str, LOCK_EX);
}
/* функция определения ip посетителя */
function userId(){
    if (!empty($_SERVER['HTTP_X_REAL_IP'])){
        $ip=$_SERVER['HTTP_X_REAL_IP'];
    }
    elseif (!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Документ без названия</title>
    </head>
    <body>
        Ваш ID: <?= $key ?>
        <br>
        Кроме вас на сайте еще:
        <br>
        <ol>
        <?php
        foreach($users as $keys=>$value) {
            if($value != 'default' && $value != $userId) {
                echo "<li>Посетитель с ID: $keys</li>";
            }
        }
        ?>
        </ol>
        <script>
        /* при закрытии браузера - отправляем запрос на удаление посетителя из массива */
        window.addEventListener('beforeunload', function (){
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?=$url?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('key='+encodeURIComponent('<?=$key?>')+'&userId='+encodeURIComponent('<?=$userId?>'));
        }, false);
        </script>
    </body>
</html>
