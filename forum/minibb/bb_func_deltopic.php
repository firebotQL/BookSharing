<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2006 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2008-May-19
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

if($csrfchk=='' or $csrfchk!=$_COOKIE[$cookiename.'_csrfchk']) die('Can not proceed: possible CSRF/XSRF attack!');

$canDelete=TRUE;
if(isset($topicData[2])) {
$poster_id=$topicData[2];
$time_diff=strtotime('now')-strtotime($topicData[8]);
if($user_id!=0 and $user_id==$poster_id and $useredit!=0 and $time_diff>$useredit) $canDelete=FALSE;
elseif($topicData[1]==1 and $user_id!=1 and $isMod==0) $canDelete=FALSE;
}
else {
$poster_id=-1;
$canDelete=FALSE;
}

if ($logged_admin==1 or $isMod==1 or (isset($userDeleteMsgs) and $userDeleteMsgs==2 and $user_id!=0 and $user_id==$poster_id and $canDelete)) {

if($user_sort==0) $return=PAGE1_OFFSET+1; else{

if($res=db_simpleSelect(0,$Tt,'topic_id','topic_id','>',$topic,'topic_id asc','','forum_id','=',$forum)) $h=$res[0]; else $h=0;

if(!isset($countRes)) $countRes=0;
$numRows=$countRes;

/* define sticky topics */
if($stRow=db_simpleSelect(0, $Tt, 'count(*)', 'sticky', '=', '1', '', '', 'forum_id', '=', $forum)) $stRow=$stRow[0]; else $stRow=0;
$numRows+=$stRow;
$return=ceil($numRows/$viewmaxtopic)+PAGE1_OFFSET;
}

$pUsers=array();
if($row=db_simpleSelect(0,$Tp,'poster_id','topic_id','=',$topic,'post_id ASC')){
do if(!in_array($row[0], $pUsers) and $row[0]!=0) $pUsers[]=$row[0];
while($row=db_simpleSelect(1));
}

if(file_exists($pathToFiles.'bb_plugins2.php')) require($pathToFiles.'bb_plugins2.php');

db_delete($Ts,'topic_id','=',$topic);
$topicsDel=db_delete($Tt,'topic_id','=',$topic,'forum_id','=',$forum);
$postsDel=db_delete($Tp,'topic_id','=',$topic,'forum_id','=',$forum);
$postsDel--;
db_calcAmount($Tp,'forum_id',$forum,$Tf,'posts_count');
db_calcAmount($Tt,'forum_id',$forum,$Tf,'topics_count');

$i=0;
foreach($pUsers as $val){
if($i==0) db_calcAmount($Tt,'topic_poster',$val,$Tu,$dbUserSheme['num_topics'][1],$dbUserId);
db_calcAmount($Tp,'poster_id',$val,$Tu,$dbUserSheme['num_posts'][1],$dbUserId);
$i++;
}

if (defined('DELETE_PREMOD')) return;

//CSRF cookie delete
setcookie($cookiename.'csrf', '', (time()-2592000), $cookiepath, $cookiedomain, $cookiesecure);

if($user_sort==1){
if(isset($metaLocation)) { $meta_relocate="{$main_url}/{$indexphp}action=vtopic&forum={$forum}&page={$return}&h={$h}"; echo ParseTpl(makeUp($metaLocation)); exit; } else { header("Location: {$main_url}/{$indexphp}action=vtopic&forum={$forum}&page={$return}&h={$h}"); exit; }
}
else{
if(isset($mod_rewrite) and $mod_rewrite) $urlp=addForumURLPage(genForumURL($main_url, $forum, '#GET#'), PAGE1_OFFSET+1); else $urlp="{$main_url}/{$indexphp}action=vtopic&forum={$forum}";
if(isset($metaLocation)) { $meta_relocate=$urlp; echo ParseTpl(makeUp($metaLocation)); exit; } else { header("Location: {$urlp}"); exit; }
}

}
else {
$errorMSG=$l_forbidden; $correctErr='';
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}

?>