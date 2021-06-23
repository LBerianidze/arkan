<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$tempsess = $_SESSION;

$fsubject = base64_decode($tempsess['fsubject']);
$fmessage = base64_decode($tempsess['fmessage']);
$fmsgtypearr = array(0, 1, 2);
$fmsgtype_cek = radiobox_opt($fmsgtypearr, $tempsess['fmsgtype']);

if ($FORM['isconfirm'] != '') {
    // transaction
    $isconfirm = base64_decode($FORM['isconfirm']);
    $txmpid = explode('-', $isconfirm);
    $txid = $txmpid[0];
    $mpid = $txmpid[1];
    $trxrow = array();
    $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', ' AND txid = "' . $txid . '"');
    foreach ($row as $value) {
        $trxrow = array_merge($trxrow, $value);
    }
    $amount = $bpprow['currencysym'] . $trxrow['txamount'] . ' ' . $bpprow['currencycode'] . " ({$mbrstr['username']}-{$txid})";

    $fsubject = 'Подтверждение об оплате: ' . $amount;
    $fmessage = 'Оплата успешно зачислена';
}

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    if (!defined('ISDEMOMODE') && $fsubject && $fmessage) {
        if (!dumbtoken($dumbtoken)) {
            $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
            $redirval = "?res=errtoken";
            redirpageto($redirval);
            exit;
        }

        require(INSTALL_PATH . '/assets/fellow/phpmailer/Exception.php');
        require(INSTALL_PATH . '/assets/fellow/phpmailer/PHPMailer.php');

        if ($fmsgtype != 9) {
            $_SESSION['fsubject'] = base64_encode($fsubject);
            $_SESSION['fmessage'] = base64_encode($fmessage);
        }
        $_SESSION['fmsgtype'] = $fmsgtype;
        $_SESSION['fmsgtime'] = time();

        switch ($fmsgtype) {
            case "1":
                $fmsgtype = "Запрос в техподдержку";
                break;
            case "2":
                $fmsgtype = "Обратная связь или предложение";
                break;
            case "9":
                $fmsgtype = "Подтверждение об оплате";
                break;
            default:
                $fmsgtype = "Общие вопросы";
        }

        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        $fsubject = mystriptag($fsubject);
        $fmessage = mystriptag($fmessage);

        $fmessageadd = "{$fmsgtype}:
            Name: {$mbrstr['firstname']} {$mbrstr['lastname']} ({$mbrstr['email']})
            Username: {$mbrstr['username']}

            ";
        $fmessage = $fmessageadd . $fmessage;

        $fmessagehtml = nl2br($fmessage);

        try {
            //Set who the message is to be sent from
            $mail->setFrom($cfgrow['site_emailaddr'], $cfgrow['site_emailname']);
            //Set an alternative reply-to address
            //$mail->addReplyTo($cfgrow['site_emailaddr'], $cfgrow['site_emailname']);
            //Set who the message is to be sent to
            $mail->addAddress($cfgrow['site_emailaddr'], $cfgrow['site_emailname']);
            //Set the subject line
            $mail->Subject = $fsubject;
            //Replace the plain text body with one created manually
            $mail->Body = $fmessagehtml;
            $mail->AltBody = $fmessage;
            $mail->isHTML(TRUE);
            //Attach a file
            if ($_FILES["fattfile"]["name"]) {
                $extfile = array("zip", "rar", "gif", "jpg", "png");
                $faextension = pathinfo($_FILES["fattfile"]["name"], PATHINFO_EXTENSION);
                $isFileExtension = ( (in_array($faextension, $extfile)) ? true : false );
                if ($isFileExtension) {
                    $mail->addAttachment($_FILES["fattfile"]["tmp_name"], $_FILES["fattfile"]["name"]);
                }
            }
            //send the message, check for errors
            if (!$mail->send()) {
                $_SESSION['dotoaster'] = "toastr.error('Mailer Error ({$fsubject}): {$mail->ErrorInfo}. Пожалуйста, свяжитесь с вашим хостинг-провайдером для помощи!', 'Предупреждение');";
            } else {
                $_SESSION['dotoaster'] = "toastr.success('Сообщение отправлено ({$fsubject})!', 'Успешно');";

                $extimgfile = array("gif", "jpg", "png");
                $isImgFileExtension = ( (in_array($faextension, $extimgfile)) ? true : false );
                if ($txid > 0 && $isImgFileExtension) {
                    $proofimg = 'proofimg' . $txid;
                    do_imgresize($proofimg, $_FILES["fattfile"]["tmp_name"], 720, 0, 'jpeg');
                    $txrow = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'txtoken', ' AND txid="' . $txid . '"');
                    $txtoken = put_optionvals($txrow[0]['txtoken'], 'proofimg', $proofimg . '.jpg');
                    $data = array(
                        'txtoken' => $txtoken,
                    );
                    $update = $db->update(DB_TBLPREFIX . '_transactions', $data, array('txid' => $txid));
                }
            }
        } catch (Exception $e) {
            $_SESSION['dotoaster'] = "toastr.error('Сообщение ({$fsubject}) не отправлено. Ошибка доставщика: {$mail->ErrorInfo}. Пожалуйста, свяжитесь с вашим хостинг-провайдером для помощи!', 'Предупреждение');";
        }
    }

    //header('location: index.php?hal=' . $hal);
    redirpageto('index.php?hal=' . $hal);
    exit;
}

