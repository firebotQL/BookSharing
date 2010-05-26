<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2006, 2008 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2008-May-21
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

/* Get list of moderators */
$modsForums=array();
if(isset($mods) and sizeof($mods)>0){
$mds=array();
foreach($mods as $md) if(is_array($md)) foreach($md as $us) if(!in_array($us,$mds)) $mds[]=$us;
if(sizeof($mds)>0){
$xtr=getClForums($mds,'where','',$dbUserId,'or','=');
if ($row=db_simpleSelect(0,$Tu,$dbUserSheme['username'][1].','.$dbUserId,'','','',$dbUserId)){
do $modsForums[$row[1]]="<a href=\"{$main_url}/{$indexphp}action=userinfo&amp;user={$row[1]}\">{$row[0]}</a>"; while ($row=db_simpleSelect(1));
}
}
unset($xtr);
}

/* Forums */

$keyAr=0;
$list_forums='';

if($cols=db_simpleSelect(0,$Tf,'forum_id, forum_name, forum_desc, forum_icon, topics_count, posts_count, forum_group','','','','forum_order')){
$i=1;
if(!isset($startPageModern) or !$startPageModern) {
$tpl=makeUp('main_forums_cell'); $tplg=makeUp('main_forumgroup');
}
else{
$tpl=makeUp('main_modern_fcell'); $tplg=makeUp('main_modern_stitle');
}
do{
$forum=$cols[0];

if($user_id!=1 and isset($clForums) and in_array($forum,$clForums) and isset($clForumsUsers[$forum]) and !in_array($user_id,$clForumsUsers[$forum])) $show=FALSE; else $show=TRUE;

if($show){

if($i>0) $bg='tbCel1'; else $bg='tbCel2';

$forum_title=str_replace('"', '&quot;', $cols[1]);
$forum_desc=$cols[2];
$forum_icon=$cols[3];
$fIcon[$forum]=$cols[3];
$fTitle[$forum]=$cols[1];
$numTopics=$cols[4];
$numPosts=$cols[5];
$numPosts=$numPosts-$numTopics;

$moderatorsList='';
if(isset($mods[$forum]) and is_array($mods[$forum])){
foreach($mods[$forum] as $ms) if(isset($modsForums[$ms])) $moderatorsList.=$modsForums[$ms].', ';
if($moderatorsList!='') $moderatorsList='<br /><span class="txtSm">'.$l_moderatorsAre.': '.substr($moderatorsList,0,strlen($moderatorsList)-2).'</span>';
}

if ($cols[6]!=''){
$list_forums.=ParseTpl($tplg);
$keyAr++;
}

if(isset($mod_rewrite) and $mod_rewrite) $linkToForums=addForumURLPage(genForumURL($main_url, $forum, $forum_title), PAGE1_OFFSET+1); else $linkToForums="{$main_url}/{$indexphp}action=vtopic&amp;forum={$forum}";

$list_forums.=ParseTpl($tpl);
$i=-$i;
}

}
while($cols=db_simpleSelect(1));
unset($result);unset($countRes);

$title=$sitename;
echo load_header();
if(!isset($startPageModern) or !$startPageModern) echo ParseTpl(makeUp('main_forums'));
}
else{
$errorMSG=$l_stillNoForums; $l_returntoforums=''; $correctErr='';
echo load_header(); echo ParseTpl(makeUp('main_warning'));
}
?>