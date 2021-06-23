<?php
include_once('../../common/init.loader.php');
$seskey = verifylog_sess('member');
if ($seskey == '')
{
    exit;
}
$sesRow = getlog_sess($seskey);
$username = get_optionvals($sesRow['sesdata'], 'un');
// Get member details
$mbrstr = getmbrinfo($username, 'username');


mt_rand();
ob_start();
$time = system('date +%s%N');
ob_clean();

$telegram_hash = md5('OQ1&{-;Mlz-e8C$S0a,H(50=*`u6P*' . $mbrstr['username'] . $time . 'U4SF]K>Px9T!1)DkMeDd@]HhRlY9J4');
$db->update('ar_mbrs', array('telegram_hash' => $telegram_hash, 'telegram_id' => 0), array('id' => $mbrstr['id']));
$db->update('telegram_db', array('step' => 1), array('telegram_id' => $mbrstr['telegram_id']));

include $_SERVER['DOCUMENT_ROOT'] . '/Telegram/Sender.php';
sendMessage($mbrstr['telegram_id'], "Ваш ключ телеграмм был изменен в личном кабинете. Пожалуйста отправьте новый ключ");

$result = array();
$result[0] = 1;
$result[1] = $telegram_hash;
exit(json_encode($result));