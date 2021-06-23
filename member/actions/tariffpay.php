<?php
include_once('../../common/init.loader.php');
$seskey = verifylog_sess('member');
if ($seskey == '')
{
    exit;
}
extract($FORM);
if (!isset($type))
{
    exit();
}
$sesRow = getlog_sess($seskey);
$username = get_optionvals($sesRow['sesdata'], 'un');
// Get member details
$mbrstr = getmbrinfo($username, 'username');

$regfee = $payplans[$type - 1]['regfee'];
$balance = $mbrstr['ewallet'];
if ($regfee <= $balance)
{
    $db->update('ar_mbrs', array('ewallet' => $balance - $regfee), array('id' => $mbrstr['id']));
    $db->update('ar_mbrplans', array('mpstatus' => 1), array('idmbr' => $mbrstr['id']));


    $dt = new DateTime();
    $current = $dt->format('Y-m-d H:i:s');
    $dt->add(new DateInterval('P30D'));
    $end = $dt->format('Y-m-d H:i:s');
    $db->update('ar_mbrplans', array('mppid' => $type, 'reg_fee' => $regfee, 'subscription_active' => 1, 'subscription_activation_date' => $current, 'subscription_end_date' => $end, 'subscription_id' => (($type - 1) * 6) + 1), array('idmbr' => $mbrstr['id']));

    include '../../common/refferal.php';
    payRefferal($mbrstr['id'], $regfee,"Бонус за активацию пакета рефералом");

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
$config['m_amount'] = $payplans[$type - 1]['regfee'];
$config['m_curr'] = 'RUB';
$config['m_api_url'] = 'https://payeer.com/merchant/';
$config['m_orderid'] = 4234236;
$config['m_user_id'] = $mbrstr['id'];
$config['page'] = 'dashboard';
$payeer = new Payeer($config);
$form = $payeer->generateForm(1, json_encode(array("plan" => $type)));
$result = array();
$result[0] = -1;
$result[1] = $form;
exit(json_encode($result));