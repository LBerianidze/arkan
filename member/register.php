<?php
include_once('../common/init.loader.php');

$page_header = $LANG['g_registration'];
include('../common/pub.header.php');

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1')
{
    extract($FORM);

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    $firstname = mystriptag($firstname);
    $lastname = mystriptag($lastname);
    $username = mystriptag($username, 'user');
    $email = mystriptag($email, 'email');

    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidinput']);
        $redirval = "?res=errinstr";
        redirpageto($redirval);
        exit;
    }

    $isrecapv3 = 1;
    if ($cfgrow['isrecaptcha'] == 1 && isset($FORM['g-recaptcha-response']))
    {
        $secret = $cfgrow['rc_securekey'];
        $response = $FORM['g-recaptcha-response'];
        $remoteIp = $_SERVER['REMOTE_ADDR'];
        // call curl to POST request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secret, 'response' => $response, 'remoteip' => $remoteIp), '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $arrResponse = json_decode($response, true);

        // verify the response
        if ($arrResponse["success"] == '1' && $arrResponse["score"] >= 0.5)
        {
            // valid submission
        }
        else
        {
            $isrecapv3 = 0;
        }
    }

    // if new username exist, keep using old username
    $condition = ' AND username LIKE "' . $username . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition . "");

    if ($isrecapv3 == 0)
    {
        $_SESSION['show_msg'] = showalert('warning', '????????????!', '?????????? ???? ??????????????????. ???????????????????? ?????? ??????!');
        $redirval = "?res=rcapt";
    }
    elseif (count($sql) > 0)
    {
        $_SESSION['show_msg'] = showalert('danger', '????????????!', '?????? ???????????????????????? ?????? ????????????????????!');
        $redirval = "?res=exist";
    }
    else
    {

        if (!dumbtoken($dumbtoken))
        {
            $_SESSION['show_msg'] = showalert('danger', '????????????!', $LANG['g_invalidtoken']);
            $redirval = "?res=errtoken";
            redirpageto($redirval);
            exit;
        }

        $in_date = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));

        $password = filter_var($password, FILTER_SANITIZE_STRING);
        $passwordconfirm = filter_var($passwordconfirm, FILTER_SANITIZE_STRING);

        $passres = passmeter($password);
        if ($password != $passwordconfirm)
        {
            $_SESSION['show_msg'] = showalert('danger', '???????????? ???? ??????????????????', '?????? ???????????? ???????????? ???????? ??????????????????????. ????????????????????, ???????????????????? ?????? ??????!');
            $redirval = "?res=errpass";
        }
        elseif ($passres == 1)
        {
            $log_ip = get_userip();
            $country = get_countrycode($log_ip);
            $password = getpasshash($password);
            $telegram_hash = md5('OQ1&{-;Mlz-e8C$S0a,H(50=*`u6P*' . $username . 'U4SF]K>Px9T!1)DkMeDd@]HhRlY9J4');
            $data = array('in_date' => $in_date, 'firstname' => $firstname, 'lastname' => $lastname, 'username' => $username, 'email' => $email, 'password' => $password, 'log_ip' => $log_ip, 'country' => $country, 'telegram_hash' => $telegram_hash,'carat_refresh_month'=>date('n'));
            $insert = $db->insert(DB_TBLPREFIX . '_mbrs', $data);
            $newmbrid = $db->lastInsertId();

            $_SESSION['firstname'] = $_SESSION['lastname'] = $_SESSION['username'] = $_SESSION['email'] = '';

            if ($insert)
            {
                require_once('../common/mailer.do.php');

                // send welcome email
                $cntaddarr['fullname'] = $firstname . ' ' . $lastname;
                $cntaddarr['login_url'] = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;
                $cntaddarr['rawpassword'] = $passwordconfirm;
                delivermail('mbr_reg', $newmbrid, $cntaddarr);

                if ($cfgtoken['isautoregplan'] == 1)
                {
                    // register to membership
                    $mbrstr = getmbrinfo($newmbrid);
                    regmbrplans($mbrstr, $sesref['mpid'], $bpprow['ppid']);
                }

                addlog_sess($username, 'member');
                $redirval = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;
            }
            else
            {
                $redirval = "?res=errsql";
            }
        }
        else
        {
            $_SESSION['show_msg'] = showalert('warning', '?????????????????? ????????????', $passres);
            $redirval = "?res=errpass";
        }
    }
    redirpageto($redirval);
    exit;
}

