<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$LANG = array();

// Translation Details
$translation_str = 'English';
$translation_author = 'AmazeGo';
$translation_version = '1.3.0';
$translation_update = '3';
$translation_stamp = '2020-06-22 11:18';

// Character encoding, example: utf-8, iso-8859-1
$LANG['lang_iso'] = "en";
$LANG['lang_charset'] = "utf-8";

// ----------------
// Array of Language
// ----------------
$LANG['g_pagenotfound'] = "Page not found!";
$LANG['g_continue'] = "Continue";
$LANG['g_registration'] = "Registration";
$LANG['g_register'] = "Register";
$LANG['g_agreeterms'] = "I agree with the site terms";
$LANG['g_termscon'] = "Terms and Conditions";
$LANG['g_haveacc'] = "Have an account?";
$LANG['g_donothaveacc'] = "Do not have an account?";
$LANG['g_createacc'] = "Create One";
$LANG['g_forgotpass'] = "Forgot Password";
$LANG['g_forgotpassresetlink'] = "We will send a link to reset your password";
$LANG['g_resetpass'] = "Reset Password";
$LANG['g_name'] = "Name";
$LANG['g_firstname'] = "First Name";
$LANG['g_lastname'] = "Last Name";
$LANG['g_email'] = "Email";
$LANG['g_username'] = "Username";
$LANG['g_dashboard'] = "Dashboard";
$LANG['g_admincp'] = "Admin CP";
$LANG['g_admincpinit'] = "ACP";
$LANG['g_membercp'] = "Member CP";
$LANG['g_membercpinit'] = "MCP";
$LANG['g_rememberme'] = "Remember Me";
$LANG['g_successlogout'] = "You have been successfully logged out.";
$LANG['g_invalidtoken'] = "Invalid token, please try it again!";
$LANG['g_invalidinput'] = "Invalid input format, please try it again!";
$LANG['g_dashboardtitle'] = "Dashboard";
$LANG['g_accoverview'] = "Account Overview";
$LANG['g_referrallist'] = "Referral List";
$LANG['g_referrals'] = "Referrals";
$LANG['g_historylist'] = "Transaction History";
$LANG['g_withdrawreq'] = "Withdrawal Request";
$LANG['g_withdrawstr'] = "Withdrawal Request";
$LANG['g_withdrawfee'] = "Withdrawal Fee";
$LANG['g_findreferral'] = "Find Referral";
$LANG['g_addreferral'] = "Add Referral";
$LANG['g_memberprofile'] = "Member Profile";
$LANG['g_findhistory'] = "Find History";
$LANG['g_point'] = "Point";
$LANG['g_hits'] = "Hits";
$LANG['g_earning'] = "Earning";
$LANG['g_registered'] = "Registered";
$LANG['g_refurl'] = "Referral URL";
$LANG['g_mysponsor'] = "My Sponsor";
$LANG['g_recentref'] = "Recent Referrals";
$LANG['g_performance'] = "Performance";
$LANG['g_membership'] = "Membership";
$LANG['g_transactionid'] = "Transaction ID";
$LANG['g_description'] = "Description";
$LANG['g_keyword'] = "Keyword";
$LANG['g_balance'] = "Balance";
$LANG['g_account'] = "Account";
$LANG['g_content'] = "Content";
$LANG['g_all'] = "All";
$LANG['g_activeonly'] = "Active Only";
$LANG['g_withdrawstatusinfo'] = "<blockquote><p><strong>Pending</strong>: The request has been sent but is not yet processed. <strong>Verified</strong>: The request has passed verification. <strong>Processing</strong>: The request is being processed. Once processed, the funds will be sent to your account.</p></blockquote>";

$LANG['a_managemember'] = "Manage Member";
$LANG['a_findmember'] = "Find Member";
$LANG['a_historylist'] = "Transaction History";
$LANG['a_withdrawlist'] = "Withdraw Request";
$LANG['a_genealogylist'] = "Member Genealogy";
$LANG['a_getstart'] = "Getting Started";
$LANG['a_digifile'] = "Digital Product";
$LANG['a_digicontent'] = "Digital Content";
$LANG['a_termscon'] = "Terms Conditions";
$LANG['a_notifylist'] = "Notification List";
$LANG['a_settings'] = "General Settings";
$LANG['a_payplan'] = "Payplan Settings";
$LANG['a_payment'] = "Payment Options";
$LANG['a_languagelist'] = "Manage Language";
$LANG['a_updates'] = "Maintenance";

$LANG['m_getstarted'] = "Getting Started";
$LANG['m_genealogyview'] = "Genealogy View";
$LANG['m_digiload'] = "Download";
$LANG['m_digiview'] = "Page Content";
$LANG['m_planpay'] = "Payment";
$LANG['m_planreg'] = "Upgrade";
$LANG['m_profilecfg'] = "Profile";
$LANG['m_feedback'] = "Feedback";
$LANG['m_userlist'] = "Referral";
$LANG['m_historylist'] = "Transaction";
$LANG['m_withdrawreq'] = "?????????????? ????????????";
$LANG['m_withdrawamount'] = "Amount to withdraw";
$LANG['m_genealogyview'] = "Genealogy";
$LANG['m_membergenealogy'] = "Member Genealogy";
$LANG['m_nofile'] = "We couldn't find any file";
$LANG['m_nofilenote'] = "Sorry, we can't find any downloadable file for you :(";
$LANG['m_withdrawreqnote'] = "You are allowed to submit withdrawal request once a time!";
$LANG['m_clicklefttocnt'] = "Please click the page menu on the left to display the content!";
$LANG['m_profileaccnote'] = "Please complete the forms below, make sure the value you entered is valid.";
$LANG['m_profilepaynote'] = "Member account settings.";
$LANG['m_profilewebnote'] = "Please enter your website details below (optional).";
$LANG['m_profilepaynote'] = "Member account settings.";
$LANG['m_profilepassnote'] = "Update your password using forms below. Leave empty to keep the current password.";
$LANG['m_confirmpass'] = "Confirm Password Change";
$LANG['m_feedbacknote'] = "Use the following form for any questions, support request, or feature suggestion.";
$LANG['m_payoption'] = "Payment Option";
$LANG['m_payinfo'] = "Please complete your payment by clicking the Make Payment button from the available payment option below.";
$LANG['m_testpayinfo'] = "Click the button below to simulate the payment process!";
$LANG['m_notice'] = "Notice!";
$LANG['m_noticereg'] = "You are not registered to";
$LANG['m_noticepay'] = "Your account is not active, please complete the payment.";
$LANG['m_noticerepay'] = "You have an outstanding payment, please complete the payment.";
$LANG['m_ipnthanks'] = "Thank You";
$LANG['m_ipnthanksverify'] = "Please wait a few moments to verify your payment.";
$LANG['m_ipnnextbtn'] = "Continue";
$LANG['m_ibconversion'] = "Conversion";
$LANG['m_ibpersonal'] = "Personal";
$LANG['m_ibwallet'] = "Wallet";
$LANG['m_registeredsince'] = "Registered Since";
