<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING 
file for more details. Copyright (C) 2004-2009 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2009-Jul-13
*/
$unset=array('logged_admin', 'isMod', 'user_id', 'langu', 'includeHeader', 'includeFooter', 'emptySubscribe', 'allForumsReg', 'registerInactiveUsers', 'mod_rewrite', 'enableViews', 'userDeleteMsgs', 'userInfoInPosts', 'inss', 'insres', 'preModerationType', 'textLd', 'adminAcceptsSignup', 'customProfileList', 'correct', 'customTopicSort', 'manualIndex', 'startIndex', 'mTop', 'mdrw', 'metaLocation', 'post', 'reply_to_email', 'csrfchk', 'emailCharset', 'adminUser', 'cook', 'forumClone', 'xtr', 'addMainTitle', 'url_follow', 'site_url', 'allowedRefs', 'uname_minlength', 'uname_maxlength', 'enchecked', 'fIconWidth', 'fIconHeight');
for($i=0;$i<sizeof($unset);$i++) if(isset(${$unset[$i]})) { ${$unset[$i]}=''; unset(${$unset[$i]}); }
if(isset($metaLocation)) $metaLocation=str_replace(array('.','/','\\','admin_'),'',$metaLocation);

$currY=date('Y');

function get_microtime() {
$mtime=explode(' ',microtime());
return $mtime[1]+$mtime[0];
}

$starttime=get_microtime();

if(!isset($_SERVER['QUERY_STRING'])) $_SERVER['QUERY_STRING']='';
$queryStr=(isset($_POST['queryStr'])?$_POST['queryStr']:$_SERVER['QUERY_STRING']);
//$queryStr=(isset($_POST['queryStr'])?rawurlencode(rawurldecode($_POST['queryStr'])):rawurlencode($_SERVER['QUERY_STRING']));

//echo $queryStr; 

$queryStr=preg_replace("@[^0-9a-zA-Z./#_&=%-]@", '', $queryStr);
$queryStrDisp=str_replace('&', '&amp;', $queryStr);
$queryStr=str_replace('&amp;', '&', $queryStr);

define ('INCLUDED776',1);

if(defined('CLONE_PATH')) $clonePath=CLONE_PATH; else $clonePath='./';
include ($clonePath.'setup_options.php');

if (isset($_POST['action'])) $action=$_POST['action']; elseif (isset($_GET['action'])) $action=$_GET['action']; else $action='';

/* Allowing to post only from the internal or allowed domain */
if(isset($site_url)) $tUrl=$site_url;
else{
$t=explode('/', $main_url);
$tUrl=implode('/', array($t[0], $t[1], $t[2]));
}

if(isset($_POST) and sizeof($_POST)>0 and (!isset($allowedDirectPostActions) or !in_array($action, $allowedDirectPostActions) ) ){
if(isset($_SERVER['HTTP_REFERER'])) $httpRef=strtolower($_SERVER['HTTP_REFERER']); else $httpRef='';
if(substr_count($httpRef, strtolower($main_url))==0 and substr_count($httpRef, strtolower($tUrl))==0){
$af=FALSE;
if(isset($allowedRefs) and is_array($allowedRefs)) {
foreach($allowedRefs as $a) {
if(substr_count($httpRef, strtolower($a))>0) { $af=TRUE; break; }
}
}
if(!$af) die('Sorry, it seems like an intrusion attempt or your server doesn\'t support HTTP referrers!');
}
}
/* --Allowing to post */

if(!isset($startIndex)) $startIndex=$indexphp;
if(!isset($manualIndex)) $manualIndex=$indexphp.'action=manual';
if($cookiesecure) $csecurejs='1'; else $csecurejs='0';
if(!isset($fIconWidth)) $fIconWidth=16;
if(!isset($fIconHeight)) $fIconHeight=16;

$langOrig=$lang;

