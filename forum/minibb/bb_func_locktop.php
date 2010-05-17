<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2006 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2006-May-19
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

if(!isset($_GET['chstat'])) die('Fatal error.'); else $topic_status=$_GET['chstat'];
$errorMSG=$l_forbidden; $correctErr=$backErrorLink;
if($userUnlock==2 and !($user_id==1 or $isMod==1)) $topic=-1;

if($tD=db_simpleSelect(0,$Tt,'topic_status, topic_poster, sticky','topic_id','=',$topic)){
if (($tD[1]==$user_id and $tD[2]!=1 and (($topic_status==0 and $userUnlock==1) or $topic_status==1)) OR $logged_admin==1 OR $isMod==1) {
if(updateArray(array('topic_status'),$Tt,'topic_id',$topic)>0) $errorMSG=(($topic_status==1)?$l_topicLocked:$l_topicUnLocked);
else $errorMSG=$l_itseemserror;

if(isset($mod_rewrite) and $mod_rewrite) $furl=addTopicURLPage(genTopicURL($main_url, $forum, '#GET#', $topic, '#GET#'), PAGE1_OFFSET+1); else $furl="{$main_url}/{$indexphp}action=vthread&amp;forum=$forum&amp;topic=$topic";

$correctErr="<a href=\"{$furl}\">$l_back</a>";
}
}

$title.=$errorMSG;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
?>