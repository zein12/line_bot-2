<?php


// データベース
// ・グループ
// gameRoomNum(Int) gameRoomId(String) gameMode(Int) numOfPeople(Int) numOfRolls(Int) numOfVotes(Int)
// ・個人
// userId(String) userName(String) gameRoomNum(Int) rollNum(Int) votedNum(Int) isRolling(Bool) isVoting(Boll)
// ・ゲームモード
// gameMode(Int) modeName(String)
// ・役職
// rollNum(Int) rollName(String)


require('../vendor/autoload.php');


//POST

$input = file_get_contents('php://input');
$json = json_decode($input);
$event = $json->events[0];
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('vYskIIIna79UwhpXsYtI3Xd8LsBWIrYwurJ6bWajgIKK9o7hXJuYAAl16uw8E1+9RuwuNHMPU/JEv2bL9FSu6hglkLY+fTZsSCtiEqsObUsZtUf1Hp7mmPZmttk8REBs4635vsMjsrd21TXyEN8iTQdB04t89/1O/w1cDnyilFU=');
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => 'e051f306f6d42b66e715790b82e0544d']);


////////////////////////////
//データベースと接続する場所
////////////////////////////

    $server = 'us-cdbr-iron-east-04.cleardb.net';
    $username = 'b8613072c41507';
    $password = 'a207894a';
    $db = 'heroku_e0a333c38f14545';

    $link = mysqli_connect($server, $username, $password, $db);
    //$result = mysqli_query($link, "select * from user");




$GAMEMODE_BEFORE_THE_START = 0;//@start前
$GAMEMODE_WAITING = 1;//@start後
$GAMEMODE_NIGHT = 2;//夜時間
$GAMEMODE_NOON = 3;//昼時間
$GAMEMODE_END = 4;//投票結果開示

$gameMode = $GAMEMODE_BEFORE_THE_START;//テーブル参照してＲｏｗがあれば（部屋が生成されていれば）次行で引っ張ってくる

////////////////////////////
//メインループ
////////////////////////////
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
  global $bot, $event;
  if ("@help" == $message_text) {
    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("[ヘルプ]\n@gameをグループチャットでコメントすることでゲーム開始前待機時間に移行します。そしてグループチャットがゲームルームとして認識され、ルームナンバーが発行されます。\nルームナンバーをそのままコピーして個人チャットで私にコメントすれば参加者として認識されます。\nゲーム開始前待機時間では、@memberをコメントすることで現在の参加者を見ることが出来ます。参加者が揃ったら@startしてください。ゲームが始まり夜時間へと移行します。\n夜時間では個人チャットに送られる私のコメントに従って行動してください。村人、狂人、人狼、吊人も了解ボタンを押してください。全員の行動が終われば自動的に議論時間へと移行します。\n議論時間の初めに個人チャットに投票ボタンをコメントします。ゲームルームで議論をし、投票する相手を決め投票してください。全員の投票が終われば自動的に投票結果、勝敗が開示され、ゲームが終了します。\nもう一度同じメンバーでやりたい場合は@newgameを、終わりたい、メンバーを追加したい場合は@endをゲームルームでコメントしてください。\n\n※ゲーム中に私をゲームルームから削除するとゲームがリセットされます");
    $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
  } else if ("@rule" == $message_text) {
    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ルール説明だよ");
    $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
  } else if ("@db" == $message_text) {
    $result = mysqli_query($link, "select id from user where id = 3;");
    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    //printf ("%s (%s)\n", $row[0], $row[1]);
    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("%s\n", $row[0]);
    $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
  }
}
//BeforeのDoAction,メッセージを見てアクションする
function DoActionBefore($message_text){
  global $bot, $event;
  if ("@game" == $message_text) {
    // ルームナンバー発行、テーブルにＲｏｗを生成する、gameModeを移行する
  }
}
//WaitingのDoAction,メッセージを見てアクションする
function DoActionWaiting($message_text){
  global $bot, $event;
  if ("ルームナンバー" == $message_text) {

  } else if ("@member" == $message_text) {
    // 現在参加者のみ表示
  } else if ("@start" == $message_text) {
    // 参加者一覧を表示してからゲーム開始
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
  $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("僕はワンナイト人狼Botだよ！\n\nワンナイト人狼のルールを知りたいときは「@rule」\nこのbotの使い方を知りたいときは「@help」\nゲームを始めたいときは「@game」\n\nってコメントしてね！");
  $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
}
//部屋から退出させられるときの処理
function DoActionLeave(){
  global $bot, $event;
  $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ばいばーい！\nまたやりたくなったら入れてねー！");
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
