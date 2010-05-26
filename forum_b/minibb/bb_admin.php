<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2009 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2009-Dec-21
*/
define ('INCLUDED776',1);

$unset=array('logged_admin','isMod','user_id','langu','includeHeader','includeFooter', 'mod_rewrite', 'inss', 'insres','cook');
for($i=0;$i<sizeof($unset);$i++) if(isset(${$unset[$i]})) { ${$unset[$i]}=''; unset(${$unset[$i]}); }

$metaRobots='NOINDEX,NOFOLLOW';

function get_microtime() {
$mtime=explode(' ',microtime());
return $mtime[1]+$mtime[0];
}

$starttime=get_microtime();

define ('ADMIN_PANEL',1);

include ('./setup_options.php');

if(!isset($GLOBALS['indexphp'])) $indexphp='index.php?'; else $indexphp=$GLOBALS['indexphp'];

if(!isset($_SERVER['QUERY_STRING'])) $_SERVER['QUERY_STRING']='';
$queryStr=(isset($_POST['queryStr'])?rawurlencode(rawurldecode($_POST['queryStr'])):rawurlencode($_SERVER['QUERY_STRING']));

include ($pathToFiles."setup_$DB.php");
include ($pathToFiles.'bb_cookie.php');
include ($pathToFiles."bb_functions.php");
include ($pathToFiles."lang/$lang.php");

if(isset($_POST['mode'])) $mode=$_POST['mode']; elseif(isset($_GET['mode'])) $mode=$_GET['mode']; else $mode='';
if(isset($_POST['action'])) $action=$_POST['action']; elseif(isset($_GET['action'])) $action=$_GET['action']; else $action='start_admin_panel';

$l_adminpanel_link='';
$warning='';

$thisIp=getIP();

if(!isset($fIconWidth)) $fIconWidth=16;
if(!isset($fIconHeight)) $fIconHeight=16;

//-----
function getForumIcons() {
$iconList='';
if($handle=@opendir($GLOBALS['pathToFiles'].'img/forum_icons')) {
$ss=0;
while (($file=readdir($handle))!==false) {
if ($file != '.' && $file != '..' and (substr(strtolower($file),-3)=='gif' OR substr(strtolower($file),-3)=='jpg' OR substr(strtolower($file),-4)=='jpeg' OR substr(strtolower($file),-3)=='png')) {
$iconList.="<a href=\"JavaScript:paste_strinL('{$file}')\" onmouseover=\"window.status='{$GLOBALS['l_forumIcon']}: {$file}'; return true\"><img src=\"{$GLOBALS['main_url']}/img/forum_icons/{$file}\" alt=\"{$file}\" /></a>&nbsp;&nbsp;";
$ss++;
if ($ss==5) {
$ss=0;
$iconList.="<br />\n";
}
}
}
closedir($handle);
if ($iconList=='') $iconList=$GLOBALS['l_accessDenied'];
}
else $iconList=$GLOBALS['l_accessDenied'];
return $iconList;
}

//-----
function get_forums_fast_preview () {
// Get forums fast order preview in admin panel
global $result;
$fast='';

if($GLOBALS['viewTopicsIfOnlyOneForum']==1) $fast="<br />{$GLOBALS['l_topicsWillBeDisplayed']}";

else{
if ($row=db_simpleSelect(0,$GLOBALS['Tf'],'forum_id, forum_name, forum_desc, forum_order, forum_icon, forum_group','','','','forum_order')){
do{

if($row[5]!='') $fast.="<img src=\"{$GLOBALS['main_url']}/img/p.gif\" style=\"width:{$fIconWidth}px;height:{$fIconHeight}px\" alt=\"\" />&nbsp;<strong>{$row[5]}</strong><br />";

$fast.="<img src=\"{$GLOBALS['main_url']}/img/forum_icons/{$row[4]}\" style=\"width:{$fIconWidth}px;height:{$fIconHeight}px\" alt=\"Forum icon\" />&nbsp;<b><a href=\"{$GLOBALS['main_url']}/{$GLOBALS['bb_admin']}action=editforum2&amp;forumID={$row[0]}\">{$row[1]}</a></b> [ORDER: {$row[3]}] - <span class=\"txtSm\">{$row[2]}&nbsp;</span>&nbsp;&nbsp; <a href=\"{$GLOBALS['main_url']}/{$GLOBALS['bb_admin']}action=move&amp;where=1&amp;forumID={$row[0]}\">&uarr;</a>&nbsp;&nbsp;<a href=\"{$GLOBALS['main_url']}/{$GLOBALS['bb_admin']}action=move&amp;where=0&amp;forumID={$row[0]}\">&darr;</a><br />";

}
while($row=db_simpleSelect(1));
}

}
return $fast;
}

