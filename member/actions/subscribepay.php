<?php
include_once('../../common/init.loader.php');
use Longman\TelegramBot\Request;
$seskey = verifylog_sess('member');
if ($seskey == '') {
    exit;
}
extract($_POST);
if (!isset($type))
    exit();
$sesRow = getlog_sess($seskey);
$username = get_optionvals($sesRow['sesdata'], 'un');
// Get member details
$mbrstr = getmbrinfo($username, 'username');
if ($mbrstr['subscription_active'] == 1 && (new DateTime()) <= (new DateTime($mbrstr['subscription_end_date'])))
    exit();
$regfee = $subscribes[$type]['price'];
$balance = $mbrstr['ewallet'];
if ($regfee <= $balance) {
    $dt = new DateTime();
    $current = $dt->format('Y-m-d H:i:s');
    $dt->add(new DateInterval('P' . ($subscribes[$type]['months'] * 30) . 'D'));
    $end = $dt->format('Y-m-d H:i:s');

    $db->update('ar_mbrs', array('ewallet' => $balance - $regfee), array('id' => $mbrstr['id']));
    $db->update('ar_mbrplans', array('subscription_active' => 1, 'subscription_activation_date' => $current, 'subscription_end_date' => $end,'subscription_id' => $subscribes[$type]['id']), array('idmbr' => $mbrstr['id']));
    include '../../Telegram/Sender.php';
    Request::sendMessage(['chat_id' => $mbrstr['telegram_id'], 'text' => "С вашего баланса списано " . $regfee . ' рублей за активацию абонентской платы на '.$subscribes[$type]['months'].' месяцев']);
    include '../../common/refferal.php';
    payRefferal($mbrstr['id'], $regfee,"Бонус за активацию абонентской платы рефералом ");

    $result = array();
    $result[0] = 1;
    $result[1] = '../member/index.php?hal=dashboard#subscribeplanprice_span';
    exit(json_encode($result));
}
require_once "Payeer.php";
$config = array();
$config['m_shop'] = '1073201784';
$config['m_key'] = '2Fztj93wZ7LL';
$config['m_encryption_key'] = 'eQ2FDGnhHPYL';
$config['m_amount'] = $regfee;
$config['m_curr'] = 'RUB';
$config['m_api_url'] = 'https://payeer.com/merchant/';
$config['m_orderid'] = 4234236;
$config['m_user_id'] = $mbrstr['id'];
$config['page'] = 'dashboard';
$payeer = new Payeer($config);
$form = $payeer->generateForm(2,json_encode(array('sbid' => $subscribes[$type]['id'])));
$result = array();
$result[0] = -1;
$result[1] = $form;
exit(json_encode($result));