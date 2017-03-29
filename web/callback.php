<?php


// データベース
// ・game_room
// game_room_num(Int) game_room_id(String) game_mode(String) num_of_people(Int) num_of_roles(Int) num_of_votes(Int)
// ・user
// user_id(String) user_name(String) game_room_num(Int) role(String) voted_num(Int) is_roling(Bool) is_voting(Bool)
//
// 初期値
// ・グループ
// gameRoomNum = null
// gameRoomId = null
// gameMode = "BEFORE_THE_START"
// numOfPeople = 0
// numOfRoles = 0
// numOfVotes = 0
//
// ・個人
// userId = null
// userName = null
// role = "無し"
// votedNum = 0
// isRoleing = false
// isVoting = false
//
// ・ゲームモード
// modeName
// BEFORE_THE_START
// WAITING
// NIGHT
// NOON
// END
//
// ・役職
// role
// 無し
// 村人
// 占い師
// 怪盗
// 人狼
// 狂人
// 吊人


require('../vendor/autoload.php');


//POST

$input = file_get_contents('php://input');
$json = json_decode($input);
$event = $json->events[0];
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('3/cEBpOR0mjAMUtnHKrSrx3N6FnMVNPYfXBIwMO6HNGaljxuxTxZz2fGrmZYFwqfV3dvAWMa7FEGrmOONfbZ7or1wxYgpjbtFMS0Mkk+RftjvYSrUpThxAHGiivf2M662z2zM5P8BSKby0dJiBG3GQdB04t89/1O/w1cDnyilFU=');
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => 'a6b4b1a80d9f25eb0a719fc92cef7d86']);


////////////////////////////
//データベースと接続する場所
////////////////////////////
$server = 'us-cdbr-iron-east-04.cleardb.net';
$username = 'b8613072c41507';
$password = 'a207894a';
$db = 'heroku_e0a333c38f14545';
$link = mysqli_connect($server, $username, $password, $db);


$GAMEMODE_BEFORE_THE_START = "BEFORE_THE_START";//@game前
$GAMEMODE_WAITING = "WAITING";//@game後
$GAMEMODE_NIGHT = "NIGHT";//夜時間
$GAMEMODE_NOON = "NOON";//昼時間
$GAMEMODE_END = "END";//投票結果開示


////////////////////////////
//メインループ
////////////////////////////
$gameMode = $GAMEMODE_BEFORE_THE_START;
// グループIDもしくはルームIDが取得できる$event->source->groupId or $event->source->roomId
// それをテーブルで検索してあればそこのレコードのGAMEMODEを$gamemodeに代入。無ければ$gameMode = $GAMEMODE_BEFORE_THE_START;ってif文を作ってほしい
if ("group" == $event->source->type) {
  $gameRoomId = $event->source->groupId;
} else if ("room" == $event->source->type) {
  $gameRoomId = $event->source->roomId;
}
$gameRoomId = mysqli_real_escape_string($link, $gameRoomId);
if($result = mysqli_query($link, "select * from game_room where game_room_id = '$gameRoomId';")){
  $row = mysqli_fetch_row($result);
  if(null != $row){
    $game_mode = $row[2];
    $gameMode = $game_mode;
  }
}



if("message" == $event->type){
  DoActionAll($event->message->text);
  if ($GAMEMODE_BEFORE_THE_START == $gameMode){
    DoActionBefore($event->message->text);
  } else if ($GAMEMODE_WAITING == $gameMode) {
    DoActionWaiting($event->message->text);
  } else if ($GAMEMODE_NIGHT == $gameMode) {
    DoActionNight($event->message->text);
  } else if ($GAMEMODE_NOON == $gameMode) {
    DoActionNoon($event->message->text);
  } else if ($GAMEMODE_END == $gameMode){
    DoActionEnd($event->message->text);
  }
} else if ("join" == $event->type){
  DoActionJoin();
} else if ("leave" == $event->type) {
  DoActionLeave();
}
return;