// less than five minutes ago
if ($_SESSION['fsubject'] != '' && $_SESSION['fmsgtime'] > time() - 60 * 5) {
    $btnsendaval = " disabled";
} else {
    $_SESSION['fsubject'] = $_SESSION['fmessage'] = $_SESSION['fmsgtype'] = $_SESSION['fmsgtime'] = '';
    $btnsendaval = '';
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-life-ring"></i> <?php echo myvalidate($LANG['m_feedback']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Техподдержка</h4>
                </div>
                <div class="card-body">
                    <div class="chocolat-parent">
                        <div>
                            <img alt="image" src="<?php echo myvalidate($site_logo); ?>" class="img-fluid rounded-circle img-shadow author-box-picture">
                        </div>
                    </div>
                    <div class="mb-2 text-muted"><?php echo isset($cfgrow['site_descr']) ? $tempsess['site_descr'] : ''; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" enctype="multipart/form-data" id="fbackform">
                    <input type="hidden" name="hal" value="feedback">

                    <div class="card-header">
                        <h4>Форма для связи</h4>
                    </div>

                    <div class="card-body">
                        <div class="tab-content no-padding" id="myTab2Content">
                            <div class="tab-pane fade show active">
                                <p class="text-muted"><?php echo myvalidate($LANG['m_feedbacknote']); ?></p>

                                <div class="form-group">
                                    <label for="fsubject">Тема</label>
                                    <input type="text" name="fsubject" id="fsubject" class="form-control" value="<?php echo isset($fsubject) ? $fsubject : ''; ?>" placeholder="Тема сообщения" required>
                                </div>

                                <div class="form-group">
                                    <label for="fmessage">Сообщение</label>
                                    <textarea class="form-control rowsize-lg" name="fmessage" id="fmessage" placeholder="Вопросы, запрос в службу поддержки или предложение" required><?php echo isset($fmessage) ? $fmessage : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="fattfile">Вложение (<?php echo isset($txid) ? 'подтверждение платежа: изображение в формате gif, jpg или png ':' архив zip или rar, или изображение gif, jpg, png'; ?>)</label>
                                    <input type="file" name="fattfile" id="fattfile" class="form-control">
                                    <div class="form-text text-muted">Файл должен быть размером максимум 1Mb</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="selectgroup-pills">Тип сообщения</label>
                                <div class="selectgroup selectgroup-pills">
                                    <?php
                                    if ($FORM['isconfirm'] != '') {
                                        ?>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="fmsgtype" value="9" class="selectgroup-input" checked="checked">
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-question-circle"></i> Подтверждение об оплате</span>
                                        </label>
                                        <?php
                                    } else {
                                        ?>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="fmsgtype" value="0" class="selectgroup-input"<?php echo myvalidate($fmsgtype_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-question-circle"></i> Общие вопросы</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="fmsgtype" value="1" class="selectgroup-input"<?php echo myvalidate($fmsgtype_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-hands-helping"></i> Запрос в техподдержку</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="fmsgtype" value="2" class="selectgroup-input"<?php echo myvalidate($fmsgtype_cek[2]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-comment-medical"></i> Обратная связь или предложение</span>
                                        </label>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer bg-whitesmoke text-md-right">
                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                            <i class="fa fa-fw fa-undo"></i> Очистить
                        </button>
                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary"<?php echo myvalidate($btnsendaval); ?>>
                            <i class="fa fa-fw fa-plus-circle"></i> Отправить
                        </button>
                        <input type="hidden" name="dosubmit" value="1">
                        <input type="hidden" name="txid" value="<?php echo myvalidate($txid); ?>">
                        <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
</div>
