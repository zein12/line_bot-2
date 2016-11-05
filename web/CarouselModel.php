<?php

/**
 * Created by IntelliJ IDEA.
 * User: ashi_psn
 * Date: 2016/11/06
 * Time: 2:48
 */
class CarouselModel
{



    static function sendCarousel($game_room_id,$link){
        //$userlist = getUserList($game_room_id,$link);
        $userlist = self::getUserList($game_room_id,$link);
        return $userlist;
    }




    static function getUserList($game_room_id,$link){
        $result = mysqli_query($link, "select user_id from user where game_room_nm = '$game_room_id'");
        //select user_id from user where game_room_nm = $roomid
        return mysqli_fetch_row($result);
    }





    static function getRoomUsers($userid,$userList){

    }

}