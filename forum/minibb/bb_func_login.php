<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2007 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2007-May-19
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

$queryStr2=(isset($_POST['queryStr2'])?$_POST['queryStr2']:'');

$cook='';

if(isset($_POST['user_usr'])) $user_usr=htmlspecialchars(trim($_POST['user_usr']),ENT_QUOTES); else $user_usr='';
if(isset($_POST['user_pwd'])) $user_pwd=htmlspecialchars(trim($_POST['user_pwd']),ENT_QUOTES); else $user_pwd='';

$queryStr=rawurldecode($queryStr);
if(strstr($queryStr,'vtopic')) $queryStr.='#newtopic';
elseif(strstr($queryStr,'vthread')) $queryStr.='#newreply';
if(($action=='pthread' and isset($_POST['postText']) and $_POST['postText']=='') OR ($action=='ptopic' and isset($_POST['postText']) and $_POST['postText']=='')) $action='';

/* */
$urlp="{$indexphp}{$queryStr}";

if(isset($mod_rewrite) and $mod_rewrite) {

$urlp=preg_replace("@".str_replace('?', '\?', $indexphp)."action=vthread&forum=([0-9]+)&topic=([0-9]+)&page=([-0-9]+)&mdrw=on(#newreply)?@", addTopicURLPage(genTopicURL($main_url, "\\1", '#GET#', "\\2", '#GET#'), "\\3")."\\4", $urlp);

$urlp=preg_replace("@".str_replace('?', '\?', $indexphp)."action=vtopic&forum=([0-9]+)&page=([-0-9]+)&mdrw=on(#newtopic)?@", addForumURLPage(genForumURL($main_url, "\\1", '#GET#'), "\\2")."\\3", $urlp);

$urlp=preg_replace("@".str_replace('?', '\?', $indexphp)."mdrw=on@", $main_url.'/'.$startIndex, $urlp);

}
if($urlp==$indexphp) $urlp=$main_url.'/'.$startIndex;
/* */

if(strlen($admin_pwd)==32) $user_pwd_cmp=writeUserPwd($user_pwd); else $user_pwd_cmp=$user_pwd;

if ($user_usr==$admin_usr OR (isset($loginsCase) and $loginsCase and strtolower($user_usr)==strtolower($admin_usr)) ) {
if ($user_pwd_cmp==$admin_pwd) {
$logged_admin=1;
$cook=$admin_usr."|".writeUserPwd($user_pwd)."|".$cookieexptime;
deleteMyCookie();
setMyCookie($admin_usr,$user_pwd,$cookieexptime);
setCSRFCheckCookie();

if ($action=='') {
if(isset($metaLocation)) {
$meta_relocate="{$main_url}/{$indexphp}{$queryStr}"; echo ParseTpl(makeUp($metaLocation)); exit;
}
elseif($queryStr2!='') {
header("Location: {$queryStr2}"); exit;
}
else {
header("Location: {$urlp}");
}
}

}
else {
include ($pathToFiles."lang/{$lang}.php");
$errorMSG=$l_loginpasswordincorrect;
$title.=$l_errorUserData;

if(!isset($_POST['postText'])){
$loginError=1;
$correctErr="<a href=\"JavaScript:history.back(-1)\">$l_correctLoginpassword</a>";
}
else{
$loginError=2;
$displayFormElements=array('userData'=>1, 'topicTitle'=>1, 'postText'=>1);
$antiWarn=$l_fixData;
include($pathToFiles.'bb_func_posthold.php');
}


}
// if this is not admin, this is anonymous or registered user; check registered first
}
else {
if(isset($loginsCase) and $loginsCase) { $caseComp1=$caseComp.'('; $caseComp2=')'; } else { $caseComp1=''; $caseComp2=''; }

if($row=db_simpleSelect(FALSE,$Tu,$dbUserSheme['username'][1].','.$dbUserSheme['user_password'][1],$caseComp1.$dbUserSheme['username'][1].$caseComp2,'=',$user_usr,'',1))
{
// It means that username exists in database; so let's check a password
$username=$row[0]; $userpassword=$row[1];
if ( ($user_usr==$username OR (isset($loginsCase) and $loginsCase and strtolower($user_usr)==strtolower($username))) and $userpassword==writeUserPwd($user_pwd)) 
{
$logged_user=1;
$cook=$username."|".writeUserPwd($user_pwd)."|".$cookieexptime;
deleteMyCookie();
setMyCookie($username,$user_pwd,$cookieexptime);
setCSRFCheckCookie();

if ($action==''){
if(isset($metaLocation)) {
$meta_relocate="{$main_url}/{$indexphp}{$queryStr}"; echo ParseTpl(makeUp($metaLocation)); exit;
}
elseif($queryStr2!='') {
header("Location: {$queryStr2}"); exit;
}
else {
header("Location: {$urlp}");
}
}

}
else {
include ($pathToFiles."lang/{$lang}.php");
$errorMSG=$l_loginpasswordincorrect;
$title.=$l_errorUserData;

if(!isset($_POST['postText'])){
$loginError=1;
$correctErr="<a href=\"JavaScript:history.back(-1)\">$l_correctLoginpassword</a>";
}
else{
$loginError=2;
$displayFormElements=array('userData'=>1, 'topicTitle'=>1, 'postText'=>1);
$antiWarn=$l_fixData;
include($pathToFiles.'bb_func_posthold.php');
}

}
}
else {
// There are now rows - this is Anonymous
if(in_array($forum,$regUsrForums) or isset($allForumsReg) and $allForumsReg){
include ($pathToFiles."lang/{$lang}.php");
$errorMSG=$l_signIn;
$title.=$l_forbidden;
$loginError=2;

$displayFormElements=array('userData'=>1, 'topicTitle'=>1, 'postText'=>1);
$antiWarn=$l_fixData;
include($pathToFiles.'bb_func_posthold.php');

return;
}
else{
require_once($pathToFiles.'bb_func_txt.php');$reqTxt=1;
if(get_magic_quotes_gpc()==1) $user_usr=stripslashes($user_usr);
$user_usr=str_replace(array('|', '&amp;#'), array('', '&#'), $user_usr);
$user_usr=special_substr($user_usr, 35);
$user_usr=wrapText(15,$user_usr);
$fake=0;
if(isset($disallowNames) and is_array($disallowNames)) { foreach($disallowNames as $dn) if(strtolower($user_usr)==strtolower($dn)) { $fake=1; include ($pathToFiles."lang/{$lang}.php"); $user_usr=$l_anonymous; break; } }
if($fake==0){
if(isset($disallowNamesIndex) and is_array($disallowNamesIndex)) { foreach($disallowNamesIndex as $dn) if(substr_count(strtolower($user_usr),strtolower($dn))>0) { include ($pathToFiles."lang/{$lang}.php"); $user_usr=$l_anonymous; break; } }
}
if (isset($_COOKIE[$cookiename])) {
$cookievalue=explode ("|", $_COOKIE[$cookiename]);
$user_usrOLD=$cookievalue[0];
} else { $user_usrOLD=''; }
if ($user_usr != $user_usrOLD) {
// We don't need to set a cookie if the same 'anonymous name' specified
$cook=$user_usr.'||'.$cookieexptime;
deleteMyCookie();
setMyCookie($user_usr,'',$cookieexptime);
}
}
}
}

?>