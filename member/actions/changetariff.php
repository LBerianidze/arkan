<?php
include_once('../../common/init.loader.php');

$seskey = verifylog_sess('member');
if ($seskey == '')
{
    exit;
}
extract($_POST);
if (!isset($type))
{
    exit();
}
$sesRow = getlog_sess($seskey);
$username = get_optionvals($sesRow['sesdata'], 'un');
// Get member details
$mbrstr = getmbrinfo($username, 'username');

if ($mbrstr['mppid'] >= $type)
{
    exit();
}
$payplans = $db->getAllRecords(DB_TBLPREFIX . '_payplans', '*');
$difference = $payplans[$type - 1]['regfee'] - $payplans[$mbrstr['mppid'] - 1]['regfee'];
$balance = $mbrstr['ewallet'];
if ($difference <= $balance)
{
    $db->update('ar_mbrs', array('ewallet' => $balance - $difference), array('id' => $mbrstr['id']));
    $subscribes = $db->getAllRecords(DB_TBLPREFIX . '_subscribeplans', '*');
    $prev = $subscribes[$mbrstr['subscription_id'] - 1];
    $new = $subscribes[$mbrstr['subscription_id'] + (($type - $mbrstr['mppid']) * 6) - 1];
    if ($mbrstr['subscription_active'] == 1)
    {
        $olddaycost = $prev['price'] / ($prev['months'] * 30);
        $newdaycost = $new['price'] / ($new['months'] * 30);
        $start_date = new DateTime($mbrstr['subscription_activation_date']);
        $end_date = new DateTime($mbrstr['subscription_end_date']);
        $now = new DateTime();
        //$start_date->setTime(0,0,0);
        //$end_date->setTime(0,0,0);
        //$now->setTime(0,0,0);

        $days = ($prev['months'] * 30) - ($end_date->diff($now)->format('%a'));
        //var_dump($days);
        $left = $prev['price'] - ($days * $olddaycost);
        $days = ceil($left / $newdaycost);
        if (is_nan($days))
        {
            $days = 0;
        }
        $newenddate = ($now->add(new DateInterval('P' . $days . 'D')))->format('Y-m-d H:i:s');
        $db->update('ar_mbrplans', array('mpstatus' => 1, 'mppid' => $type, 'reg_fee' => $payplans[$type - 1]['regfee'], 'subscription_end_date' => $newenddate, 'subscription_id' => $new['id']), array('idmbr' => $mbrstr['id']));
    }
    else
    {
        $db->update('ar_mbrplans', array('mpstatus' => 1, 'mppid' => $type, 'reg_fee' => $payplans[$type - 1]['regfee'], 'subscription_id' => $new['id']), array('idmbr' => $mbrstr['id']));

    }
    include '../../common/refferal.php';
    payRefferal($mbrstr['id'], $difference,"Бонус за апгрейд пакета рефералом");

    include_once '../../Telegram/Sender.php';
    sendMessage($mbrstr['telegram_id'], "С вашего баланса списано " . $difference . ' рублей за смену тарифа');

    $result = array();
    $result[0] = 1;
    $result[1] = '../member/index.php?hal=dashboard';
    exit(json_encode($result));
}
require_once "Payeer.php";
$config = array();
$config['m_shop'] = '1073201784';
$config['m_key'] = '2Fztj93wZ7LL';
$config['m_encryption_key'] = 'eQ2FDGnhHPYL';
$config['m_amount'] = $difference;
$config['m_curr'] = 'RUB';
$config['m_api_url'] = 'https://payeer.com/merchant/';
$config['m_orderid'] = 4234236;
$config['m_user_id'] = $mbrstr['id'];
$config['page'] = 'dashboard';
$payeer = new Payeer($config);
$form = $payeer->generateForm(3, json_encode(array('mppid' => $type)));
$result = array();
$result[0] = -1;
$result[1] = $form;
exit(json_encode($result));