//-----

switch ($mode) {
case 'logout':
deleteMyCookie();
if(isset($metaLocation)) { $meta_relocate="{$main_url}/{$bb_admin}"; echo ParseTpl(makeUp($metaLocation));
exit; } else header("Location: {$main_url}/$bb_admin");

case 'login':
if ($mode=='login') {
if(!isset($_POST['adminusr'])) $_POST['adminusr']='';
if(!isset($_POST['adminpwd'])) $_POST['adminpwd']='';

if(strlen($admin_pwd)==32) { 
$encodePass=FALSE;
$comparePass=writeUserPwd($_POST['adminpwd']);
} else {
$encodePass=TRUE;
$comparePass=$_POST['adminpwd'];
}

//echo $comparePass;

if ($_POST['adminusr']==$admin_usr and $comparePass==$admin_pwd) {

$cook=$admin_usr.'|'.$comparePass.'|'.$cookieexptime;
deleteMyCookie();
setMyCookie($admin_usr,$admin_pwd,$cookieexptime,$encodePass);
if(isset($metaLocation)) { $meta_relocate="{$main_url}/{$bb_admin}"; echo ParseTpl(makeUp($metaLocation));
exit; } else header("Location: {$main_url}/{$bb_admin}");
}
else {
$warning=$l_incorrect_login;
}
} // if mode=login, for preventing login checkout

default:

$user_id=0;
user_logged_in();
if(isset($langu) and file_exists($pathToFiles."lang/{$langu}.php")) $lang=$langu;
include ($pathToFiles."lang/$lang.php");

if($user_id==1){

$l_adminpanel_link="<p><a href=\"{$main_url}/{$bb_admin}\">".$l_adminpanel."</a></p>";

$isMod=1;
$forum=0;
$topic=0;
if(!defined('PAGE1_OFFSET')) define('PAGE1_OFFSET', 0);
$page=PAGE1_OFFSET+1;
$user_usr=$admin_usr;
include ($pathToFiles.'bb_plugins.php');

switch ($action) {
case 'addforum1':
$iconList=getForumIcons();
$text2=ParseTpl(makeUp('admin_addforum1'));
break;

case 'addforum2':
$iconList=getForumIcons();
foreach($_POST as $key=>$val) {
if(get_magic_quotes_gpc()==0) $$key=addslashes(trim($val)); else $$key=trim($val);
$posts[$key]=htmlspecialchars(trim($val),ENT_QUOTES);
}

if($forum_name!='') {

$forum_name=strip_tags($forum_name);

if($forum_icon=='') $forum_icon='default.gif';

if (file_exists($pathToFiles."img/forum_icons/{$forum_icon}")) {

$topics_count=0; $posts_count=0;
if($mx=db_simpleSelect(0,$Tf,'count(*)')) $forum_order=$mx[0]+1; else $forum_order=0;

$er=insertArray(array('forum_name','forum_desc','forum_icon','forum_order','topics_count','posts_count', 'forum_group'),$Tf);

if ($er==0) $warning=$l_forum_added; else $warning=$l_itseemserror;
$text2=ParseTpl(makeUp('admin_panel'));
}
else {
if(!isset($forumicon)) $forumicon='';
$warning=$l_error_addforumicon."'".$forumicon."'";
$text2=ParseTpl(makeUp('admin_addforum1'));
}
}
else {
$warning=$l_error_addforum;
foreach($posts as $key=>$val) $$key=$val;
$text2=ParseTpl(makeUp('admin_addforum1'));
}
break;

case 'move':
if(isset($_GET['forumID'])) $forumID=$_GET['forumID']; else $forumID=0;
if(isset($_GET['where'])) $where=$_GET['where']; else $where=0;
$c=0;
$num=db_simpleSelect(0,$Tf,'count(*)'); $num=$num[0];

$forums=array();
if($row=db_simpleSelect(0,$Tf,'forum_id, forum_order','','','','forum_order')){
$a=1;
do { $forums[$a]=$row[0]; $a++; }
while($row=db_simpleSelect(1));

$ch=0;

if($where==1){
for($i=1; $i<=sizeof($forums); $i++) {
$d=$i-1;
if($forums[$i]==$forumID){
if(isset($forums[$d])) { $tmp=$forums[$d]; $forums[$d]=$forums[$i]; $forums[$i]=$tmp; $ch=1; }
else { $forums[$num+1]=$forums[$i]; unset($forums[$i]); $ch=1; }
}
if($ch==1) break;
}
}
elseif($where==0){
for($i=1; $i<=sizeof($forums); $i++) {
$d=$i+1;
if($forums[$i]==$forumID){
if(isset($forums[$d])) { $tmp=$forums[$d]; $forums[$d]=$forums[$i]; $forums[$i]=$tmp; $ch=1; }
else { $forums[0]=$forums[$i]; unset($forums[$i]); $ch=1; }
}
if($ch==1) break;
}
}

ksort($forums);
reset($forums);

$forum_order=1;
while(list($key,$val)=each($forums)) {
updateArray(array('forum_order'),$Tf,'forum_id',$val);
$forum_order++;
}

}

header("Location: {$main_url}/{$bb_admin}action=editforum2&forumID={$forumID}#bottom");
break;

case 'editforum1':
$frm=0;
include ($pathToFiles.'bb_func_forums.php');
$text2=ParseTpl(makeUp('admin_editforum1'));
break;

case 'editforum2':
if(isset($_POST['forumID'])) $forumID=$_POST['forumID']+0; elseif(isset($_GET['forumID'])) $forumID=$_GET['forumID']+0; else $forumID=0;
if ($forumID!=0) {

$forumsPreview=get_forums_fast_preview();

if ($row=db_simpleSelect(0,$Tf,'forum_name, forum_desc, forum_icon, forum_group','forum_id','=',$forumID)) {

foreach($row as $key=>$val) $row[$key]=htmlspecialchars($row[$key],ENT_QUOTES);
$forum_group=$row[3]; $forum_name=$row[0]; $forum_desc=$row[1]; $forum_icon=$row[2]; 

$iconList=getForumIcons();

$text2=ParseTpl(makeUp('admin_editforum2'));
}
else {
$warning=$l_noforums;
$text2=ParseTpl(makeUp('admin_panel'));
}
}
else {
$warning=$l_noforums;
$text2=ParseTpl(makeUp('admin_panel'));
}
break;

case 'editforum3':
$posts=array();
foreach($_POST as $key=>$val) {
if(get_magic_quotes_gpc()==0) $$key=addslashes(trim($val)); else $$key=trim($val);
$posts[$key]=htmlspecialchars(stripslashes(trim($val)),ENT_QUOTES);
}

if (!isset($_POST['deleteforum'])) {

if ($forum_name!='') {

$forum_name=strip_tags($forum_name);

if ($forum_icon=='') $forum_icon='default.gif';

if (!file_exists($pathToFiles."img/forum_icons/{$forum_icon}")) {
$warning=$l_error_addforumicon."'".$forum_icon."'";
}
else {

$fs=updateArray(array('forum_name','forum_desc','forum_icon','forum_group'),$Tf,'forum_id',$forumID);

if($fs>0) $warning=$l_forumUpdated; else $warning=$l_prefsNotUpdated;
}
} // if forum name is set
else {
$warning=$l_error_addforum;
}

$forumsPreview=get_forums_fast_preview();
$iconList=getForumIcons();
foreach($posts as $key=>$val) $$key=$val;
$text2=ParseTpl(makeUp('admin_editforum2'));
}
else {

$aff=0;

/* Amount of user topics */
$updatedUsers=array();
if($rrr=db_simpleSelect(0,$Tp,'poster_id','forum_id','=',$forumID,'','','poster_id','!=',0)){
do if(!in_array($rrr[0], $updatedUsers)) $updatedUsers[]=$rrr[0];
while($rrr=db_simpleSelect(1));
}

/* Sendmails */

if($rrr=db_simpleSelect(0,"$Tt,$Ts","$Tt.topic_id","$Tt.forum_id",'=',$forumID,'','',"$Tt.topic_id",'=',"$Ts.topic_id")){
$ord='';
do $ord.="topic_id={$rrr[0]} or "; while($rrr=db_simpleSelect(1));
$ord=substr($ord,0,strlen($ord)-4);
$aff+=db_delete($Ts,$ord,'','');
}

/* Forums, posts, topics */

$aff+=db_delete($Tf,'forum_id','=',$forumID);
$aff+=db_delete($Tt,'forum_id','=',$forumID);
$aff+=db_delete($Tp,'forum_id','=',$forumID);
foreach($updatedUsers as $uu) {
$aff+=db_calcAmount($Tp,'poster_id',$uu,$Tu,$dbUserSheme['num_posts'][1],$dbUserId);
$aff+=db_calcAmount($Tt,'topic_poster',$uu,$Tu,$dbUserSheme['num_topics'][1],$dbUserId);
}

if ($aff>0) $warning=$l_forumdeleted.' ("'.stripslashes($forum_name).'") - '."$l_del $aff $l_rows"; else $warning=$l_itseemserror;
$text2=ParseTpl(makeUp('admin_panel'));
}
break;

case 'removeuser1':
$userID=(isset($_GET['userID'])?$_GET['userID']:'');
$text2=ParseTpl(makeUp('admin_removeuser1'));
break;

case 'removeuser2':
$userID=(isset($_POST['userID'])?$_POST['userID']+0:0);

if ($userID==0 or !db_simpleSelect(0,$Tu,$dbUserId,$dbUserId,'=',$userID) or $userID==1 or $userID==0) $warning=$l_cantDeleteUser;
else {
if (db_delete($Tu,$dbUserId,'=',$userID)) $warning=$l_userDeleted." (".$userID.")"; else $warning=$l_userNotDeleted." (".$userID.")";

/*Delete from sendMails*/
db_delete($Ts,'user_id','=',$userID);

if (isset($_POST['removemessages'])) {
//set_time_limit(0);

$aff=0;
/*Deleting user messages from posts and topics table. Topics - delete also all associated posts*/
if($rrr=db_simpleSelect(0,$Tt,'topic_id','topic_poster','=',$userID)){
$ord='';
do $ord.="topic_id={$rrr[0]} or "; while($rrr=db_simpleSelect(1));
$ord=substr($ord,0,strlen($ord)-4);
$aff+=db_delete($Tp,$ord,'','');
$aff+=db_delete($Tt,$ord,'','');
}

/* Posts only */
if($rrr=db_simpleSelect(0,$Tp,'DISTINCT topic_id','poster_id','=',$userID)){
do{
$topic_id=$rrr[0];
$aff+=db_delete($Tp,'topic_id','=',$topic_id,'poster_id','=',$userID);
db_calcAmount($Tp,'topic_id',$topic_id,$Tt,'posts_count');
$RES1=$result;
$CNT1=$countRes;
if($lp=db_simpleSelect(0,$Tp,'post_id, post_time, poster_name','topic_id','=',$topic_id,'post_id DESC',1)){
$topic_last_post_id=$lp[0];
$topic_last_post_time=$lp[1];
$topic_last_poster=$lp[2];
$fs=updateArray(array('topic_last_post_id', 'topic_last_post_time', 'topic_last_poster'),$Tt,'topic_id',$topic_id);
$aff+=$fs;
}
$result=$RES1;
$countRes=$CNT1;
}
while($rrr=db_simpleSelect(1));
}

/* Update forums posts, topics amount */
if($res=db_simpleSelect(0,$Tf,'forum_id')){
do{
db_calcAmount($Tp,'forum_id',$res[0],$Tf,'posts_count');
db_calcAmount($Tt,'forum_id',$res[0],$Tf,'topics_count');
}
while($res=db_simpleSelect(1));
}

if ($aff>0) $warning.="<br />".$l_userMsgsDeleted; else $warning.="<br />".$l_userMsgsNotDeleted;
}
else {
/*Make user posts as anonymous*/
$aff=0;
$poster_id=0; $topic_poster=0;
$aff+=updateArray(array('poster_id'),$Tp,'poster_id',$userID);
$aff+=updateArray(array('topic_poster'),$Tt,'topic_poster',$userID);
if ($aff>0) $warning.="<br />".$l_userUpdated0; else $warning.="<br />".$l_userNotUpdated0;
}

}

$text2=ParseTpl(makeUp('admin_panel'));
break;

case 'delsendmails1':
if (!isset($_POST['warning'])) $warning='';
if (!isset($_POST['delemail'])) $delemail='';
$text2=ParseTpl(makeUp('admin_sendmails1'));
break;

case 'delsendmails2':
$delemail=(isset($_POST['delemail'])?htmlspecialchars($_POST['delemail'],ENT_QUOTES):'');

if($delemail!='' and $rw=db_simpleSelect(0,$Tu,$dbUserId,$dbUserSheme['user_email'][1],'=',$delemail)) {
$fs=db_delete($Ts,'user_id','=',$rw[0]);
$row=$delemail;
}
elseif($delemail=='') {
$fs=db_delete($Ts);
$row='ALL';
}
else {
$warning=$l_emailNotExists;
$text2=ParseTpl(makeUp('admin_sendmails1'));
break;
}

$warning=$l_completed." ($row)";
$text2=ParseTpl(makeUp('admin_panel'));
break;

case 'restoreData':
${$dbUserSheme['username'][1]}=$admin_usr;
${$dbUserSheme['user_password'][1]}=writeUserPwd($admin_pwd);
${$dbUserSheme['user_email'][1]}=$admin_email;
${$dbUserDate}=date('Y-m-d H:i:s');
$fields=array($dbUserSheme['username'][1],$dbUserSheme['user_password'][1],$dbUserSheme['user_email'][1]);
if($res=db_simpleSelect(0,$Tu,$dbUserId,$dbUserId,'=',1)) {$ins=1; $fs=updateArray($fields,$Tu,$dbUserId,1); }
else {$fields[]=$dbUserDate; $fields[]=$dbUserId; ${$dbUserId}=1; $ins=0; $fs=insertArray($fields,$Tu); }
if (($fs>0 and $ins==1) OR ($fs==0 and $ins==0)) $warning=$l_prefsUpdated; else $warning=$l_prefsNotUpdated;
$text2=ParseTpl(makeUp('admin_panel'));
break;

case 'banUsr1':
$warning='';
$banip=(isset($_GET['banip'])?$_GET['banip']:'');
$text2=ParseTpl(makeUp('admin_banusr1'));
break;

case 'banUsr2':
$warning='';
foreach($_POST as $key=>$val) $$key=htmlspecialchars(trim($val),ENT_QUOTES);
if (preg_match("/^[0-9.+]+$/", $banip) and trim($banip)!='0') {
$thisIp=$banip; $thisIpMask=array($banip,$banip);
if(db_ipCheck($thisIp,$thisIpMask,$user_id)) $warning=$l_IpExists;
else{
$fs=insertArray(array('banip','banreason'),$Tb);
$warning=($fs==0?$l_IpBanned:$l_mysql_error);
}
$text2=makeUp('admin_panel');
}
else{
$warning=$l_incorrectIp;
$text2=makeUp('admin_banusr1');
}
$text2=ParseTpl($text2);
break;

case 'deleteban1':
$warning='';
$banipID='';
$bannedIPs='';
if ($banned=db_simpleSelect(0,$Tb,'id,banip,banreason','','','','banip')) {
do {
if(trim($banned[2])!='') $banned[2]='('.$banned[2].')';
$bannedIPs.='<input type="checkbox" name="banip['.$banned[0].']" />&nbsp;&nbsp;'.$banned[1].'&nbsp;'.$banned[2]."<br />\n";
}
while($banned=db_simpleSelect(1));
$text2=makeUp('admin_deleteban1');
}
else {
$warning=$l_noBans;
$text2=makeUp('admin_panel');
}
$text2=ParseTpl($text2);
break;

case 'deleteban2':
$banip=(isset($_POST['banip'])?$_POST['banip']:array());
$i=0;
$row=0;
if (sizeof($banip)>0) {
while (list($key)=each($banip)) {
$delban[$i]=$key;
$i++;
}
$xtr=getClForums($delban,'','','id','or','=');
$row=db_delete($Tb,$xtr);
}
$warning=$l_completed.' ('.$row.')';
$text2=ParseTpl(makeUp('admin_panel'));
break;

case 'exportemails':
if (db_simpleSelect(0,$Tu,$dbUserId,$dbUserId,'!=',1)) { $text2=makeUp('admin_export_emails'); }
else { $warning=$l_accessDenied; $text2=makeUp('admin_panel'); }
$text2=ParseTpl($text2);
break;

case 'exportemails2':
if ($row=db_simpleSelect(0,$Tu,$dbUserSheme['username'][1].','.$dbUserSheme['user_email'][1],$dbUserId,'!=',1,$dbUserId) and isset($_POST['expEmail'])) {
$cont='';
do {
$cont.=$row[1];
if (isset($_POST['expLogin'])) {
if ($_POST['separate']=='comma') $sep=','; else $sep=chr(9);
$cont.=$sep.$row[0];
}
if ($_POST['screen']==1) $cont.='<br />'; else $cont.="\r\n";
}
while ($row=db_simpleSelect(1));

if ($_POST['screen']==0) {
header("Content-Type: DUMP/unknown");
header("Content-Disposition: attachment; filename=".str_replace(' ', '_', $sitename)."_emails.txt");
}
echo $cont; exit;
}
$text2=ParseTpl(makeUp('admin_panel'));
break;

case 'searchusers':
$searchus='id';
$whatDropDown=makeValuedDropDown(array('id'=>'ID','login'=>$l_sub_name,'email'=>$l_email,'inactive'=>$l_inactiveUsers,'registr'=>$l_haventReg, 'notmember'=>$l_member.': ['.$l_no.']'),'searchus');
$warning='';
$text2=ParseTpl(makeUp('admin_searchusers'));
break;

case 'searchusers2':

if(isset($_GET['searchus'])) $searchus=$_GET['searchus']; elseif(isset($_POST['searchus'])) $searchus=$_POST['searchus']; else $searchus='';
if(isset($_GET['whatus'])) $whatus=$_GET['whatus']; elseif(isset($_POST['whatus'])) $whatus=$_POST['whatus']; else $whatus='';
if($whatus!='') $whatus=str_replace(array('>', '<', '%3C', '%3E'), '', $whatus);
if(isset($_GET['page'])) $page=(int)$_GET['page']+0; elseif(isset($_POST['page'])) $page=(int)$_POST['page']+0; else $page=PAGE1_OFFSET+1;
if($page<PAGE1_OFFSET+1) $page=PAGE1_OFFSET+1;

/* Delete users if selected */

if(isset($_POST['delus']) and is_array($_POST['delus']) and sizeof($_POST['delus'])>0) {
$newarr=array();
foreach($_POST['delus'] as $dl) if($dl!=1 and $dl!=0) $newarr[]=$dl;

if(isset($_POST['anchor']) and $_POST['anchor']!=0) $anchor='#u'.($_POST['anchor']+0); else $anchor='';

$xtr=getClForums($newarr,'','',$dbUserId,'or','=');
$row=db_delete($Tu,$xtr);
$row=db_delete($Ts,$xtr);
header("Refresh:0; url={$main_url}/{$bb_admin}action={$action}&searchus={$searchus}&whatus={$whatus}&page={$page}{$anchor}");
exit;
}


$tR=makeUp('admin_searchusersres');

$whatDropDown=makeValuedDropDown(array('id'=>'ID','login'=>$l_sub_name,'email'=>$l_email,'inactive'=>$l_inactiveUsers,'registr'=>$l_haventReg, 'notmember'=>$l_member.': ['.$l_no.']'),'searchus');

$totch=0;
/* for any "dead" users found, we will display a checkbox, which will produce a possibility to delete many users fast at once */

$Results='';
$idArray='';

/* All users */
if($whatus=='' and $searchus!='inactive' and $searchus!='registr' and $searchus!='notmember' and $num=db_simpleSelect(0,$Tu,'count(*)')){

$num=$num[0];
$makeLim=makeLim($page,$num,$viewmaxsearch);
$pageNav=pageNav($page,$num,"{$main_url}/{$bb_admin}action=searchusers2",$viewmaxsearch,FALSE);

if ($row=db_simpleSelect(0,$Tu,$dbUserId.','.$dbUserSheme['username'][1].','.$dbUserDate.','.$dbUserSheme['user_password'][1].','.$dbUserSheme['user_email'][1].', '.$dbUserSheme['num_posts'][1],'','','',$dbUserId,$makeLim)){

do {
$numReplies=$row[5];
$lReplies='&mdash;';
$idArray.=$row[0].', ';

if($numReplies>0){

$delCheckBox='';

$RES1=$result;
$CNT1=$countRes;

if ($lRepl=db_simpleSelect(0,$Tp,'post_time','poster_id','=',$row[0],'post_id DESC',1)) $lReplies=convert_date($lRepl[0]);

}
else {
$totch++;
$delCheckBox="<input type=\"checkbox\" name=\"delus[]\" value=\"{$row[0]}\" />&nbsp;";
}

$Rest=$tR;
$rDate=convert_date($row[2]);
$Results.=ParseTpl($Rest);

if($numReplies>0){
$result=$RES1;
$countRes=$CNT1;
}

}
while ($row=db_simpleSelect(1));
}
$warning=$l_recordsFound.' '.$num;
}

/* Determine all inactive users, who have posted NOTHING */
elseif ($searchus=='inactive'){
$whatus='';
$makeLim='';
$num=0;
if ($num=db_inactiveUsers(0,'count(*)')) $num=$num[0];
$makeLim=makeLim($page,$num,$viewmaxsearch);
$pageNav=pageNav($page,$num,"{$main_url}/{$bb_admin}action=searchusers2&amp;whatus={$whatus}&amp;searchus=inactive",$viewmaxsearch,FALSE);

//index of num_replies
$nr=$dbUserSheme['num_posts'][0];

if ($row=db_inactiveUsers(0,'*')){
$tot=0;
do {
$idArray.=$row[0].', ';
$totch++;
$delCheckBox="<input type=\"checkbox\" name=\"delus[]\" value=\"{$row[0]}\" />&nbsp;";
$Rest=$tR;
$lReplies='&mdash;';
$numReplies=$row[$nr];
$rDate=convert_date($row[2]);
$Results.=ParseTpl($Rest);
$tot++;
}
while($row=db_inactiveUsers(1));
}
$warning=$l_recordsFound.' '.$num;
}

/* Search users by email or username (LIKE condition) */
elseif ($searchus=='email' OR $searchus=='login'){
$tot=0;
$whatx=($searchus=='email'?$dbUserSheme['user_email'][1]:$dbUserSheme['username'][1]);

$makeLim='';
$num=0;
if ($num=db_simpleSelect(0, $Tu, 'count(*)', $whatx, ' like ', '%'.$whatus.'%')) $num=$num[0];
$makeLim=makeLim($page,$num,$viewmaxsearch);
$pageNav=pageNav($page,$num,"{$main_url}/{$bb_admin}action=searchusers2&amp;whatus={$whatus}&amp;searchus=$searchus",$viewmaxsearch,FALSE);

if($row=db_simpleSelect(0,$Tu,$dbUserId.','.$dbUserSheme['username'][1].','.$dbUserDate.','.$dbUserSheme['user_password'][1].','.$dbUserSheme['user_email'][1].', '.$dbUserSheme['num_posts'][1], $whatx, ' like ', '%'.$whatus.'%', "{$dbUserId} ASC", $makeLim)){

do {
$user=$row[0];
$idArray.=$row[0].', ';

$numReplies=$row[5];
$lReplies='&mdash;';

if($numReplies>0){

$delCheckBox='';

$RES1=$result;
$CNT1=$countRes;

if ($lRepl=db_simpleSelect(0,$Tp,'post_time','poster_id','=',$row[0],'post_id DESC',1)) $lReplies=convert_date($lRepl[0]);

}
else {
$totch++;
$delCheckBox="<input type=\"checkbox\" name=\"delus[]\" value=\"{$row[0]}\" />&nbsp;";
}

$Rest=$tR;

$rDate=convert_date($row[2]);
$Results.=ParseTpl($Rest);
$tot++;

if($numReplies>0){
$result=$RES1;
$countRes=$CNT1;
}

}
while ($row=db_simpleSelect(1));
}
$warning=$l_recordsFound.' '.$tot;
}

/* Searching by dead users */
elseif ($searchus=='registr') {
$num=0;
if (!preg_match("/^[12][019][0-9][0-9]-[01][0-9]-[0123][0-9]$/", $whatus)) $warning=$l_wrongData;
else{
$less=$whatus.' 00:00:00';
if($row=db_deadUsers(0,$less)){
$num=$countRes;
$makeLim=makeLim($page,$num,$viewmaxsearch);
$pageNav=pageNav($page,$num,"{$main_url}/{$bb_admin}action=searchusers2&amp;whatus={$whatus}&amp;searchus=registr",$viewmaxsearch,FALSE);

$row=db_deadUsers(0,$less);
do{
$idArray.=$row[0].', ';
$Rest=$tR;
$rDate=convert_date($row[2]);
$lReplies=$row[5];
$numReplies=$row[6];

$Results.=ParseTpl($Rest);
}
while($row=db_deadUsers(1,$less));
}
$warning=$l_recordsFound.' '.$num;
}
}

/* Determine all disabled users */
elseif ($searchus=='notmember'){
$makeLim='';
$num=0;
if ($row=db_simpleSelect(0, $Tu, 'count(*)', $dbUserAct, '=', '0')) $num=$row[0];
$makeLim=makeLim($page,$num,$viewmaxsearch);
$pageNav=pageNav($page,$num,"{$main_url}/{$bb_admin}action=searchusers2&amp;whatus={$whatus}&amp;searchus=notmember",$viewmaxsearch,FALSE);

if ($row=db_simpleSelect(0, $Tu, $dbUserId.','.$dbUserSheme['username'][1].','.$dbUserDate.','.$dbUserSheme['user_password'][1].','.$dbUserSheme['user_email'][1].', '.$dbUserSheme['num_posts'][1], $dbUserAct, '=', '0', "{$dbUserId} ASC", $makeLim)){

$tot=0;
$whatus='';

do {
$Rest=$tR;
$idArray.=$row[0].', ';
$numReplies=$row[5];

if($numReplies>0){

$delCheckBox='';

$RES1=$result;
$CNT1=$countRes;

if ($lRepl=db_simpleSelect(0,$Tp,'post_time','poster_id','=',$row[0],'post_id DESC',1)) $lReplies=convert_date($lRepl[0]);

}
else {
$totch++;
$delCheckBox="<input type=\"checkbox\" name=\"delus[]\" value=\"{$row[0]}\" />&nbsp;";
}

$rDate=convert_date($row[2]);
$Results.=ParseTpl($Rest);
$tot++;

if($numReplies>0){
$result=$RES1;
$countRes=$CNT1;
}

}
while($row=db_simpleSelect(1));

}
$warning=$l_recordsFound.' '.$num;
}

/* Searching by user ID */
else{
$tot=0;
if($row=db_simpleSelect(0,$Tu,$dbUserId.','.$dbUserSheme['username'][1].','.$dbUserDate.','.$dbUserSheme['user_password'][1].','.$dbUserSheme['user_email'][1].', '.$dbUserSheme['num_posts'][1],$dbUserId,'=',$whatus)){
$Results=makeUp('admin_searchusersres');
$rDate=convert_date($row[2]);
$numReplies=$row[5];

if ($numReplies>0 and $lRepl=db_simpleSelect(0,$Tp,'post_time','poster_id','=',$row[0],'post_id DESC',1)) $lReplies=convert_date($lRepl[0]); else $lReplies='&mdash;';
$tot++;
$Results=ParseTpl($Results);
}
$warning=$l_recordsFound.' '.$tot;
}

if($Results!='') $Results='<ul>'.$Results.'</ul>';
if($idArray!='') $idArray="var usx=new Array(".substr($idArray,0,strlen($idArray)-2).");\n";
else $idArray='var usx=new Array();';

if($totch>0){
$Results=<<<out
<script type="text/javascript">
<!--
{$idArray}

function turnAllLayers(sw){
var el=document.searchForm.elements;
var len=el.length;
for(var i=0;i<len;i++){
if (el[i].name.substring(0,5)=='delus'){el[i].checked=sw}
}
}

function submitAnch(){
var tmpn, fin;
var el=document.searchForm.elements;
var len=el.length;
for(var i=0;i<len;i++){
if (el[i].name.substring(0,5)=='delus' && el[i].checked) {tmpn=el[i].value;break;}
}
fin=0;
for (x in usx){ if(usx[x]<tmpn) fin=usx[x]; }
document.searchForm.anchor.value=fin;
document.searchForm.submit();
return;
}

//-->
</script>
<form action="{$main_url}/{$bb_admin}" method="post" name="searchForm">
{$Results}
<input type="hidden" name="action" value="{$action}" />
<input type="hidden" name="page" value="{$page}" />
<input type="hidden" name="searchus" value="{$searchus}" />
<input type="hidden" name="whatus" value="{$whatus}" />
<input type="hidden" name="anchor" value="0" />
<input type="button" value="{$l_delete}" class="inputButton" onclick="JavaScript:submitAnch();" />
<input type="button" value="+" onclick="turnAllLayers(true);" class="inputButton" />
<input type="button" value="-" onclick="turnAllLayers(false);" class="inputButton" />
</form>
out;
}


$text2=ParseTpl(makeUp('admin_searchusers'));
break;

case 'viewsubs':
$topic=(isset($_GET['topic'])?$_GET['topic']+0:0);
$text2='';
if($tt=db_simpleSelect(0,$Tt,'topic_title','topic_id','=',$topic)){
$topicTitle=$tt[0];
$listSubs='';

if ($row=db_simpleSelect(0,"$Ts,$Tu","$Ts.id,$Ts.user_id,$Tu.{$dbUserSheme['username'][1]},$Tu.{$dbUserSheme['user_email'][1]},$Ts.active",'topic_id','=',$topic,'','',"$Ts.user_id",'=',"$Tu.$dbUserId")){
$listSubs="<form action=\"{$main_url}/{$bb_admin}\" method=\"post\" class=\"formStyle\"><input type=\"hidden\" name=\"action\" value=\"viewsubs2\" />
<input type=\"hidden\" name=\"topic\" value=\"$topic\" />";
do {
if($row[4]==0) $s='<span class="warning"><b>-</b></span>'; else $s='+';
$listSubs.="<br /><input type=\"checkbox\" name=\"selsub[]\" value=\"{$row[0]}\" /><span class=\"txtSm\"><a href=\"{$main_url}/{$indexphp}action=userinfo&amp;user={$row[1]}\">{$row[2]}</a> (<a href=\"mailto:{$row[3]}\">{$row[3]}</a>) [{$s}]</span>\n";
}
while ($row=db_simpleSelect(1));
$listSubs.="<br /><br />&nbsp;<input type=\"submit\" value=\"$l_deletePost\" class=\"inputButton\" /></form>\n";
}

$text2=ParseTpl(makeUp('admin_viewsubs'));
}
break;

case 'viewsubs2':
$fs=0;
if(isset($_POST['selsub']) and sizeof($_POST['selsub'])>0){
$xtr=getClForums($_POST['selsub'],'','','id','or','=');
$fs=db_delete($Ts,$xtr);
}
$errorMSG=$l_subscriptions.': '.$l_del.' '.$fs.' '.$l_rows;
$correctErr="<a href=\"{$indexphp}action=vthread&amp;topic={$_POST['topic']}#newreply\">$l_back</a>";
$text2=ParseTpl(makeUp('main_warning'));
break;

default:
$warning='';
$text2=ParseTpl(makeUp('admin_panel'));
} // end of switch
}
else {
if (!$warning) $warning=$l_enter_admin_login;
$text2=ParseTpl(makeUp('admin_login'));
}

} // end of switch

echo load_header();
echo $text2;
include ($pathToFiles.'bb_plugins2.php');

$endtime=get_microtime();
$totaltime=sprintf ("%01.3f", ($endtime-$starttime));
$currY=date('Y');
if(isset($includeFooter) and $includeFooter!='') include($includeFooter); else echo ParseTpl(makeUp('main_footer'));
?>