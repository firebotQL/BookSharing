<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2009 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2009-Jul-21
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

if (!isset($user_sort) or $user_sort=='') $user_sort=$sortingTopics; // Sort messages default by last answer (0) desc OR 1 - by last new topics

if(isset($lastOut) and is_array($lastOut)){
foreach($lastOut as $l){
if(!in_array($l,$clForums)) $clForums[]=$l;
$clForumsUsers[$l]=array();
}
}

if (isset($clForumsUsers)) $closedForums=getAccess($clForums, $clForumsUsers, $user_id); else $closedForums='n';
if ($closedForums!='n') $xtr=getClForums($closedForums,'where','','forum_id','and','!='); else $xtr='';

$lPosts=array();
if ($user_sort==1) $orderBy='topic_id DESC'; else $orderBy='topic_last_post_id DESC';

$colls=array();
if($cols=db_simpleSelect(0, $Tt, 'topic_id, topic_title, topic_poster, topic_poster_name, topic_time, forum_id, posts_count, topic_last_post_id, topic_views, topic_last_post_time, topic_last_poster','','','',$orderBy,$viewlastdiscussions)){
do {
if(!isset($textLd)) $lPosts[]=$cols[7];
else { if($user_sort==0) $lPosts[]=$cols[7]; else $lPosts[]=$cols[0]; }
$colls[]=array($cols[0], $cols[1], $cols[2], $cols[3], $cols[4], $cols[5], $cols[6], $cols[7], $cols[8], $cols[9], $cols[10]);
}
while($cols=db_simpleSelect(1));
}

if(isset($textLd)) {

if(sizeof($lPosts)>0) {
if($user_sort==0) { $ordb='post_id'; $ordSql='DESC'; } else { $ordb='topic_id'; $ordSql='ASC'; }
$xtr=getClForums($lPosts,'where','',$ordb,'or','=');
}
else $xtr='';

if($xtr!=''){
if($row=db_simpleSelect(0, $Tp, 'poster_id, poster_name, post_time, topic_id, post_text, post_id', '', '', '', 'post_id '.$ordSql))
do 
if(!isset($pVals[$row[3]])) $pVals[$row[3]]=array($row[0],$row[1],$row[2],$row[4],$row[5]); else continue;
while($row=db_simpleSelect(1));
}

}

$list_topics='';

unset($result);

$i=1;
if(!isset($startPageModern) or !$startPageModern) $tpl=makeUp('main_last_discuss_cell'); else $tpl=makeUp('main_modern_lcell');

foreach($colls as $cols){

$forum=$cols[5];
$numReplies=$cols[6]; if($numReplies>=1) $numReplies-=1;

$topic=$cols[0];
$topic_views=$cols[8];
$topic_reverse='';
if(isset($themeDesc) and in_array($topic,$themeDesc)) $topic_reverse="<img src=\"{$main_url}/img/topic_reverse.gif\" style=\"vertical-align:middle\" alt=\"\" />&nbsp;";

if(!isset($preModerationType) or $preModerationType==0) $topic_title=$cols[1]; elseif($preModerationType>0 and isset($premodTopics) and in_array($cols[0], $premodTopics)) $topic_title=$l_topicQueued; else $topic_title=$cols[1];
if($topic_title=='') $topic_title=$l_emptyTopic;

if(isset($pVals[$topic][0])) $lastPosterID=$pVals[$topic][0]; else $lastPosterID='N/A';

if($numReplies>0 and isset($cols[10]) and $cols[10]!='') $lastPoster=$cols[10];
elseif($numReplies>0 and isset($pVals[$topic][1])) $lastPoster=$pVals[$topic][1];
else $lastPoster='&mdash;';

if($numReplies>0 and isset($cols[9])) $lastPostDate=convert_date($cols[9]);
elseif($numReplies>0 and isset($pVals[$topic][2])) $lastPostDate=convert_date($pVals[$topic][2]);
else $lastPostDate='';

if(isset($textLd) and isset($pVals[$topic][3])) {
$lptxt=($textLd==1?$pVals[$topic][3]:strip_tags(str_replace('<br />', ' ', $pVals[$topic][3])));
if(!isset($preModerationType) or $preModerationType==0) $lastPostText=$lptxt;
elseif($preModerationType>0 and isset($premodTopics) and in_array($cols[0], $premodTopics)) $lastPostText=$l_postQueued;
elseif($preModerationType>0 and isset($premodPosts) and in_array($pVals[$topic][4], $premodPosts)) $lastPostText='';
else $lastPostText=$lptxt;
}
else $lastPostText='N/A';

if($cols[3]=='') $cols[3]=$l_anonymous;
$topicAuthor=$cols[3];

if($i>0) $bg='tbCel1'; else $bg='tbCel2';

if(isset($mod_rewrite) and $mod_rewrite) {
$urlp=genTopicURL($main_url, $forum, $fTitle[$forum], $topic, $topic_title);
$urlForum=addForumURLPage(genForumURL($main_url, $forum, $fTitle[$forum]), PAGE1_OFFSET+1);
$urlType='Topic';
}
else{
$urlp="{$main_url}/{$indexphp}action=vthread&amp;forum=$forum&amp;topic=$topic";
$urlForum="{$main_url}/{$indexphp}action=vtopic&amp;forum=$forum";
$urlType='Gen';
}

$pageNavCell=pageNav(PAGE1_OFFSET+1,$numReplies+1,$urlp,$viewmaxreplys,TRUE,$urlType);

$whenPosted=convert_date($cols[4]);
if(trim($cols[1])=='') $cols[1]=$l_emptyTopic;

//Forum icon
if(isset($fIcon[$forum])) $forumIcon=$fIcon[$forum]; else $forumIcon='default.gif';

if(isset($mod_rewrite) and $mod_rewrite) {
$linkToTopic=addTopicURLPage(genTopicURL($main_url, $forum, $fTitle[$forum], $topic, $topic_title), PAGE1_OFFSET+1);
}
else $linkToTopic="{$main_url}/{$indexphp}action=vthread&amp;forum={$forum}&amp;topic={$topic}";

if(function_exists('parseTopic')) parseTopic();
$list_topics.=ParseTpl($tpl);

$i=-$i;
}
?>