<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 17.05.2020
 * Time: 13:07
 */
include('vendor/autoload.php');
include 'DBConfig.php';

use Longman\TelegramBot\Request;
//file_put_contents("test.txt",'test');
$db_config = new DBConfig();
$telegram = new Longman\TelegramBot\Telegram('1032111112:AAGHNnHc3cmEgFIV1EzTtoWfW4TTZxLzAq0', '@Sarafaner_bot');
$users = $db_config->getCronUsers();
$dt_now = new DateTime('now');
$json = json_decode(file_get_contents('Texts.json'), true);
foreach ($users as $user)
{
    $chat_id = $user->telegram_id;
    $dt_register = new DateTime($user->register_date);
    $dt_first = new DateTime($user->register_date);
    $dt_second = new DateTime($user->register_date);
    $dt_third = new DateTime($user->register_date);
    $stage = $user->stage;
    $notify_count = $user->notify_count;

    $text = $json[sprintf('Mot_%d_%d', $stage + 1, $notify_count + 1)];
    Request::sendMessage([
        'chat_id' => $chat_id,
        'text' => $text
    ]);

    $db_config->setNotify($chat_id, $notify_count + 1);
}
//var_dump($users);