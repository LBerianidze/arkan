<?php
include('vendor/autoload.php');

use Longman\TelegramBot\Entities\InputMedia\InputMediaPhoto;
use Longman\TelegramBot\Request;

$telegram = new Longman\TelegramBot\Telegram('959744814:AAEso6iSwmwtt8Jpl3UMNNhunJvmd0tWrhE', '@ArkanStatusBot');
function sendMessage($id, $text)
{
    global $telegram;
    if ($id != 0)
    {
        $result = Request::sendMessage(['chat_id' => $id, 'text' => $text, 'parse_mode' => 'HTML']);
        $result = $result->getResult();
        return $result->message_id;
    }
    return -1;
}

function sendMessageWithImage($id, $text, $url)
{
    global $telegram;
    if ($id != 0)
    {
        $result = Request::sendPhoto(['chat_id' => $id, 'caption' => $text, 'photo' => $url, 'parse_mode' => 'HTML']);
        $result = $result->getResult();
        return $result->message_id;
    }
    return -1;
}

function editMessageCaption($id, $text, $message_id, $url)
{
    global $telegram;
    if ($id != 0)
    {
        if ($url != '')
        {
            $result = Request::editMessageMedia(['chat_id' => $id, 'message_id' => $message_id, 'media' => new InputMediaPhoto(['caption' => $text, 'media' => $url,'parse_mode'=>'HTML'])]);
        }
        else
        {
            $result = Request::editMessageCaption(['chat_id' => $id, 'caption' => $text, 'message_id' => $message_id, 'parse_mode' => 'HTML']);
        }
    }
}

function editMessage($id, $text, $message_id)
{
    global $telegram;
    if ($id != 0)
    {
        $result = Request::editMessageText(['chat_id' => $id, 'text' => $text, 'message_id' => $message_id, 'parse_mode' => 'HTML']);
        $result = $result->getResult();
    }
}

function deleteMessage($id, $text, $message_id)
{
    global $telegram;
    if ($id != 0)
    {
        $result = Request::deleteMessage(['chat_id' => $id, 'message_id' => $message_id]);
        $result = $result->getResult();
    }
}