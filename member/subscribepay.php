<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$row = $db->getAllRecords(DB_TBLPREFIX . '_paygates', '*', ' AND pgidmbr = "' . $mbrstr['id'] . '"');
$mbrpaystr = array();
foreach ($row as $value) {
    $mbrpaystr = array_merge($mbrpaystr, $value);
}

$wdvarval = $cfgrow['wdrawfee'];
$wdvarvalarr = explode('|', $wdvarval);
$fval = (strpos($wdvarvalarr[0], '%') !== false) ? $wdvarvalarr[0] / 100 : $wdvarvalarr[0];
$fval = number_format((float)$fval, 2);
$fcapval = number_format((float)$wdvarvalarr[1], 2);

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    if ($txpaytype != '' && $txamount > 0 && $txamount <= $mbrstr['ewallet']) {
        $redirto = $_SESSION['redirto'];
        $_SESSION['redirto'] = '';

        // apply fee
        $txamountval = $txamount;
        $txwdrfee = $txamountfee = 0;
        if ($fval > 0) {
            $txwdrfee = $txamount * $fval;
            $txamountfee = ($fcapval <= $txwdrfee) ? $fcapval : $txwdrfee;
            $txamountval = $txamount - number_format($txamountfee, 2);
        }

        // deduct wallet
        $ewallet = $mbrstr['ewallet'] - $txamount;
        $data = array(
            'ewallet' => $ewallet,
        );
        $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrstr['id']));

        // add withdraw request
        $paybyopt = $avalwithdrawgate_array[$txpaytype];
        $txadminfo = "Payout To [{$paybyopt}]: ";
        $txadminfo .= base64_decode($mbrpaystr[$txpaytype]);
        $txdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $data = array(
            'txdatetm' => $txdatetm,
            'txpaytype' => $txpaytype,
            'txfromid' => $mbrstr['id'],
            'txtoid' => 0,
            'txamount' => $txamountval,
            'txmemo' => $LANG['g_withdrawstr'],
            'txppid' => $mbrstr['mppid'],
            'txtoken' => "|WIDR:OUT|, |WDRTXFEE:{$txamountfee}|",
            'txstatus' => 0,
            'txadminfo' => $txadminfo,
        );
        $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);

        if ($insert) {
            $newtrxid = $db->lastInsertId();
            if ($txamountfee > 0) {
                $txdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
                $txlogtime = date('mdH-is-' . $newtrxid, time() + (3600 * $cfgrow['time_offset']));
                $data = array(
                    'txdatetm' => $txdatetm,
                    'txpaytype' => $txpaytype,
                    'txfromid' => $mbrstr['id'],
                    'txtoid' => 0,
                    'txamount' => $txamountfee,
                    'txmemo' => $LANG['g_withdrawfee'],
                    'txppid' => $mbrstr['mppid'],
                    'txtoken' => "|WDRTXID:{$newtrxid}|, |NOTE:" . base64_encode("WDRID-{$txlogtime}") . "|",
                    'txstatus' => 1,
                );
                $insertrx = $db->insert(DB_TBLPREFIX . '_transactions', $data);
            }

            $_SESSION['dotoaster'] = "toastr.success('Запрос на вывод средств был успешно отправлен!', 'Успешно');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Запрос на вывод средств не выполнен <strong> Пожалуйста, попробуйте еще раз!</strong>', 'Предупреждение');";
        }
    } else if ($txamount <= 0) {
        $_SESSION['dotoaster'] = "toastr.warning('Запрос на вывод средств не выполнен <strong> Неверная сумма!</strong>', 'Ошибка');";
    } else if ($txpaytype == '') {
        $_SESSION['dotoaster'] = "toastr.warning('Запрос на снятие не выполнен <strong> Аккаунт получателя недоступен!</strong>', 'Ошибка');";
    } else {
        $_SESSION['dotoaster'] = "toastr.error('Запрос на вывод средств не выполнен <strong> Недостаточно средств!</strong>', 'Ошибка');";
    }

    redirpageto('index.php?hal=withdrawreq');
    exit;
}

if ($mbrstr['ewallet'] < 0) {
    $balanceclor = ' text-danger';
} elseif ($mbrstr['ewallet'] > 0) {
    $balanceclor = ' text-info';
}

$btnwidrdis = ($mbrstr['ewallet'] <= 0) ? " disabled" : '';

$condition = " AND txtoken LIKE '%|WIDR:%' AND txtoid = '0' AND txfromid = '{$mbrstr['id']}'";
$withdrawlist = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . " LIMIT 12");
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-hand-holding-usd"></i> <?php echo myvalidate($LANG['g_withdrawreq']); ?></h1>
</div>