$indexphp=(!isset($GLOBALS['indexphp'])?'index.php':$GLOBALS['indexphp']);
if(!isset($manualIndex)) $manualIndex=$indexphp.'action=manual';
if(isset($mod_rewrite) and $mod_rewrite) $queryStr=str_replace(array('%3D0%26mdrw%3Don', '&amp;mdrw=on'), '', $queryStr);

include ($pathToFiles.'setup_'.$DB.'.php');
include ($pathToFiles.'bb_cookie.php');
include ($pathToFiles.'bb_functions.php');
include ($pathToFiles.'bb_specials.php');

/* Main stuff */

$loginError=0;
$title=$sitename.' - ';

if(!isset($user_id)) $user_id=0;
if(!defined('PAGE1_OFFSET')) define('PAGE1_OFFSET', 0);
if(isset($_GET['page'])) $page=(integer)$_GET['page']+0; elseif(isset($_POST['page'])) $page=(integer)$_POST['page']+0; else $page=PAGE1_OFFSET+1;

if(isset($_GET['forum'])) $forum=(integer)$_GET['forum']+0; elseif(isset($_POST['forum'])) $forum=(integer)$_POST['forum']+0; else $forum=0;
if(isset($_GET['topic'])) $topic=(integer)$_GET['topic']+0; elseif(isset($_POST['topic'])) $topic=(integer)$_POST['topic']+0; else $topic=0;
if (isset($_POST['action'])) $action=$_POST['action']; elseif (isset($_GET['action'])) $action=$_GET['action']; else $action='';
if (isset($_POST['csrfchk'])) $csrfchk=$_POST['csrfchk']; elseif (isset($_GET['csrfchk'])) $csrfchk=$_GET['csrfchk']; else $csrfchk=''; 

if($action!='vthread' and $action!='vtopic' and $page<PAGE1_OFFSET+1) $page=PAGE1_OFFSET+1;

$user_id+=0;
$user_usr='';

$l_adminpanel_link='';
$reqTxt=0;

if(function_exists('defineRobots')) $metaRobots=defineRobots();

else{

function defineRobots(){
$action=$GLOBALS['action'];

$pdcc1=($action=='' or ($action=='vtopic' and isset($GLOBALS['forum']) and !in_array($GLOBALS['forum'], $GLOBALS['clForums'])) or $action=='vthread' or $action=='manual' or $action=='tpl');
$pdcc2=(isset($_GET['mdrw']));
$pdcc3=(isset($_GET['sortBy']));

if($action=='stats' or $action=='userinfo') $metaRobots='NOINDEX,FOLLOW';

elseif(isset($GLOBALS['mod_rewrite']) and $GLOBALS['mod_rewrite']){
if($pdcc1 and !$pdcc3) { if($pdcc2 or (!$pdcc2 and $action=='') ) $metaRobots='INDEX,FOLLOW'; else $metaRobots='NOINDEX,NOFOLLOW'; }
else $metaRobots='NOINDEX,NOFOLLOW';
}

else{
if($pdcc1 and !$pdcc3) $metaRobots='INDEX,FOLLOW'; else $metaRobots='NOINDEX,NOFOLLOW';
}

return $metaRobots;
}//func

$metaRobots=defineRobots();

}
//echo $metaRobots;

/* Predefining variables */
if(!isset($url_follow)) { $url_follow=FALSE; $relFollowUrl=' rel="nofollow"'; } else { $url_follow=TRUE; $relFollowUrl=''; }

$sortingTopics+=0;

if (isset($_GET['sortBy'])) {
$sortBy=$_GET['sortBy']; $sdef=1;
} else {
$sortBy=$sortingTopics; $sdef=0;
}

if (!($sortBy==1 or $sortBy==0 or $sortBy==2)) $sortBy=$sortingTopics;

if (isset($_POST['mode']) and $_POST['mode']=='login') require($pathToFiles.'bb_func_login.php');

