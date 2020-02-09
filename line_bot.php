<?php
date_default_timezone_set('Asia/Tokyo');

define('ACCESS_TOKEN', '');
define('MESSAGE_REPLY_API_URL', 'https://api.line.me/v2/bot/message/reply');

//ユーザーからのメッセージを取得
$json_str = file_get_contents('php://input');
if ($json_str) {
    $json_obj = json_decode($json_str);
     
    //返信先トークンを取得
    $reply_token = $json_obj->{'events'}[0]->{'replyToken'};
     
    //返信メッセージを作成
    $message_type = 'text';
    $reply_msg = create_reply_msg();
     
    //返信
    send_msg(ACCESS_TOKEN, $reply_token, $message_type, $reply_msg);
}

function create_reply_msg() {
    $morning_arr = [
        'おはよう',
        'Good morning'
    ];
    $daytime_arr = [
        'こんにちは',
        'Hello'
    ];
    $night_arr = [
        'こんばんは',
        'Good evening'
    ];

    $hour = date('H');

    if ($hour >= 4 && $hour <= 10) {
        return $morning_arr[mt_rand(0, count($morning_arr ) - 1)];
    } else if ($hour >= 11 && $hour <= 17) {
        return $daytime_arr[mt_rand(0, count($daytime_arr) - 1)];
    } else {
        return $night_arr[mt_rand(0, count($night_arr) - 1)];
    }
}

function send_msg($access_token, $reply_token, $message_type, $reply_msg) {
    $msgs = [
        'type' => $message_type,
        'text' => $reply_msg
    ];
 
    $post_data = [
        'replyToken' => $reply_token,
        "messages" => [$msgs]
    ];
 
    $ch = curl_init(MESSAGE_REPLY_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer '.$access_token
    ]);
    curl_exec($ch);
    curl_close($ch);
}