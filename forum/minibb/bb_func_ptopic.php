<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2007 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2007-May-19
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

$allowForm=($user_id==1 or $isMod==1);
$c1=(in_array($forum,$clForums) and isset($clForumsUsers[$forum]) and !in_array($user_id,$clForumsUsers[$forum]) and !$allowForm);
$c2=(isset($allForumsReg) and $allForumsReg and $user_id==0);
$c3=(isset($poForums) and in_array($forum, $poForums) and !$allowForm);
$c4=(isset($roForums) and in_array($forum, $roForums) and !$allowForm);
$c5=(isset($regUsrForums) and in_array($forum, $regUsrForums) and $user_id==0);

if ($c1 or $c2 or $c3 or $c4 or $c5) {
$errorMSG=$l_forbidden; $correctErr=$backErrorLink;
$title=$title.$l_forbidden; $loginError=1;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}

if (!$user_usr) $user_usr=$l_anonymous;

if(!isset($post_text_minlength)) $post_text_minlength=10;

if(!isset($_POST['disbbcode']) or (isset($_POST['disbbcode']) and $_POST['disbbcode']=='') ) $disbbcode=FALSE; else $disbbcode=TRUE;
$postText_tmp=textFilter($_POST['postText'],$post_text_maxlength,$post_word_maxlength,1,$disbbcode,1,$user_id);
$compareTL=strlen(trim(strip_tags($postText_tmp)));
$sce=FALSE; if(isset($simpleCodes)) foreach($simpleCodes as $e) { if(substr_count($postText_tmp, $e)>0) { $sce=TRUE; break; } }

if( ($compareTL==0 or ($compareTL>0 and $compareTL<$post_text_minlength)) and !$sce) {
$errorMSG=$l_emptyPost; $correctErr=$backErrorLink;
$title.=$l_forbidden; $loginError=1;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}

if(isset($_POST['topicTitle'])) $TT=trim($_POST['topicTitle']); else $TT='';
if(strlen($TT)>0 and strlen($TT)<$post_text_minlength) $TT='';

if ($TT=='' and trim($_POST['postText'])=='') {
$action='vtopic'; return;
}
elseif ($TT=='' or $TT=='#GET#'){
$errorMSG=$l_topiccannotempty; $correctErr=$backErrorLink;
$title.=$l_topiccannotempty;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}
else {
$TT=str_replace(array('&#032;', '&#32;'), '', $TT);
$topicTitle=textFilter($TT,$topic_max_length,$post_word_maxlength,0,1,0,$user_id,255);
}

$poster_ip=getIP();

if(db_simpleSelect(0,$Tf,'forum_id','forum_id','=',$forum)) {

if($postRange==0) $antiSpam=0; else {
if($user_id==0) $fields=array('poster_ip',$poster_ip); else $fields=array('poster_id',$user_id);
if($asTime=db_simpleSelect(0,$Tp,'post_time',$fields[0],'=',$fields[1],'post_id DESC','1')) {
$asTime=time()-strtotime($asTime[0]); if($asTime<=$postRange) $antiSpam=1; else $antiSpam=0;
}
else $antiSpam=0;
}

if ( ($user_id==1 or $isMod==1) or $antiSpam==0) {
$topic_title=$topicTitle;
$topic_poster=$user_id;
$topic_poster_name=$user_usr;
$topic_time=date('Y-m-d H:i:s');
if(!defined('TOPIC_TIME')) define('TOPIC_TIME', $topic_time);
$forum_id=$forum;
$topic_status=0;
$topic_last_post_id=0;
$posts_count=0;
$dll=insertArray(array('topic_title','topic_poster','topic_poster_name','topic_time','forum_id','topic_status','topic_last_post_id','posts_count'),$Tt);
if($dll==0) {
$topic=$insres;
db_calcAmount($Tt,'forum_id',$forum,$Tf,'topics_count');
if($user_id!=0) db_calcAmount($Tt,'topic_poster',$user_id,$Tu,$dbUserSheme['num_topics'][1],$dbUserId);
require($pathToFiles.'bb_func_pthread.php');
}
else {
$errorMSG=$l_mysql_error; $correctErr=$backErrorLink;
$title.=$l_mysql_error; $loginError=1;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}
}
else {
$errorMSG=$l_antiSpam;
//$correctErr=$backErrorLink;
$title.=$l_antiSpam; $loginError=1;

$displayFormElements=array('topicTitle'=>1, 'postText'=>1);
$antiWarn=$l_antiSpamWait;
$antiSpam=1;
include($pathToFiles.'bb_func_posthold.php');

echo load_header(); echo ParseTpl(makeUp('main_posthold')); return;
}

}
else {
$errorMSG=$l_forbidden; $correctErr=$backErrorLink;
$title.=$l_forbidden; $loginError=1;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}
?>