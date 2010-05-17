<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2006 Paul Puzyrev, Sergei Larionov. www.minibb.net
Latest File Update: 2006-May-02
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

$usrid=(isset($_GET['usrid'])?$_GET['usrid']+0:0);

if ($topic!=0 and $usrid!=0 and $usrid==$user_id and $ids=db_simpleSelect(0,$Ts,'id','topic_id','=',$topic,'','','user_id','=',$user_id)) {
$topicU=$topic;

$op=db_delete($Ts,'id','=',$ids[0]);
if ($op>0) {
$errorMSG=$l_completed; $title.=$l_completed;
}
else {
$errorMSG=$l_itseemserror; $title.=$l_itseemserror;
}

}
else {
$title.=$l_accessDenied; $errorMSG=$l_accessDenied;
}

$correctErr='';
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
?>