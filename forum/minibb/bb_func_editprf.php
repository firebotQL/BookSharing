<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2007, 2009 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2009-Oct-22
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

if(isset($_GET['adminUser'])) $adminUser=$_GET['adminUser']+0; elseif(isset($_POST['adminUser'])) $adminUser=$_POST['adminUser']+0; else $adminUser=0;

$editedMod=FALSE;
foreach($mods as $k=>$v) if(in_array($adminUser,$v)) { $editedMod=TRUE; break; }

if($adminUser>1 and ( $user_id==1 or ($isMod==1 and $adminUser!=$user_id and !$editedMod) ) ) {
$tmpUser=$user_id;
if(!defined('ADMIN_USER_TMP')) define('ADMIN_USER_TMP', $adminUser);
$user_id=$adminUser;
$adminEditField='<input type="hidden" name="adminUser" value="'.$adminUser.'" />';
}
else $adminUser=0;

$pluginsFile=$pathToFiles.'bb_plugins_user.php';
$moDBValue=array(); $moDBValueTmp=array();

if ($user_id!=0) {

if (!isset($warning)) $warning='';
$l_fillRegisterForm='';
$editable='disabled="disabled"';
$userTitle=$l_editPrefs;
$l_passOnceAgain.=' <span class="txtSm">('.$l_onlyIfChangePwd.')</span>';
$actionName='editprefs';

if ($userData=db_simpleSelect(0,$Tu,'*',$dbUserId,'=',$user_id)) {

$profileLink="<br /><a href=\"{$main_url}/{$indexphp}action=userinfo&amp;user={$user_id}\">{$l_about} &ldquo;{$userData[$dbUserSheme['username'][0]]}&rdquo;</a>";

include($pathToFiles.'bb_func_inslng.php');
if(isset($_POST['user_viewemail'])) $user_viewemail=$_POST['user_viewemail']; else $user_viewemail=$userData[$dbUserSheme['user_viewemail'][0]];
$showemailDown=makeValuedDropDown(array(0=>$l_no,1=>$l_yes),'user_viewemail');
if(isset($_POST['user_sorttopics'])) $user_sorttopics=$_POST['user_sorttopics']; else $user_sorttopics=$userData[$dbUserSheme['user_sorttopics'][0]];
$sorttopicsDown=makeValuedDropDown(array(0=>$l_newAnswers,1=>$l_newTopics),'user_sorttopics');
if(!isset($_POST['language'])) $language=$userData[$dbUserSheme['language'][0]]; else $language=$_POST['language'];
$languageDown=makeValuedDropDown($glang,'language');

if ($step==1) {
require($pathToFiles.'bb_func_usrdat.php');
${$dbUserSheme['username'][1]}=$userData[$dbUserSheme['username'][0]];
${$dbUserSheme['username'][2]}=$userData[$dbUserSheme['username'][0]];

$act='upd';
require($pathToFiles.'bb_func_checkusr.php');

if ($rowp=db_simpleSelect(0,$Tu,$dbUserId,$caseComp."({$dbUserSheme['user_email'][1]})",'=',strtolower(${$dbUserSheme['user_email'][1]}),'','',$dbUserId,'!=',$user_id) or (strtolower(${$dbUserSheme['user_email'][1]})==strtolower($admin_email) and $user_id!=1)) $correct=4;


$prevVals=array();

foreach($dbUserSheme as $key=>$val) {
if(strstr($key,'user_custom')) $prevVals[$key]=$userData[$dbUserSheme[$key][0]];
}

if(file_exists($pluginsFile)) include($pluginsFile);

if ($correct==0) {
//Update db
$addFieldsGen=array('user_icq','user_website','user_occ','user_from','user_interest');

$upda=array($dbUserSheme['user_email'][1], $dbUserSheme['user_viewemail'][1], $dbUserSheme['user_sorttopics'][1], $dbUserSheme['language'][1]);

foreach($addFieldsGen as $k) if(isset($dbUserSheme[$k])) $upda[]=$dbUserSheme[$k][1];
foreach($dbUserSheme as $k=>$v) if(strstr($k,'user_custom') and isset($_POST[$v[2]]) and ($_POST[$v[2]]!='' OR (isset($prevVals[$k]) and $prevVals[$k]!='' and $_POST[$v[2]]=='' ) ) ) $upda[]=$v[1];

if($passwd!=''){
${$dbUserSheme['user_password'][1]}=writeUserPwd(${$dbUserSheme['user_password'][1]});
$upda[]=$dbUserSheme['user_password'][1];
}

/* sending confirm link on user's email if it's changed */
if(!defined('ADMIN_USER_TMP') and $user_id!=1 and $genEmailDisable!=1 and isset($closeRegister) and $closeRegister==1 and strtolower($userData[$dbUserSheme['user_email'][0]])!=strtolower(${$dbUserSheme['user_email'][2]})){
if(!isset($reply_to_email)) $reply_to_email=$admin_email;

//echo $userData[$dbUserSheme['user_email'][0]].' '.${$dbUserSheme['user_email'][2]};
$upda[]=$dbUserNk; $upda[]=$dbUserAct; ${$dbUserAct}=-1;
$$dbUserNk=substr(ereg_replace("[^0-9A-Za-z]", "A", writeUserPwd(uniqid(rand()))),0,10);
$confirmCode='email'.$$dbUserNk;
$loginName=${$dbUserSheme['username'][2]};

$lng=${$dbUserSheme['language'][2]};
if(!file_exists($pathToFiles.'templates/email_user_confirm_'.$lng.'.txt')) $lng=$langOrig;

$emailMsg=ParseTpl(makeUp('email_user_confirm_'.$lng));
$sub=explode('SUBJECT>>', $emailMsg); $sub=explode('<<', $sub[1]); $emailMsg=trim($sub[1]); $sub=$sub[0];
sendMail(${$dbUserSheme['user_email'][2]}, $sub, $emailMsg, $reply_to_email, $reply_to_email);

$warning.=$l_emailChangeCode.'<br />';

}
/* --sending ... */

//plugins...
foreach($moDBValue as $mk=>$mv){
if(in_array($mk, $upda) and isset($$mk)) {
$moDBValueTmp[$mk]=$$mk; $$mk=$mv;
}
}

$upd=updateArray($upda,$Tu,$dbUserId,$user_id);
if ($upd>0) {
$title.=$l_prefsUpdated;
$warning.=$l_prefsUpdated;
if (${$dbUserSheme['user_password'][2]}!='' and !defined('ADMIN_USER_TMP')) $warning.=$l_prefsPassUpdated;
}
else {
$title.=$l_editPrefs;
$warning.=$l_prefsNotUpdated;
}

}
else {
if (!isset($l_userErrors[$correct])) $l_userErrors[$correct]=$l_undefined;
$warning.=$l_errorUserData.": <span class=warning>{$l_userErrors[$correct]}</span>";
$title.=$l_errorUserData;
}

foreach($moDBValueTmp as $mk=>$mv) $$mk=$mv;

$tpl=makeUp('user_dataform');
if($user_id==1) $tpl=preg_replace("#<!--PASSWORD-->(.*?)<!--/PASSWORD-->#is",'',$tpl);
echo load_header(); echo ParseTpl($tpl); return;
}

else {
//step=0
foreach($dbUserSheme as $k=>$v){
$fk=$v[2];
if(isset($userData[$v[0]])) $$fk=$userData[$v[0]]; else $$fk='';
}
${$dbUserSheme['user_password'][2]}='';
$passwd2='';

if($pluginsFile) include($pluginsFile);
foreach($moDBValue as $mk=>$mv) $$mk=$mv;

$title.=$l_editPrefs;
$tpl=makeUp('user_dataform');
if($user_id==1) $tpl=preg_replace("#<!--PASSWORD-->(.*?)<!--/PASSWORD-->#is",'',$tpl);
echo load_header(); echo ParseTpl($tpl); return;
}

}
else {
$title.=$l_mysql_error; $errorMSG=$l_mysql_error; $correctErr='';
$tpl=makeUp('main_warning'); 
}

}
else {
$title.=$l_forbidden; $errorMSG=$l_forbidden; $correctErr='';
$tpl=makeUp('main_warning');
}

if(defined('ADMIN_USER_TMP')) $user_id=$tmpUser;

echo load_header(); echo ParseTpl($tpl); return;
?>