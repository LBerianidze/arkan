<?php
if (!defined('OK_LOADME'))
{
    die('o o p s !');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    include $_SERVER['DOCUMENT_ROOT'] . '/Telegram/Sender.php';
    if ($_FILES['flimage'] && $_FILES['flimage']['error'] == 0)
    {
        if ($_FILES["flimage"]["size"] < 10485760)
        {
            $valid_extensions = array('png', 'jpg', 'bmp');
            $fname = $_FILES['flimage']['name'];
            $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
            if (in_array($ext, $valid_extensions))
            {
                $flpath = $cfgrow['dldir'] . '/' . $fname;
                move_uploaded_file($_FILES['flimage']['tmp_name'], $flpath);
            }
            $url = 'https://arkan.network/downloads/' . $fname;
        }
    }
    if (isset($_REQUEST['send']))
    {
        $users = $db->getRecFrmQry('SELECT ar_mbrs.id,ar_mbrs.telegram_id,ar_mbrplans.subscription_end_date FROM ar_mbrs RIGHT JOIN ar_mbrplans on ar_mbrs.id = ar_mbrplans.idmbr where ar_mbrplans.subscription_end_date > now() and ar_mbrs.telegram_id!=0');
        if ($_FILES['flimage']['error'] == 0)
        {

            $text = $_REQUEST['messagetext'];
            $db->insert('messages', ['text' => $text, 'image' => $url, 'date' => date('Y-m-d H:i:s'), 'type' => 1]);
            $inner_id = $db->getPdo()->lastInsertId();
            foreach ($users as $item)
            {
                $telegram_id = $item['telegram_id'];
                $user_id = $item['id'];
                $message_id = sendMessageWithImage($telegram_id, $text, $url);
                if($message_id!=-1)
                $db->insert('telegram_sent_messages', ['message_id' => $message_id, 'inner_message_id' => $inner_id, 'user_id' => $user_id, 'telegram_id' => $telegram_id]);

            }
        }
        else
        {
            $text = $_REQUEST['messagetext'];
            $db->insert('messages', ['text' => $text, 'date' => date('Y-m-d H:i:s'), 'type' => 0]);
            $inner_id = $db->getPdo()->lastInsertId();
            foreach ($users as $item)
            {
                $telegram_id = $item['telegram_id'];
                $user_id = $item['id'];
                $message_id = sendMessage($telegram_id, $text);
                if($message_id!=-1)
                $db->insert('telegram_sent_messages', ['message_id' => $message_id, 'inner_message_id' => $inner_id, 'user_id' => $user_id, 'telegram_id' => $telegram_id]);
            }
        }
    }
    else if (isset($_REQUEST['edit']))
    {
        $text = $_REQUEST['messagetext'];
        $users = $db->getAllRecords('telegram_sent_messages', '*', 'and inner_message_id = (SELECT MAX(inner_message_id) FROM telegram_sent_messages)');
        $msg = $db->getAllRecords('messages', '*', 'and id = ' . $users[0]['inner_message_id'])[0];
        if ($msg['type'] == 0)
        {
            foreach ($users as $item)
            {
                $message_id = $item['message_id'];
                $chat_id = $item['telegram_id'];
                editMessage($chat_id, $text, $message_id);
            }
        }
        else
        {
            if (!isset($url))
            {
                $url = '';
            }
            foreach ($users as $item)
            {
                $message_id = $item['message_id'];
                $chat_id = $item['telegram_id'];
                editMessageCaption($chat_id, $text, $message_id, $url);
            }
        }
    }
    else if (isset($_REQUEST['delete']))
    {
        $text = $_REQUEST['messagetext'];
        $users = $db->getAllRecords('telegram_sent_messages', '*', 'and inner_message_id = (SELECT MAX(inner_message_id) FROM telegram_sent_messages)');
        foreach ($users as $item)
        {
            $message_id = $item['message_id'];
            $chat_id = $item['telegram_id'];
            deleteMessage($chat_id, $text, $message_id);
        }
    }
}


?>

<div class="section-header">
    <h1><i class="fa fa-comment"></i> Телеграмм</h1>
</div>

<div class="section-body">

    <form method="post" enctype="multipart/form-data">
        <div class="card card-primary">
            <div class="card-header">
                <h4>
                    <i class="fa fa-comment"></i> Отправить сообщение всем пользователям </h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Изображение</label>
                    <div class="input-group">
                        <input type="file" name="flimage" id="flimage" class="form-control" value="<?= DEFIMG_FILE; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="fldescr">Текст<span class="text-danger">*</span></label>
                    <textarea name="messagetext" class="form-control-range" rows="10" required></textarea>
                </div>
            </div>
            <div class="card-footer bg-whitesmoke">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="float-md-right">
                            <button type="submit" name="delete" value="delete" id="submit" class="btn btn-danger" formnovalidate><i class="far fa-fw fa-paper-plane"></i> Удалить крайнее сообщение
                            </button>
                            <button type="submit" name="edit" value="edit" id="submit" class="btn btn-warning"><i class="far fa-fw fa-paper-plane"></i> Отредактировать крайнее сообщение
                            </button>
                            <button type="submit" name="send" value="send" id="submit" class="btn btn-primary"><i class="far fa-fw fa-paper-plane"></i> Отправить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="hidden" name="hal" value="telegram">
    </form>

    <hr class="mt-4">

</div>