////////////////////////////
//関数群
////////////////////////////
//全てに共通するDoAction,メッセージを見てアクションする
function DoActionAll($message_text){
  global $bot, $event, $link, $gameMode, $_SERVER, $gameRoomId;
  if ("@help" == $message_text) {
    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("Bermigrasi ke awal permainan sebelum waktu menunggu untuk mengomentari Help] \ n @game di grup chat. Dan grup chat diakui sebagai ruang permainan, . sebagai peserta jika saya mengomentari obrolan individu. Dalam n permainan \ sebelum dimulainya waktu tunggu, Anda dapat melihat peserta saat ini untuk komentar para @member. Harap @start Ketika sdh dilengkapi dengan peserta. Permainan akan mulai bermigrasi ke waktu malam. \ N Silakan untuk bertindak sesuai dengan komentar saya untuk dikirim ke chat pribadi di waktu malam. silahkan tekan tombol OK. Bermigrasi ke otomatis waktu diskusi Setelah selesai. Pada awal \ waktu n diskusi untuk mengomentari tombol voting dalam individu chat. Pembahasan di ruang permainan, silahkan memilih memutuskan siapa yang harus memilih. Secara otomatis hasil voting Setelah selesai suara semua, menang atau kerugian diungkapkan, permainan akan berakhir. \ N a @newgame Jika Anda ingin melakukannya lagi dengan anggota yang sama, ingin akhirnya, jika Anda ingin menambahkan anggota silakan komentar yang @end di ruang permainan. \ N \ n ※ Ketika Anda menghapus aku dari ruang permainan selama pertandingan permainan akan diatur ulang");
    $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
  } else if ("@rule" == $message_text) {
    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("Aturan ket");
    $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
  } else if ("@but1" == $message_text){
    //$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ボタンだよ");
    //$response = $bot->replyMessage($event->replyToken, $textMessageBuilder);

    //$result = mysqli_query($link, "select * from game_room where game_room_id = '$gameRoomId'");
    //$row = mysqli_fetch_row($result);


    // if(null != $row){
    //
    //   $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ボタンだよ");
    //   $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
    //     $game_room_num = $row[0];
    //     $game_room_num = mysqli_real_escape_string($link, $game_room_num);
    //     $result = mysqli_query($link, "select user_name from user where game_room_num = '$game_room_num'");
    //     //$member = "";
    //     // while($row = mysqli_fetch_row($result)){
    //     //   $memberListText .= $row[1] . "\n";
    //     // }
    //     //$member = mysqli_fetch_row($result);
    //     $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("成功!");
    //     $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
    //
    //   }


    $action0 = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(石井, "Ishii");
    $action1 = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(川崎, "Kawasaki");
    $action2 = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(小野, "ono");

    $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("Siapa yang harus memilih? "" Aku memilih orang-orang yang ingin memilih! " "https://" . $_SERVER['SERVER_NAME'] . "/kyojin.jpeg", [$action0, $action1, $action2]);
    $button_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("Tombol Voting ditampilkan di sini", $button);
    
    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("");

    $response = $bot->pushMessage($event->source->userId, $button_message);

  } else if ("@but2" == $message_text){
    //$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ボタンだよ");
    //$response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
  } else if ("@debug" == $message_text) {//デバッグ用
    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($gameMode);
    $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
  } else if ("@del" == $message_text) {// デバッグ用
    $result = mysqli_query($link,"TRUNCATE TABLE game_room");
    $result = mysqli_query($link,"TRUNCATE TABLE user");
  } else if ("user" == $event->source->type) {// 一時的にこっち。最終的にはuser情報からテーブル持ってきて以下略
    $gameRoomNum = mysqli_real_escape_string($link, $message_text);
    $userId = mysqli_real_escape_string($link, $event->source->userId);
    if($result = mysqli_query($link, "select * from user where user_id = '$userId'")){
      $row = mysqli_fetch_row($result);
      if(null == $row){// 中身が空なら実行
        //個人チャット内
        if ($result = mysqli_query($link, "select * from game_room where game_room_num = '$gameRoomNum';")) {
          $row = mysqli_fetch_row($result);
          if(null != $row){
            $response = $bot->getProfile($event->source->userId);
            if ($response->isSucceeded()) {
              $profile = $response->getJSONDecodedBody();
              $user_name = mysqli_real_escape_string($link, $profile['displayName']);
              $user_id = mysqli_real_escape_string($link, $event->source->userId);
              $room_num = mysqli_real_escape_string($link, $row[0]);
              $result = mysqli_query($link, "insert into user (user_id, user_name, game_room_num, role, voted_num, is_roling, is_voting) values ('$user_id', '$user_name', '$room_num', 'tanpa', 0, 'false', 'false');");
              $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($user_name . "Yang berpartisipasi dalam permainan!");
              $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
            }
          }
        }
      }
    }
  }
}
//BeforeのDoAction,メッセージを見てアクションする
function DoActionBefore($message_text){
  global $bot, $event, $link, $result;
  if("group" == $event->source->type || "room" == $event->source->type){
    if ("@game" == $message_text) {
      // ルームナンバー発行、テーブルにレコードを生成する、gameModeを移行する
      $roomNumber = 101;// 仮
      $roomNumber = mysqli_real_escape_string($link, $roomNumber);
      if ("group" == $event->source->type){
        $gameRoomId = $event->source->groupId;
      } else if ("room" == $event->source->type) {
        $gameRoomId = $event->source->roomId;
      }
      $gameRoomId = mysqli_real_escape_string($link, $gameRoomId);
      $result = mysqli_query($link, "insert into game_room (game_room_num, game_room_id, game_mode, num_of_people, num_of_roles, num_of_votes) values ('$roomNumber', '$gameRoomId', 'WAITING', 0, 0, 0);");
      $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("Ruang NumberItu dikeluarkan! \ N nomor ruang adalah "" . $roomNumber . "」だよ！");
      $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
    }
  }
}
//WaitingのDoAction,Aksi untuk melihat pesan
function DoActionWaiting($message_text){
  global $bot, $event, $link;
  if("group" == $event->source->type || "room" == $event->source->type){
    if ("@member" == $message_text) {
      // 現在参加者のみ表示
    } else if ("@start" == $message_text) {
      // 参加者一覧を表示してからゲーム開始
    }
  }
}
//NightのDoAction,メッセージを見てアクションする
function DoActionNight($message_text){
  global $bot, $event;
  //messageでif分けする（役職行動）
}
//NoonのDoAction,メッセージを見てアクションする
function DoActionNoon($message_text){
  global $bot, $event;
  //messageでif分けする(投票)
}
//EndのDoAction,メッセージを見てアクションする
function DoActionEnd($message_text){
  global $bot, $event;
  if ("@newgame" == $message_text) {

  } else if ("@end" == $message_text) {

  }
}
//部屋に入ったときに諸々発言
function DoActionJoin(){
  global $bot, $event;
  $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("Aku serigala Bot One Night ini! \ N \ n Jika Anda ingin tahu aturan serigala satu malam ketika Anda ingin memulai "@help" \ n permainan ketika Anda ingin tahu bagaimana menggunakan "@rule" \ n bot ini adalah "@game" \ n \ n berkomentar saya!");
  $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
}
//部屋から退出させられるときの処理
function DoActionLeave(){
  global $bot, $event;
  $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("Baibai! Aku meletakkan Setelah \ n juga menjadi ingin melakukan!");
  $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
}
//DoActionNightで役職行動のPostBack来たらこれを使う
function ProcessRoling(){
  //誰かが役職行動とるとカウント＋１とtrueにする、役職のカウントと参加人数を照合して同数になったらgameMode+1と全体チャットにその旨しを伝える
}
//DoActionNoonで投票のPostBack来たらこれを使う
function ProcessVoting(){
  //誰かが投票するとカウント＋１とtrueと投票された人に＋１にする、投票のカウントと参加人数を照合して同数になったらgameMode+1と投票結果開示する
}



////////////////////////////
//データベースとの接続を終了する場所
////////////////////////////
mysqli_free_result($result);
mysqli_close($link);



//
// //イベントタイプ判別
//
// if ("message" == $event->type) {            //一般的なメッセージ(文字・イメージ・音声・位置情報・スタンプ含む)
//
//     if ("@join" == $event->message->text) {
//     	$response = $bot->getProfile($event->source->userId);
//     	if ($response->isSucceeded()) {
//     		$profile = $response->getJSONDecodedBody();
//     		$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($profile['displayName'] . "はゲームに参加したよ！");
//     		$response2 = $bot->replyMessage($event->replyToken, $textMessageBuilder);
// 		  }
//
//     } else if ("text" == $event->message->type) {
//
//       if("group" == $event->source->type) {
//
//         //groupの話
//         $action0 = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("NU", "nu");
//         $action1 = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("NO", "no");
//         $action2 = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("NE", "ne");
//
//         $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("ひげ", "ひげげ", "https://" . $_SERVER['SERVER_NAME'] . "/kyojin.jpeg", [$action0, $action1, $action2]);
//         $button_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("ひげがここにボタンで表示されてるよ", $button);
//
//
//         $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ぬ");
//         $response = $bot->pushMessage('R9b7dbfd03cbc9c2e4ab3624051c6b011', $button_message);
//       } else if("room" == $event->source->type) {
//
//     	}
//
//     } else {
//
//         $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ごめん、わかんなーい(*´ω｀*)");
//     }
//
// } elseif ("follow" == $event->type) {        //お友達追加時
//
//     $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("よろしくー");
//
// } elseif ("join" == $event->type) {           //グループに入ったときのイベント
//
//     $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('こんにちは よろしくー');
//
// } elseif ('beacon' == $event->type) {         //Beaconイベント
//
//     $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('Godanがいんしたお(・∀・) ');
//
// } else {
// }
//
// ////////////////////////////
// //データベースとの接続を終了する場所
// ////////////////////////////
//
// //$response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
//
//syslog(LOG_EMERG, print_r($event->replyToken, true));
//
//syslog(LOG_EMERG, print_r($response, true));
