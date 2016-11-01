<?php
    $server = 'us-cdbr-iron-east-04.cleardb.net';
    $username = 'b217bd08555b23';
    $password = '697b02c3e63d6b5';
    $db = 'heroku_bacf636d511d4fe';

    $link = mysqli_connect($server, $username, $password, $db);
    $result = mysqli_query($link, "select * from user");

    while($user = mysqli_fetch_array($result)) {
      echo $user['id'], " : ", $user['name'], "<br>";
    }
?>