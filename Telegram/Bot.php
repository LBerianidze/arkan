<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 16.04.2020
 * Time: 00:54
 */
include('vendor/autoload.php');
include('BotHelper.php');
include('DBConfig.php');

use Longman\TelegramBot\Request;

class TelegramBot
{
    /**
     * @var Longman\TelegramBot\Telegram
     */
    var $telegram;
    /**
     * @var DBConfig
     */
    var $db_config;
    var $json;

    private function log()
    {
        //file_put_contents('log.txt', file_get_contents('php://input'));
    }

    private function init()
    {
        $this->telegram = new Longman\TelegramBot\Telegram('959744814:AAEso6iSwmwtt8Jpl3UMNNhunJvmd0tWrhE', '@ArkanStatusBot');
        $this->telegram->useGetUpdatesWithoutDatabase();
        $this->telegram->handle();
        $this->db_config = new DBConfig();
    }

    /**
     * @param $message \Longman\TelegramBot\Entities\Message
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function processTextMessage($message)
    {
        $chat_id = getChatId($message);

        $text = htmlspecialchars($message->getText(), ENT_QUOTES);
        $step = $this->db_config->getStep($chat_id);
        switch ($step)
        {
            case 1:
            {
                if (strlen($text) == 32)
                {
                    $user = $this->db_config->checkTelegramKey($text);
                    if ($user != null)
                    {
                        $this->db_config->setStep($chat_id, 0);
                        $this->db_config->setUserID($chat_id, $user->id);
                        Request::sendMessage(['chat_id' => $chat_id, 'text' => "Ваш аккаунт телеграмм связан с пользователем " . $user->username."\nДля отключения уведомлений введите /stop"]);
                    }
                    else
                    {
                        Request::sendMessage(['chat_id' => $chat_id, 'text' => "Неверный ключ. Пожалуйста, скопируйте его из личного кабинета"]);
                    }
                }
                else
                {
                    Request::sendMessage(['chat_id' => $chat_id, 'text' => "Ключ имеет неверный формат. Пожалуйста, скопируйте его из личного кабинета"]);
                }
                break;
            }
        }
    }

    /**
     * @param $message \Longman\TelegramBot\Entities\Message
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function processCommand($message)
    {
        $chat_id = getChatId($message);
        $command = $message->getCommand();
        if ($command == 'start')
        {
            if (!$this->db_config->userExists($chat_id))
            {
                Request::sendMessage(['chat_id' => $chat_id, 'text' => "Здравствуйте!\nПожалуйста,введите ключ Telegram из личного кабинета"]);
                $name = $message->getChat()->getFirstName() . ' ' . $message->getChat()->getLastName();
                $username = $message->getChat()->getUsername();
                if ($username == null)
                {
                    $username = '';
                }
                $this->db_config->addUser($chat_id, $name, $username);
                $this->db_config->setStep($chat_id, 1);
            }
            else
            {
                $user = $this->db_config->getUser($chat_id);
                $this->db_config->setUserID($chat_id, $user['user_id']);
                Request::sendMessage(['chat_id' => $chat_id, 'text' => "Уведомления включены."]);
            }
        }
        else if ($command == 'stop')
        {
            $user = $this->db_config->getUser($chat_id);

            if($user['user_id']!=0)
            {
                $this->db_config->setUserID(0, $user['user_id'],false);
                Request::sendMessage(['chat_id' => $chat_id, 'text' => "Уведомления отключены.\nДля возобновления введите /start"]);

            }
        }
    }

    /**
     * @param $callback \Longman\TelegramBot\Entities\CallbackQuery
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function processCallBack($callback)
    {
        $chat_id = $callback->getFrom()->getId();
        $data = $callback->getData();
        $user = $this->db_config->getUser($chat_id);
    }

    public function __construct()
    {

    }

    public function start()
    {
        $this->log();
        $this->init();
        $updates = $this->telegram->handleGetUpdates();
        $message_str = $updates->getProperty('message');
        $message = new \Longman\TelegramBot\Entities\Message($message_str);
        if ($message_str != null)
        {
            $type = $message->getType();
            if ($type == 'text')
            {
                $this->processTextMessage($message);
            }
            else if ($type == 'command')
            {
                $this->processCommand($message);
            }
        }
        else
        {
            $callback_str = $updates->getProperty('callback_query');
            if ($callback_str == null)
            {
                exit();
            }
            $callback = new \Longman\TelegramBot\Entities\CallbackQuery($callback_str);
            $this->processCallBack($callback);
        }
    }
}

(new TelegramBot())->start();