$modalcontent = file_get_contents(INSTALL_PATH . "/common/terms.html");
$refbystr = ($sesref['username'] != '') ? "<div class='card-header-action'><span class='badge badge-info'>| {$sesref['username']}</span></div>" : '';

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';
?>
    <section class="section">
        <div class="container mt-4">
            <div class="row">
                <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
                    <div class="login-brand">
                        <img src="<?php echo myvalidate($site_logo); ?>" alt="logo" width="100"
                             class="shadow-light rounded-circle">
                        <div><?php echo myvalidate($cfgrow['site_name']); ?></div>
                    </div>

                    <?php echo myvalidate($show_msg); ?>

                    <div class="card card-primary">
                        <div class="card-header">
                            <h4><?php echo myvalidate($LANG['g_register']); ?></h4>
                            <?php echo myvalidate($refbystr); ?>
                        </div>

                        <div class="card-body">
                            <?php
                            if ($cfgrow['join_status'] != 1)
                            {
                                echo showalert('danger', '??????!', '?? ?????????? ?? ?????????????????????????? ??????????????, ?? ?????????????????? ?????????? ???? ???? ?????????????????? ?????????? ??????????????????????!');
                            }
                            elseif ($cfgrow['validref'] == 1 && $sesref['id'] < 1)
                            {
                                echo showalert('warning', '??????!', '???? ???? ???????????? ???????????????????????????????????? ?????? ?????????????????????? ????????????????!');
                            }
                            else
                            {
                                if ($cfgrow['isrecaptcha'] == 1)
                                {
                                    echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
                                }
                                ?>
                                <form method="POST" class="needs-validation" id="regmbrform">
                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="firstname"><?php echo myvalidate($LANG['g_firstname']); ?></label>
                                            <input id="firstname" type="text" class="form-control" name="firstname"
                                                   value="<?php echo myvalidate($_SESSION['firstname']); ?>"
                                                   minlength="3" autofocus required>
                                            <div class="invalid-feedback">
                                                ?????????????????? ???????? ??????
                                            </div>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="lastname"><?php echo myvalidate($LANG['g_lastname']); ?></label>
                                            <input id="lastname" type="text" class="form-control" name="lastname"
                                                   value="<?php echo myvalidate($_SESSION['lastname']); ?>"
                                                   minlength="3" required>
                                            <div class="invalid-feedback">
                                                ?????????????????? ???????? ??????????????
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="username">?????? ???????????????????????? <span
                                                        id="resultGetMbr"></span></label>
                                            <input id="username" type="text" class="form-control" name="username"
                                                   value="<?php echo myvalidate($_SESSION['username']); ?>"
                                                   minlength="4" maxlength="16"
                                                   onBlur="checkMember('unex', this.value, '')" required>
                                            <div class="invalid-feedback">
                                                ????????????????????, ???????????????? ???????? ?????? ????????????????????????
                                            </div>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="email">??????????</label>
                                            <input id="email" type="email" class="form-control" name="email"
                                                   value="<?php echo myvalidate($_SESSION['email']); ?>" minlength="8"
                                                   required>
                                            <div class="invalid-feedback">
                                                ????????????????????, ?????????????????? ?????????? ?????????? ??????????????????
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="password" class="d-block">????????????</label>
                                            <input id="password" type="password" class="form-control"
                                                   data-indicator="pwindicator" name="password" required>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="passwordconfirm" class="d-block">???????????? ?????? ??????</label>
                                            <input id="password2" type="password" class="form-control"
                                                   name="passwordconfirm">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="agree" class="custom-control-input" id="agree"
                                                   required
                                                   data-validation-required-message="???? ???????????? ???????????????????? ?? ??????????????????">
                                            <label class="custom-control-label"
                                                   for="agree"><?php echo myvalidate($LANG['g_agreeterms']); ?><a
                                                        href="javascript:;" data-toggle="modal"
                                                        data-target="#myModalterm"><i
                                                            class="fas fa-fw fa-question-circle"></i></a></label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button data-sitekey="<?php echo myvalidate($cfgrow['rc_sitekey']); ?>"
                                                data-callback='onSubmit'
                                                class="btn btn-primary btn-lg btn-block g-recaptcha">
                                            ????????????????????????????????????
                                        </button>
                                        <input type="hidden" name="dosubmit" value="1">
                                        <input type="hidden" name="dumbtoken"
                                               value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
                                    </div>
                                </form>
                                <?php
                                if ($cfgrow['isrecaptcha'] == 1)
                                {
                                    $isrecaptcha_content = <<<INI_HTML
                                    <script type="text/javascript">
                                        function onSubmit(token) {
                                            if(document.getElementsByName('agree')[0].checked == false)
                                                {
                                                    document.getElementsByName('agree')[0].reportValidity();
                                                    return;
                                                }
                                            document.getElementById('regmbrform').submit();
                                        }
                                    </script>
INI_HTML;
                                    echo myvalidate($isrecaptcha_content);
                                }
                            }
                            ?>
                            <div class="mt-4 text-muted text-center">
                                <?php echo myvalidate($LANG['g_haveacc']); ?> <a href="login.php">??????????</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="myModalterm" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo myvalidate($LANG['g_termscon']); ?></h5>
                </div>
                <div class="modal-body">
                    <div class="text-muted"><?php echo myvalidate($modalcontent); ?></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">??????????????</button>
                </div>
            </div>
        </div>
    </div>

<?php
$_SESSION['firstname'] = $_SESSION['lastname'] = $_SESSION['username'] = $_SESSION['email'] = '';
include('../common/pub.footer.php');
