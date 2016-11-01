<?php

$db = parse_url($_SERVER['mysql://b217bd08555b23:4a2fa5be@us-cdbr-iron-east-04.cleardb.net/heroku_bacf636d511d4fe?reconnect=true']);
$db['dbname'] = ltrim($db['heroku_bacf636d511d4fe'], '/');
$dsn = "mysql:host={$db['us-cdbr-iron-east-04.cleardb.net']};dbname={$db['dbname']};charset=utf8";

try {
    $db = new PDO($dsn, $db['b217bd08555b23'], $db['97b02c3e63d6b5']);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT * FROM user';
    $prepare = $db->prepare($sql);
    $prepare->execute();

    echo '<pre>';
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    print_r(h($result));
    echo "\n";
    echo '</pre>';
} catch (PDOException $e) {
    echo 'Error: ' . h($e->getMessage());
}

function h($var)
{
    if (is_array($var)) {
        return array_map('h', $var);
    } else {
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }
}