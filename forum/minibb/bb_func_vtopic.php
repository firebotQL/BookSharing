<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2008 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2009-Oct-12
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

$list_topics='';
$pageNav='';
$forumsList='';

if(!isset($_GET['showSep'])||$_GET['showSep']==2){
$st=1; $frm=$forum;
include ($pathToFiles.'bb_func_forums.php');
}

if (!isset($forumsArray[$forum])) {
$errorMSG=$l_forumnotexists; $correctErr=$backErrorLink;
$title=$title.$l_forumnotexists;
$metaRobots='NOINDEX,NOFOLLOW';
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}

$forumName=$forumsArray[$forum][0]; $forumIcon=$forumsArray[$forum][1]; $forum_desc=$forumsArray[$forum][2];
$description=substr(strip_tags($forum_desc),0,400);

if($user_sort=='') $user_sort=$sortingTopics; /* Sort messages default by last answer (0) desc OR 1 - by last new topics */

/* Redirect to the proper URL if needed */
if((!isset($_GET['showSep'])||$_GET['showSep']==2) and $user_sort==0 and !isset($_GET['sortBy'])){
$requestUrl=parseRequestForumURL($main_url);
if(isset($mod_rewrite) and $mod_rewrite) $origUrl=addForumURLPage(genForumURL($main_url, $forum, $forumName), $page);
else $origUrl=addGenURLPage("{$main_url}/{$indexphp}action=vtopic&forum={$forum}", $page, '&');

if($requestUrl!=$origUrl){
header("HTTP/1.1 301 Moved Permanently");
if(isset($metaLocation)) { $meta_relocate="{$origUrl}"; echo ParseTpl(makeUp($metaLocation)); exit; }
else{ header("Location: {$origUrl}"); exit; }
}
}
/* --Redirect... */

