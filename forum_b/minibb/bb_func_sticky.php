<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004 Paul Puzyrev, Sergei Larionov. www.minibb.net
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

if(!isset($_GET['chstat'])) die('Fatal error.'); else $sticky=$_GET['chstat']+0;

if ($logged_admin==1 or $isMod==1) {

if(updateArray(array('sticky'),$Tt,'topic_id',$topic)>0) $errorMSG=(($sticky>0)?$l_topicSticked:$l_topicUnsticked);
else $errorMSG=$l_itseemserror;
$correctErr="<a href=\"{$main_url}/{$indexphp}action=vthread&amp;forum=$forum&amp;topic=$topic\">$l_back</a>";
}
else {
$errorMSG=$l_forbidden; $correctErr=$backErrorLink;
}

$title.=$errorMSG;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
?>