<?
$server = 'us-cdbr-iron-east-04.cleardb.net';
$username = 'b8613072c41507';
$password = 'a207894a';
$db = 'heroku_e0a333c38f14545';

$link = mysqli_connect($server, $username, $password, $db);
$result = mysqli_query($link, "select * from user");

while($user = mysqli_fetch_array($result)) {
  echo $user['id'], " : ", $user['name'], "<br>";
}
?>
