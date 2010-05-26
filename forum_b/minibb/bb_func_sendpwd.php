<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004, 2007 Paul Puzyrev, Sergei Larionov. www.minibb.net
Latest File Update: 2007-Apr-26
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

if (!isset($genEmailDisable) or $genEmailDisable!=1){

$newPasswd=''; $confirmCode='';

$email=(isset($_POST['email'])?htmlspecialchars(trim($_POST['email']),ENT_QUOTES):'');

if ($email==$admin_email) $email='';

if ($step!=1) {
$title.=$l_sub_pass;
echo load_header(); echo ParseTpl(makeUp('tools_send_password')); return;
}
else {

if (!($updId=db_simpleSelect(0,$Tu,"{$dbUserId},{$dbUserSheme['language'][1]},{$dbUserSheme['username'][1]}",$dbUserSheme['user_email'][1],'=',$email))) {
$title.=$l_emailNotExists;
$errorMSG=$l_emailNotExists;
$correctErr=$backErrorLink;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}
else {
$ulang=$updId[1];
$loginName=$updId[2];

${$dbUserNp}=substr(ereg_replace("[^0-9A-Za-z]", "A", writeUserPwd(uniqid(rand()))),0,8); $newPasswd=${$dbUserNp};
${$dbUserNk}=substr(md5(uniqid(rand())),0,32); $confirmCode=${$dbUserNk};

$updArr=array($dbUserNp,$dbUserNk);
$fs=updateArray($updArr,$Tu,$dbUserId,$updId[0]);

if ($fs>0) {
if($emailusers==2 and file_exists($pathToFiles.'templates/email_user_password_'.$ulang.'.txt')) $langS=$ulang; else $langS=$langOrig;
$msg=ParseTpl(makeUp('email_user_password_'.$langS));
$sub=explode('SUBJECT>>', $msg); $sub=explode('<<', $sub[1]); $msg=trim($sub[1]); $sub=$sub[0];
if(!isset($reply_to_email)) $reply_to_email=$admin_email;
sendMail($email, $sub, $msg, $reply_to_email, $reply_to_email);
$title.=$l_emailSent;
$errorMSG=$l_emailSent;
$correctErr='';
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}
else {
$title.=$l_itseemserror;
$errorMSG=$l_itseemserror;
$correctErr='';
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}

}

}

}
else {
$title.=$l_accessDenied;
$errorMSG=$l_accessDenied;
$correctErr='';
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}
?>