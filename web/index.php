<?php
    $url = parse_url(getenv("b217bd08555b23:4a2fa5be@us-cdbr-iron-east-04.cleardb.net/heroku_bacf636d511d4fe?reconnect=true"));

    $server = $url["us-cdbr-iron-east-04.cleardb.net"];
    $username = $url["b217bd08555b23"];
    $password = $url["697b02c3e63d6b5"];
    $db = substr($url["heroku_bacf636d511d4fe"], 1);

    $link = mysqli_connect($server, $username, $password, $db);
    $result = mysqli_query($link, "select * from user");

echo $url;

    while($user = mysqli_fetch_array($result)) {
      echo $user['id'], " : ", $user['name'], "<br>";
    }
?>