<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2006 Paul Puzyrev, Sergei Larionov. www.minibb.net
Latest File Update: 2006-May-02
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

$confirmCode=(isset($_GET['confirmCode'])?htmlspecialchars($_GET['confirmCode'],ENT_QUOTES):'');

$confEmail=FALSE;
if(substr($confirmCode,0,5)=='email') { $confirmCode=substr($confirmCode,5,strlen($confirmCode)-1); $confEmail=TRUE; }

if ($confirmCode=='') {
$title.=$l_forbidden; $errorMSG=$l_forbidden; $correctErr='';
}
elseif($curr=db_simpleSelect(0,$Tu,$dbUserNp,$dbUserNk,'=',$confirmCode)) {

if($confEmail){
${$dbUserNk}=''; ${$dbUserNp}=''; ${$dbUserAct}=1;
$updArr=array($dbUserAct,$dbUserNk,$dbUserNp);
$fs=updateArray($updArr,$Tu,$dbUserNk,$confirmCode);
$mes=$l_emailCodeConfirm;
}
else{
${$dbUserSheme['user_password'][1]}=writeUserPwd($curr[0]); ${$dbUserNk}=''; ${$dbUserNp}='';
$updArr=array($dbUserSheme['user_password'][1],$dbUserNk,$dbUserNp);
$fs=updateArray($updArr,$Tu,$dbUserNk,$confirmCode);
$mes=$l_passwdUpdate;
}

if ($fs>0) {
$title.=$mes; $errorMSG=$mes; $correctErr='';
}
else {
$title.=$l_itseemserror; $errorMSG=$l_itseemserror; $correctErr='';
}
}
echo load_header(); echo ParseTpl(makeUp('main_warning'));
?>