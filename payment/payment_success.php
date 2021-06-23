<?php

use Longman\TelegramBot\Request;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !in_array($_SERVER['REMOTE_ADDR'], array('185.71.65.92', '185.71.65.189', '149.202.17.210')))
{
    return;
}

if (isset($_POST['m_operation_id']) && isset($_POST['m_sign']))
{
    include_once('../common/config.php');
    include_once('../common/db.class.php');

    $dsn = "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . "";
    $pdo = "";
    try
    {
        $pdo = new PDO($dsn, base64_decode(DB_USER), base64_decode(DB_PASSWORD));
    } catch (PDOException $e)
    {
    }
    $db = new Database($pdo);

    $arHash = array($_POST['m_operation_id'], $_POST['m_operation_ps'], $_POST['m_operation_date'], $_POST['m_operation_pay_date'], $_POST['m_shop'], $_POST['m_orderid'], $_POST['m_amount'], $_POST['m_curr'], $_POST['m_desc'], $_POST['m_status']);
    if (isset($_POST['m_params']))
    {
        $arHash[] = $_POST['m_params'];
    }
    $arHash[] = '2Fztj93wZ7LL';
    $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
    if ($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success')
    {
        include '../Telegram/Sender.php';
        include '../common/refferal.php';
        $payment = $db->getAllRecords('ar_payeer', '*', 'and `order_id`=' . $_POST['m_orderid'], '', 'limit 1');
        if ($payment[0]['type'] == 0)//пополнение баланса
        {
            $mbrstr = $db->getAllRecords('ar_mbrs', 'ewallet,telegram_id', 'and `id`=' . $payment[0]['member_id'], '', 'limit 1')[0];
            $val = $mbrstr['ewallet'] + $payment[0]['amount'];
            $db->update('ar_mbrs', array('ewallet' => $val), array('id' => $payment[0]['member_id']));
            Request::sendMessage(['chat_id' => $mbrstr['telegram_id'], 'text' => "Ваш баланс пополнен на " . $payment[0]['amount'] . ' рублей и составляет ' . $val . ' рублей']);
        }
        else if ($payment[0]['type'] == 1)//покупка тарифа
        {
            $params = json_decode($payment[0]['params'], true);
            $mbrstr = $db->getAllRecords('ar_mbrs', 'ewallet,telegram_id', 'and `id`=' . $payment[0]['member_id'], '', 'limit 1')[0];
            $sbid = (($params['plan'] - 1) * 6) + 1;

            $dt = new DateTime();
            $current = $dt->format('Y-m-d H:i:s');
            $dt->add(new DateInterval('P30D'));
            $end = $dt->format('Y-m-d H:i:s');


            $db->update('ar_mbrplans', array('mpstatus' => 1, 'mppid' => $params['plan'],'subscription_active' => 1, 'subscription_activation_date' => $current, 'subscription_end_date' => $end, 'subscription_id' => $sbid), array('idmbr' => $payment[0]['member_id']));
            Request::sendMessage(['chat_id' => $mbrstr['telegram_id'], 'text' => "Тариф активирован "]);
            payRefferal($payment[0]['member_id'], $payment[0]['amount'],"Бонус за активацию тарифа рефералом");
        }
        else if ($payment[0]['type'] == 2) //покупка абонентской платы
        {
            $params = json_decode($payment[0]['params'], true);
            $subscribe = $db->getAllRecords(DB_TBLPREFIX . '_subscribeplans', '*', ' AND id = "' . $params['sbid']/*$didId*/ . '"')[0];

            $dt = new DateTime();
            $current = $dt->format('Y-m-d H:i:s');
            $dt->add(new DateInterval('P' . ($subscribe['months'] * 30) . 'D'));
            $end = $dt->format('Y-m-d H:i:s');

            $db->update('ar_mbrplans', array('subscription_active' => 1, 'subscription_activation_date' => $current, 'subscription_end_date' => $end, 'subscription_id' => $params['sbid']), array('idmbr' => $payment[0]['member_id']));
            payRefferal($payment[0]['member_id'], $payment[0]['amount'],"Бонус за активацию абонентской платы рефералом");
        }
        else if ($payment[0]['type'] == 3)//смена тарифа
        {
            $params = json_decode($payment[0]['params'], true);
            $type = $params['mppid'];
            $subscribes = $db->getAllRecords(DB_TBLPREFIX . '_subscribeplans', '*');

            $prev = $subscribes[$mbrstr['subscription_id'] - 1];
            $new = $subscribes[$mbrstr['subscription_id'] + (($type - $mbrstr['mppid']) * 6) - 1];
            $olddaycost = $prev['price'] / ($prev['months'] * 30);
            $newdaycost = $new['price'] / ($new['months'] * 30);
            $start_date = new DateTime($mbrstr['subscription_activation_date']);
            $end_date = new DateTime($mbrstr['subscription_end_end']);
            $now = new DateTime();
            $days = $now->diff($start_date)->format('%a');
            $left = $prev['price'] - ($days * $olddaycost);
            $days = ceil($left / $newdaycost);
            $newenddate = ($now->add(new DateInterval('P' . $days . 'D')))->format('Y-m-d H:i:s');
            $db->update('ar_mbrplans', array('mpstatus' => 1, 'mppid' => $type, 'reg_fee' => $payplans[$type - 1]['regfee'], 'subscription_end_date' => $newenddate, 'subscription_id' => $new['id']), array('idmbr' => $mbrstr['id']));
            payRefferal($payment[0]['member_id'], $payment[0]['amount'],"Бонус за апгрейд тарифа рефелом");

        }
        $db->update('ar_payeer', array('status' => 1, 'complete_date' => (new DateTime())->format('yy-m-d H:i:s')), array('order_id' => $_POST['m_orderid']));
        ob_end_clean();
        exit($_POST['m_orderid'] . '|success');
    }
    ob_end_clean();
    exit($_POST['m_orderid'] . '|error');
}
else
{
    header('Location: /member/index.php?hal=fillbalance');
}
?>