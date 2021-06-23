<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 14.04.2020
 * Time: 14:58
 */
/**
 * @param \Longman\TelegramBot\Entities\Message $message
 * @return mixed
 */
function getChatId($message)
{
	$update_type = $message->getType();
	if ($update_type == 'text' || $update_type=='command')
	{
		return $message->getChat()->getId();
	}
	else
	{
		return $message->getFrom()->getId();
	}
}
function getCurrentDate()
{
	return (new DateTime('now'))->format('Y-m-d H:i:s');
}
function writeDump($item)
{
	ob_flush();
	ob_start();
	var_dump($item);
	file_put_contents("dump.txt", ob_get_flush());
}