$warn='';
if(!isset($_GET['showSep'])||$_GET['showSep']==2){

$numRows=$forumsArray[$forum][3];

if($numRows==0){
$errorMSG=$l_noTopicsInForum; $correctErr='';
$title=$title.$l_noTopicsInForum;
$warn=ParseTpl(makeUp('main_warning'));
}

else{

//if at least one topic exists in this forum

if(isset($mod_rewrite) and $mod_rewrite and $user_sort==0 and !isset($_GET['sortBy'])) {
$urlp=genForumURL($main_url, $forum, $forumName);
$urlType='Forum';
}
else {
if($user_sort==0 and !isset($_GET['sortBy'])) $sburl=''; else $sburl="&amp;sortBy={$user_sort}";
$urlp="{$main_url}/{$indexphp}action=vtopic&amp;forum=$forum{$sburl}";
$urlType='Gen';
}

//avoiding duplicated content issues
$totalPages=ceil($numRows/$viewmaxtopic);

if($page<PAGE1_OFFSET+1 or $page-PAGE1_OFFSET>$totalPages) {
$realPage=pageChk($page,$numRows,$viewmaxtopic);

if(isset($mod_rewrite) and $mod_rewrite and $sortBy==0) $urlp=addForumURLPage(genForumURL($main_url, $forum, $forumName), $realPage);
else $urlp="{$main_url}/{$indexphp}action=vtopic&forum=$forum&page={$realPage}";

header("HTTP/1.1 301 Moved Permanently");

if(isset($metaLocation)) {
$meta_relocate="{$urlp}"; echo ParseTpl(makeUp($metaLocation)); exit;
}
else{
header("Location: {$urlp}");
exit;
}
}

$pageNav=pageNav($page,$numRows,$urlp,$viewmaxtopic,FALSE,$urlType);

if($pageNav!='') $mbpn='mb';

$makeLim=makeLim($page,$numRows,$viewmaxtopic);

if(isset($customTopicSort) and is_array($customTopicSort) and isset($customTopicSort[$forum])) 
$defaultSorting="<br /><a href=\"{$main_url}/{$indexphp}action=vtopic&amp;forum={$forum}&amp;sortBy=2\">{$l_sortBy}&nbsp;{$customTopicSort[$forum][1]}</a>";

if( (!isset($_GET['sortBy']) or $sortBy==2) and isset($customTopicSort) and is_array($customTopicSort) and isset($customTopicSort[$forum])) { $orderBy=$customTopicSort[$forum][0];
$sortedByT=$customTopicSort[$forum][1];
$defaultSorting='';
}
elseif ($user_sort==1) $orderBy='sticky DESC,topic_id DESC';
else $orderBy='sticky DESC,topic_last_post_id DESC';

$colls=array();

if($cols=db_simpleSelect(0,$Tt,'topic_id, topic_title, topic_poster, topic_poster_name, topic_time, topic_status, posts_count, sticky, topic_views, topic_last_post_id, topic_last_post_time, topic_last_poster','forum_id','=',$forum,$orderBy,$makeLim)) {
do {
if(!isset($textLd)) $lPosts[]=$cols[9];
else { if($user_sort==0) $lPosts[]=$cols[9]; else $lPosts[]=$cols[0]; }
$colls[]=array($cols[0], $cols[1], $cols[2], $cols[3], $cols[4], $cols[5], $cols[6], $cols[7], $cols[8], $cols[9], $cols[10], $cols[11]);
}
while($cols=db_simpleSelect(1));
}

if(isset($textLd)){

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
unset($xtr);
}
}

$i=1;
$tpl=makeUp('main_topics_cell');

foreach($colls as $cols){

if($i>0) $bg='tbCel1';else $bg='tbCel2';
$topic=$cols[0];

$topic_reverse='';
$topic_views=$cols[8];
if(isset($themeDesc) and in_array($topic,$themeDesc)) $topic_reverse="<img src=\"{$main_url}/img/topic_reverse.gif\" style=\"vertical-align:middle;\" alt=\"\" />&nbsp;";

if(!isset($preModerationType) or $preModerationType==0) $topicTitle=$cols[1]; elseif($preModerationType>0 and isset($premodTopics) and in_array($cols[0], $premodTopics)) $topicTitle=$l_topicQueued; else $topicTitle=$cols[1];

if(trim($topicTitle)=='') $topicTitle=$l_emptyTopic;
if(isset($_GET['h']) and $_GET['h']==$topic) $topicTitle='&raquo; '.$topicTitle;

$numReplies=$cols[6]; if($numReplies>=1) $numReplies-=1;
if ($cols[3]=='') $cols[3]=$l_anonymous; $topicAuthor=$cols[3];
$whenPosted=convert_date($cols[4]);

if(isset($pVals[$topic][0])) $lastPosterID=$pVals[$topic][0]; else $lastPosterID='N/A';

if($numReplies>0 and isset($cols[11]) and $cols[11]!='') $lastPoster=$cols[11];
elseif($numReplies>0 and isset($pVals[$topic][1])) $lastPoster=$pVals[$topic][1];
else $lastPoster='&mdash;';

if($numReplies>0 and isset($cols[10])) $lastPostDate=convert_date($cols[10]);
elseif($numReplies>0 and isset($pVals[$topic][2])) $lastPostDate=convert_date($pVals[$topic][2]);
else $lastPostDate='';

if(isset($textLd) and isset($pVals[$topic][3])){
$lptxt=($textLd==1?$pVals[$topic][3]:strip_tags(str_replace('<br />', ' ', $pVals[$topic][3])));
if(!isset($preModerationType) or $preModerationType==0) $lastPostText=$lptxt;
elseif($preModerationType>0 and isset($premodTopics) and in_array($cols[0], $premodTopics)) $lastPostText=$l_postQueued;
elseif($preModerationType>0 and isset($premodPosts) and in_array($pVals[$topic][4], $premodPosts)) $lastPostText='';
else $lastPostText=$lptxt;
}

if(isset($mod_rewrite) and $mod_rewrite) {
$urlp=genTopicURL($main_url, $forum, $forumName, $topic, $topicTitle);
$urlType='Topic';
}
else {
$urlp="{$main_url}/{$indexphp}action=vthread&amp;forum=$forum&amp;topic=$topic";
$urlType='Gen';
}

$pageNavCell=pageNav(PAGE1_OFFSET+1,$numReplies+1,$urlp,$viewmaxreplys,TRUE,$urlType);

if ($cols[7]==1 and $cols[5]==1) $tpcIcon='stlock';
elseif ($cols[7]==1) $tpcIcon='sticky';
elseif ($cols[5]==1) $tpcIcon='locked';
elseif ($numReplies<=0) $tpcIcon='empty';
elseif ($numReplies>=$viewmaxreplys) $tpcIcon='hot';
else $tpcIcon='default';

if(isset($mod_rewrite) and $mod_rewrite) {
$linkToTopic=addTopicURLPage(genTopicURL($main_url, $forum, $forumName, $topic, $topicTitle), PAGE1_OFFSET+1);
}
else $linkToTopic="{$main_url}/{$indexphp}action=vthread&amp;forum={$forum}&amp;topic={$topic}";

if(function_exists('parseTopic')) parseTopic();
$list_topics.=ParseTpl($tpl);
$i=-$i;
}
}//request ok

$newTopicLink='<a href="'.$main_url.'/'.$indexphp.'action=vtopic&amp;forum='.$forum.'&amp;showSep=1">'.$l_new_topic.'</a>';
}//if not showsep

$l_messageABC=$l_message;

$emailCheckBox=emailCheckBox();

//dynamic BB buttons
$mpf=ParseTpl(makeUp('main_post_form'));
$mpfs=convertBBJS($mpf);
if($mpfs!='') $mainPostForm=$mpfs; else $mainPostForm=$mpf;

if($page>PAGE1_OFFSET+1) {
$tpage=' - '.$l_page.' '.($page-PAGE1_OFFSET);
$description.=' ('.$l_page.' '.($page-PAGE1_OFFSET).')';
}
else $tpage='';
$title1=$title; $title=$forumName;
if(isset($addMainTitle)) $title.=' - '.str_replace(' - ','',$title1);
$title.=$tpage;

if(!isset($_GET['showSep'])) $main=makeUp('main_topics');
else $main='';

$nTop=1;
$allowForm=($user_id==1 or $isMod==1);
$c1=(in_array($forum,$clForums) and isset($clForumsUsers[$forum]) and !in_array($user_id,$clForumsUsers[$forum]) and !$allowForm);
$c3=(isset($poForums) and in_array($forum, $poForums) and !$allowForm);
$c4=(isset($roForums) and in_array($forum, $roForums) and !$allowForm);

if ($c1 or $c3 or $c4) {
$main=preg_replace("/(<form.*<\/form>)/Uis", '', $main);
$nTop=0;
$newTopicLink='';
}

if($user_id==0) $l_sub_post_tpc=$l_enterforums.'/'.$l_sub_post_tpc;
echo load_header(); echo $warn; echo ParseTpl($main);
?>