<div class="section-body">

    <form method="post" action="index.php">
        <input type="hidden" name="hal" value="withdrawreq">
        <div class="card card-primary">
            <div class="card-header">
                <h4>
                    <?php echo myvalidate($LANG['g_balance']); ?> <span class="<?php echo myvalidate($balanceclor); ?>"><?php echo myvalidate($bpprow['currencysym'] . $mbrstr['ewallet']); ?></span> <?php echo myvalidate($bpprow['currencycode']); ?>
                </h4>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 float-md-right">
                        <?php echo myvalidate($LANG['g_withdrawstatusinfo']); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><?php echo myvalidate($LANG['g_account']); ?></span>
                                </div>
                                <select name='txpaytype' class="custom-select" id="inputGroupSelect05" required="">
                                    <option value="" disabled="" selected>-</option>
                                    <?php
                                    if ($payrow['paypal4usr'] == 1) {
                                        ?>
                                        <option value="paypalacc">PayPal (<?php echo myvalidate($mbrpaystr['paypalacc']) ? base64_decode($mbrpaystr['paypalacc']) : '?'; ?>)</option>
                                        <?php
                                    }
                                    if ($payrow['coinpayments4usr'] == 1) {
                                        ?>
                                        <option value="coinpaymentsmercid">Bitcoin (<?php echo myvalidate($mbrpaystr['coinpaymentsmercid']) ? base64_decode($mbrpaystr['coinpaymentsmercid']) : '?'; ?>)</option>
                                        <?php
                                    }
                                    if ($payrow['manualpay4usr'] == 1) {
                                        ?>
                                        <option value="manualpayipn"><?php echo myvalidate($payrow['manualpayname']); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><?php echo myvalidate($LANG['m_withdrawamount']); ?></span>
                                </div>
                                <input type="number" id="txamount" name="txamount" min="0" step="0.5" class="form-control" onChange="dowithdrawfee('<?php echo myvalidate($fval); ?>', '<?php echo myvalidate($fcapval); ?>', '<?php echo myvalidate($bpprow['currencysym']); ?>');" placeholder="0.00" required="">
                            </div>
                            <h6 class="text-muted text-small">
                                <span class="badge badge-info float-right" id="txamountstr2"></span>
                                <span class="badge badge-info float-right" id="txamountstr1"></span>
                            </h6>
                        </div>

                        <div class="float-md-right mt-4">
                            <a href="index.php?hal=withdrawreq" class="btn btn-danger"><i class="fa fa-fw fa-redo"></i> Clear</a>
                            <button type="submit" name="submit" value="withdraw" id="submit" class="btn btn-primary"<?php echo myvalidate($btnwidrdis); ?>><i class="fa fa-fw fa-donate"></i> Withdraw</button>
                        </div>

                    </div>

                </div>
            </div>
            <div class="card-footer bg-whitesmoke">
                <div class="row">
                    <div class="col-sm-12">
                        <?php echo myvalidate($LANG['m_withdrawreqnote']); ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="hidden" name="dosubmit" value="1">
    </form>

    <div class="row">
        <?php
        if (count($withdrawlist) > 0) {
            $numwdr = 0;
            foreach ($withdrawlist as $val) {
                if ($val['txamount'] <= 0) {
                    continue;
                }
                $paybyoptico = $avalpaygateicon_array[$val['txpaytype']];

                $headdtbg = 'bg-primary text-light';
                $statusbadge = '';
                switch ($val['txstatus']) {
                    case "1":
                        $headdtbg = 'bg-light';
                        $statusbadge .= "<span class='badge badge-secondary'>В обработке</span>";
                        break;
                    case "2":
                        $statusbadge .= "<span class='badge badge-info'>Исполнен</span>";
                        break;
                    default:
                        $statusbadge .= "<span class='badge badge-light'>В ожидании</span>";
                }
                ?>

                <div class="col-12 col-md-4 col-lg-4">
                    <div class="pricing">
                        <div class="pricing-title <?php echo myvalidate($headdtbg); ?>">
                            <?php echo formatdate($val['txdatetm'], 'dt'); ?>
                        </div>
                        <div class="pricing-padding">
                            <span class="pricing-price"><?php echo myvalidate($paybyoptico); ?></span>
                            <div class="pricing-price">
                                <h4><?php echo myvalidate($bpprow['currencysym'] . $val['txamount'] . ' ' . $bpprow['currencycode']); ?></h4>
                            </div>
                            <?php echo myvalidate($statusbadge); ?>
                        </div>
                    </div>
                </div>

                <?php
                $numwdr++;
            }
            if ($numwdr < 1) {
                echo "Записей не найдено!";
            }
        }
        ?>
    </div>

</div>

