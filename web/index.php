<?php
    $url = parse_url(getenv("mysql://b217bd08555b23:4a2fa5be@us-cdbr-iron-east-04.cleardb.net/heroku_bacf636d511d4fe"));

    $username = $url["user"];
    $password = $url["pass"];
    $server = $url["host"];
    $db = substr($url["path"], 1);

    $link = mysqli_connect($server, $username, $password, $db);
    $result = mysqli_query($link, "select * from user");


    while($user = mysqli_fetch_array($result)) {
      echo $user['id'], " : ", $user['name'], "<br>";
    }
?>