if ($loginError==0) {

if(isset($_GET['mode']) and $_GET['mode']=='logout') {
deleteMyCookie();
if(isset($metaLocation)) { $meta_relocate="{$main_url}/{$indexphp}"; echo ParseTpl(makeUp($metaLocation)); exit; } else { header("Location: {$main_url}/{$startIndex}"); exit; }
}

user_logged_in();
if($user_id!=0 and isset($langu) and $langu=str_replace(array('.','/','\\'),'',$langu) and file_exists($pathToFiles."lang/{$langu}.php")) $lang=$langu;
elseif($user_id==0 and isset($_GET['setlang']) and $setlang=str_replace(array('.','/','\\'),'',$_GET['setlang']) and file_exists($pathToFiles."lang/{$setlang}.php")) {$lang=$setlang; $indexphp.='setlang='.$setlang.'&';}

if($user_id>0 and !isset($_COOKIE[$cookiename.'_csrfchk'])) setCSRFCheckCookie();

include ($pathToFiles."lang/$lang.php");

$actEnable=(isset($GLOBALS['user_activity'])?$GLOBALS['user_activity']:1);
$actTrue=($actEnable==-1 and ($action=='prefs' OR $action=='editprefs' OR $action=='confirmpasswd'));

if($actEnable==0 or ($actEnable!=1 and !$actTrue)) $forb=2;

else{

if($action=='vtopic' or $action=='vthread' or $action=='delAvatarAdmin' or ($action=='' and $viewTopicsIfOnlyOneForum==1)){
if( (isset($allForumsReg) and $allForumsReg) OR ( isset($regUsrForums) and is_array($regUsrForums) and in_array($forum, $regUsrForums) and $user_id==0) ) { $l_anonTxt=$l_anonDisallowed; $anonPost=0; } else { $l_anonTxt=$l_anonAllowed; $anonPost=1; }
if($user_id==0) $l_anonTxt='<span class="txtSm"><br />'.$l_anonTxt.'</span>'; else $l_anonTxt='';
}

if ($user_id!=0) {
if($sdef==1) $user_sort=$sortBy;
}
else {
if($sdef==0) $user_sort=$sortingTopics; else $user_sort=$sortBy;
}

if(!isset($user_sort)) $user_sort=0;
if($user_sort==0) { $sortByNew=1; $sortedByT=$l_newAnswers; $sortByT=$l_newTopics; }
else { $sortByNew=0; $sortedByT=$l_newTopics; $sortByT=$l_newAnswers; }

/* Protected forums stuff */
if(isset($_POST['allForums']) and $_POST['allForums']==$protectWholeForumPwd) {
$allForums=writeUserPwd($protectWholeForumPwd);
setcookie($cookiename.'allForumsPwd','',(time() - 2592000),$cookiepath,$cookiedomain,$cookiesecure);
setcookie($cookiename.'allForumsPwd', $allForums);
if(isset($metaLocation)) { $meta_relocate="{$main_url}/{$indexphp}{$queryStr}"; echo ParseTpl(makeUp($metaLocation));
exit; } else header("Location: {$main_url}/{$indexphp}{$queryStr}");
}
elseif (!isset($_POST['allForums']) and isset($_COOKIE[$cookiename.'allForumsPwd'])) { $allForums=$_COOKIE[$cookiename.'allForumsPwd']; }
elseif (!isset($_POST['allForums']) and !isset($_COOKIE[$cookiename.'allForumsPwd']) and isset($_SESSION['allForums'])) $allForums=$_SESSION['allForums'];
else $allForums='';

if ($protectWholeForum==1) {
if ($allForums!=writeUserPwd($protectWholeForumPwd)) {
$title=$sitename." :: ".$l_forumProtected;
echo ParseTpl(makeUp('protect_forums')); exit;
}
}

if($viewTopicsIfOnlyOneForum==1 and ($action=='' or $action=='vtopic' or $action=='vthread')){
$row=db_simpleSelect(0,$Tf,'forum_id, forum_name, forum_icon, forum_desc, topics_count', '', '', '', 'forum_id asc', 1);
$forumsArray[$row[0]]=array($row[1], $row[2], $row[3], $row[4]); $forum=$row[0]; 
if($action=='') $action='vtopic';
}

if(!isset($logged_admin)) $logged_admin=0;

if ($logged_admin==1) {
$l_adminpanel_link='<span class="txtNr"><a href="'.$main_url.'/'.$bb_admin.'">'.$l_adminpanel.'</a></span><br />';
}
else $l_adminpanel_link='';

/* Moderator's definition */
$isMod=0;
if($forum!=0){
if(isset($mods[$forum]) and in_array($user_id,$mods[$forum])) $isMod=1;
}
else{
foreach($mods as $key=>$val) if(is_array($val) and in_array($user_id, $val)) { $isMod=1; break; }
}

if($action=='vthread' or $action=='delAvatarAdmin' or $action=='deltopic' or $action=='delmsg'){

$topicData=db_simpleSelect(0,$Tt,'topic_title, topic_status, topic_poster, topic_poster_name, forum_id, posts_count, sticky, topic_views, topic_time, topic_last_post_id','topic_id','=',$topic);
if($topicData and $topicData[4]!=$forum and !isset($_GET['goPost']) and !isset($_GET['user'])) {
//topic was moved - permanent redirect here
if(isset($mod_rewrite) and $mod_rewrite) $rcurl=addTopicURLPage(genTopicURL($main_url, $topicData[4], '#GET#', $topic, $topicData[0]), PAGE1_OFFSET+1);
else $rcurl="{$main_url}/{$indexphp}action=vthread&forum={$topicData[4]}&topic={$topic}";

header("HTTP/1.1 301 Moved Permanently");

if(isset($metaLocation)) {
$meta_relocate="{$rcurl}"; echo ParseTpl(makeUp($metaLocation)); exit;
}
else{
header("Location: {$rcurl}");
}
exit;

}
elseif(isset($_GET['goPost']) and isset($_GET['user'])) $forum=$topicData[4];

$totalPosts=$topicData[5];
//else $forb=1;
unset($result);unset($countRes);
}

}//forb

/* Private, archive and post-only forums stuff */
if(!isset($forb)) $forb=0;

if ($user_id!=1 and $forum!=0) {
if (isset($clForums) and in_array($forum, $clForums)) {
if (isset($clForumsUsers[$forum]) and !in_array($user_id,$clForumsUsers[$forum])) $forb=2;
}
if (isset($roForums) and in_array($forum, $roForums) and $isMod!=1) {
if (in_array($action, array('pthread', 'ptopic', 'editmsg', 'editmsg2', 'delmsg', 'locktopic', 'unlocktopic', 'deltopic', 'movetopic', 'movetopic2', 'sticky', 'unsticky'))) $forb=1;
}
if (isset($poForums) and in_array($forum, $poForums) and $isMod!=1){
if ($action!='' and in_array($action, array('ptopic'))) $forb=1;
}
}

if ($forb>0) {
header('Status: 404 Not Found');
$metaRobots='NOINDEX,NOFOLLOW';
$title.=$l_accessDenied;
echo load_header();
if($forb==2) $errorMSG=$l_accessDenied; else $errorMSG=$l_forbidden; 

if(isset($_POST) and sizeof($_POST)>0){
$antiWarn=$l_deniedWhilePost;
$fieldsReadOnly=1;
$displayFormElements=array('topicTitle'=>1, 'postText'=>1);
include($pathToFiles.'bb_func_posthold.php');
echo ParseTpl(makeUp('main_posthold')); 
}
else{
$l_returntoforums=''; $correctErr='';
echo ParseTpl(makeUp('main_warning'));
}

//$l_loadingtime='';

//echo ParseTpl(makeUp('main_footer'));
//exit;
}
else {

/* End stuff */

/* Banned IPs/IDs stuff */
$thisIp=getIP();
$cen=explode('.', $thisIp);

if(isset($cen[0]) and isset($cen[1]) and isset($cen[2])){
$thisIpMask[0]=$cen[0].'.'.$cen[1].'.'.$cen[2].'.+';
$thisIpMask[1]=$cen[0].'.'.$cen[1].'.+';
$thisIpMask[2]=$cen[0].'.+';
} 
else {
$thisIpMask[0]='0.0.0.+';
$thisIpMask[1]='0.0.+';
$thisIpMask[2]='0.+';
}

if (db_ipCheck($thisIp,$thisIpMask,$user_id)) {
$title=$sitename.' - '.$l_forbidden;
echo ParseTpl(makeUp('main_access_denied')); exit;
}

$backErrorLink="<a href=\"JavaScript:history.back(-1)\">$l_back</a>";
include ($pathToFiles.'bb_plugins.php');

if ($user_id!=0) {
$loginLogout=ParseTpl(makeUp('user_logged_in'));
$user_logging=$loginLogout;
}
else {
$loginLogout=ParseTpl(makeUp('user_login_form'));
if(!in_array($action,array('registernew','register','sendpass','sendpass2'))) $user_logging=ParseTpl(makeUp('user_login_only_form')); else $user_logging='';
}

/* Main actions */
function getPage($topic, $totalPosts){
if(isset($GLOBALS['themeDesc']) and in_array($topic,$GLOBALS['themeDesc'])) $page=PAGE1_OFFSET+1;
elseif($totalPosts<=$GLOBALS['viewmaxreplys']) $page=PAGE1_OFFSET+1;
else $page=ceil($totalPosts/$GLOBALS['viewmaxreplys'])+PAGE1_OFFSET;
return $page;
}

if($action=='pthread') {if($reqTxt!=1)require_once($pathToFiles.'bb_func_txt.php');require($pathToFiles.'bb_func_pthread.php');}
elseif($action=='ptopic') {if($reqTxt!=1)require_once($pathToFiles.'bb_func_txt.php');require($pathToFiles.'bb_func_ptopic.php');}

if(($action=='pthread' or $action=='ptopic') and isset($totalPosts)) {
$page=getPage($topic, $totalPosts);
}

if($action=='pthread') {
if (!isset($errorMSG)) {
if(isset($anchor) and $anchor!='') $anchor='#msg'.$anchor; elseif($postText=='') $anchor='#newreply'; else $anchor='';
if(file_exists($pathToFiles.'bb_plugins2.php')) require_once($pathToFiles.'bb_plugins2.php');
if(isset($metaLocation)) {
$meta_relocate="{$main_url}/{$indexphp}action=vthread&amp;forum=$forum&amp;topic=$topic&amp;page=$page{$anchor}";
echo ParseTpl(makeUp($metaLocation));
exit;
}
else {
if(isset($mod_rewrite) and $mod_rewrite) $furl=addTopicURLPage(genTopicURL($main_url, $forum, '#GET#', $topic, '#GET#'), $page).$anchor;
else $furl=addGenURLPage("{$main_url}/{$indexphp}action=vthread&forum=$forum&topic=$topic", $page, '&').$anchor;
header("Refresh: 0; url={$furl}"); exit;
}
}
}

elseif($action=='vthread') {

/* Redirect to the regular URL and update sendmails table, if user is accessing topic from email message */
if(isset($_GET['user'])) $resetUser=$_GET['user']+0; else $resetUser=$user_id;

if(!isset($activeEmailsDisable) and $genEmailDisable==0 and $emailusers>0 and $user_id>0 and $sendid=db_simpleSelect(0,$Ts,'id,active,user_id','user_id','=',$resetUser,'','','topic_id','=',$topic) and $sendid[1]==0){
$active=1; updateArray(array('active'),$Ts,'id',$sendid[0]);
}

if(isset($_GET['goPost']) and isset($_GET['user'])){

$goPost=$_GET['goPost']+0;

if($sendid=db_simpleSelect(0,$Ts,'id,active','user_id','=',$resetUser,'','','topic_id','=',$topic) and $sendid[1]==0){
$active=1; updateArray(array('active'),$Ts,'id',$sendid[0]);
}

$anchor='#msg'.$goPost;

if($row=db_simpleSelect(0, $Tp, 'count(*)', 'post_id', '<=', $goPost, '', '', 'topic_id', '=', $topic)) $totalPosts=$row[0]; else $totalPosts=0;
$page=getPage($topic, $totalPosts);

if(isset($metaLocation)) {
$meta_relocate="{$main_url}/{$indexphp}action=vthread&amp;forum=$forum&amp;topic=$topic&amp;page=$page{$anchor}"; echo ParseTpl(makeUp($metaLocation)); exit;
}
else {
if(isset($mod_rewrite) and $mod_rewrite) $furl=addTopicURLPage(genTopicURL($main_url, $forum, '#GET#', $topic, '#GET#'), $page).$anchor;
else $furl=addGenURLPage("{$main_url}/{$indexphp}action=vthread&forum=$forum&topic=$topic", $page, '&').$anchor;
}

header("Refresh: 0; url={$furl}"); exit;
}

require($pathToFiles.'bb_func_vthread.php');

}

elseif($action=='vtopic') {
if(isset($redthread) and is_array($redthread) and isset($redthread[$forum])) {
if(isset($metaLocation)) {
$meta_relocate="{$main_url}/{$indexphp}action=vthread&forum=$forum&topic={$redthread[$forum]}"; echo ParseTpl(makeUp($metaLocation)); exit;
} else {
if(isset($mod_rewrite) and $mod_rewrite) $furl=addTopicURLPage(genTopicURL($main_url, $forum, '#GET#', $redthread[$forum], '#GET#'), PAGE1_OFFSET+1); else $furl="{$main_url}/{$indexphp}action=vthread&forum=$forum&topic={$redthread[$forum]}";
header("Location: {$furl}");
exit;
}
}
else require($pathToFiles.'bb_func_vtopic.php');
}

elseif($action=='ptopic') {
$page=PAGE1_OFFSET+1;
if(file_exists($pathToFiles.'bb_plugins2.php')) require_once($pathToFiles.'bb_plugins2.php');
if (!isset($errorMSG)) {
if(isset($metaLocation)) {
$meta_relocate="{$main_url}/{$indexphp}action=vthread&forum={$forum}&topic={$topic}"; echo ParseTpl(makeUp($metaLocation)); exit; } else {
if(isset($mod_rewrite) and $mod_rewrite) $furl=addTopicURLPage(genTopicURL($main_url, $forum, '#GET#', $topic, '#GET#'), PAGE1_OFFSET+1); else $furl="{$main_url}/{$indexphp}action=vthread&forum=$forum&topic=$topic";
header("Refresh: 0; url={$furl}"); exit;
}
}
}

elseif($action=='search') {if($reqTxt!=1)require($pathToFiles.'bb_func_txt.php');require($pathToFiles.'bb_func_search.php');}

elseif($action=='deltopic') require($pathToFiles.'bb_func_deltopic.php');

elseif($action=='locktopic') require($pathToFiles.'bb_func_locktop.php');

elseif($action=='editmsg') {$step=0;require($pathToFiles.'bb_func_editmsg.php');}

elseif($action=='editmsg2') {require($pathToFiles.'bb_func_txt.php');$step=1;require($pathToFiles.'bb_func_editmsg.php');}

elseif($action=='delmsg') {$step=0;require($pathToFiles.'bb_func_delmsg.php');}

elseif($action=='movetopic') {$step=0;require($pathToFiles.'bb_func_movetpc.php');}

elseif($action=='movetopic2') {$step=1;require($pathToFiles.'bb_func_movetpc.php');}

elseif($action=='userinfo') require($pathToFiles.'bb_func_usernfo.php');

elseif($action=='sendpass' and file_exists($pathToFiles.'bb_func_sendpwd.php')) {$step=0;require($pathToFiles.'bb_func_sendpwd.php');}

elseif($action=='sendpass2' and file_exists($pathToFiles.'bb_func_sendpwd.php')) {$step=1;require($pathToFiles.'bb_func_sendpwd.php');}

elseif($action=='confirmpasswd') { require($pathToFiles.'bb_func_confpwd.php');}

elseif($action=='stats' and file_exists($pathToFiles.'bb_func_stats.php')) require($pathToFiles.'bb_func_stats.php');

elseif($action=='manual') require($pathToFiles.'bb_func_man.php');

elseif($action=='registernew' and ($user_id==1 or $enableNewRegistrations)) {$step=0;require($pathToFiles.'bb_func_regusr.php');}

elseif($action=='register' and ($user_id==1 or $enableNewRegistrations)) {$step=1;require($pathToFiles.'bb_func_regusr.php');}

elseif($action=='prefs' and $enableProfileUpdate) {$step=0;require($pathToFiles.'bb_func_editprf.php');}

elseif($action=='editprefs' and $enableProfileUpdate) {$step=1;require($pathToFiles.'bb_func_editprf.php');}

elseif($action=='unsubscribe') require($pathToFiles.'bb_func_unsub.php');

elseif($action=='sticky') {$status=9;require($pathToFiles.'bb_func_sticky.php');}

elseif($action=='unsticky') {$status=0;require($pathToFiles.'bb_func_sticky.php');}

elseif($action=='viewipuser') {require($pathToFiles.'bb_func_viewip.php');}

elseif($action=='tpl') {
if(isset($_GET['tplName'])) $tplName=str_replace(array('.','/','\\','admin_'),'',$_GET['tplName']); else $tplName='';
if ($tplName!='' and file_exists ($pathToFiles.'templates/'.$tplName.'.html')){
echo load_header(); echo ParseTpl(makeUp($tplName));
}
else {
if(isset($metaLocation)) { $meta_relocate="{$main_url}/{$indexphp}"; echo ParseTpl(makeUp($metaLocation)); exit; } else header("Location: {$main_url}/{$indexphp}");
}
}

elseif($action==''){
require($pathToFiles.'bb_func_vforum.php');
if ($viewlastdiscussions!=0) {
require($pathToFiles.'bb_func_ldisc.php');
$listTopics=$list_topics;
if($list_topics!='') {
if(isset($startPageModern) and $startPageModern) echo ParseTpl(makeUp('main_modern_layout')); else echo ParseTpl(makeUp('main_last_discussions'));
}
}
}

}//forb

}
else {
//loginError=1
if($loginError==1) $tpl='main_warning'; else $tpl='main_posthold';
echo load_header(); echo ParseTpl(makeUp($tpl));
}

if(!defined('HEADER_CALLED')) { header("Location: {$main_url}/"); exit; }

if(file_exists($pathToFiles.'bb_plugins2.php')) require_once($pathToFiles.'bb_plugins2.php');

$freeWareKeys=array(
'Web Forum Software',
'Chat Forum Software',
'Discussion Forum Software',
'Light Forum Script',
'PHP Forum Software',
'Forum Script',
'Forum Software',
'Free Forum Software',
'Open Source Forum Script',
'Simple Bulletin Board',
'Bulletin Board Script',
'Bulletin Board Software',
'Community Script',
'Online Community Software',
'Easy Forum Software',
'Online Community Script'
);

$rndNum=strlen($sitename);
$tk=sizeof($freeWareKeys)-1;
while($rndNum>$tk) $rndNum=$rndNum-$tk;
//$ck=rand(0,sizeof($freeWareKeys)-1);
$software=$freeWareKeys[$rndNum];

$violating_the_copyright_may_result_in_your_criminal_responsibility=<<<out
<a href="http://www.minibb.com/" target="_blank"><img src="{$main_url}/img/minibb.gif" alt="{$sitename} {$l_poweredBy} {$software} miniBB &reg;" title="{$sitename} {$l_poweredBy} {$software} miniBB &reg;" /></a>
out;

//Loading footer
$endtime=get_microtime();
$totaltime=sprintf ("%01.3f", ($endtime-$starttime));
if(isset($includeFooter) and $includeFooter!='') include($includeFooter); else echo ParseTpl(makeUp('main_footer'));
?>