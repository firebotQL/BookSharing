<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2007 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2007-Dec-26
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

$allowForm=($user_id==1 or $isMod==1);
$c1=(in_array($forum,$clForums) and isset($clForumsUsers[$forum]) and !in_array($user_id,$clForumsUsers[$forum]) and !$allowForm);
$c2=(isset($allForumsReg) and $allForumsReg and $user_id==0);
$c4=(isset($roForums) and in_array($forum, $roForums) and !$allowForm);
$c5=(isset($regUsrForums) and in_array($forum, $regUsrForums) and $user_id==0);

if ($c1 or $c2 or $c4 or $c5) {
$errorMSG=$l_forbidden; $correctErr=$backErrorLink;
$title=$title.$l_forbidden; $loginError=1;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}

if(!$user_usr) $user_usr=$l_anonymous;
if(!isset($TT)) $TT='';

if(trim($_POST['postText'])=='') $postText=$TT; else $postText=trim($_POST['postText']);
$postText=str_replace(array('&#032;', '&#32;'), '', $postText);

if(!isset($post_text_minlength)) $post_text_minlength=10;

//Check if topic is not locked
if($topic_d=db_simpleSelect(0,$Tt,'topic_status,topic_title,posts_count','forum_id','=',$forum,'','','topic_id','=',$topic)) { $lckt=$topic_d[0]; $topicTitle=$topic_d[1]; $topicTitle_em=convEnt($topic_d[1]); $totalPosts=$topic_d[2]; } else $lckt=1;

if($lckt==1 or $lckt==8) {
$errorMSG=$l_forbidden;
$antiWarn=$l_movedWhilePost;
$fieldsReadOnly=1;
$displayFormElements=array('topicTitle'=>1, 'postText'=>1);
$title=$title.$l_forbidden; $loginError=1;
include($pathToFiles.'bb_func_posthold.php');
echo load_header(); echo ParseTpl(makeUp('main_posthold')); return;
}
else {

if(!isset($_POST['disbbcode']) or (isset($_POST['disbbcode']) and $_POST['disbbcode']=='') ) $disbbcode=FALSE; else $disbbcode=TRUE;
$postText=textFilter($postText,$post_text_maxlength,$post_word_maxlength,1,$disbbcode,1,$user_id);

if ($postText=='') {
//Insert user into email notifies if allowed
if (isset($emptySubscribe) and $emptySubscribe and $user_id!=0 and isset($_POST['CheckSendMail']) and emailCheckBox()!='' and substr(emailCheckBox(),0,8)!='<!--U-->') {
$ae=db_simpleSelect(0,$Ts,'count(*)','user_id','=',$user_id,'','','topic_id','=',$topic); $ae=$ae[0];
if($ae==0) { $topic_id=$topic; insertArray(array('user_id','topic_id'),$Ts); }
}
return;
}

$compareTL=strlen(trim(strip_tags($postText)));
$sce=FALSE; if(isset($simpleCodes)) foreach($simpleCodes as $e) { if(substr_count($postText, $e)>0) { $sce=TRUE; break; } }

if( ($compareTL==0 or ($compareTL>0 and $compareTL<$post_text_minlength)) and !$sce) {
$errorMSG=$l_emptyPost; $correctErr=$backErrorLink;
$title.=$l_forbidden; $loginError=1;
echo load_header(); echo ParseTpl(makeUp('main_warning')); return;
}

$poster_ip=$thisIp;

//Posting query with anti-spam protection

if($postRange==0) $antiSpam=0; else {
if($user_id==0) $fields=array('poster_ip',$poster_ip); else $fields=array('poster_id',$user_id);
if($asTime=db_simpleSelect(0, $Tp, 'post_time', $fields[0], '=', $fields[1], 'post_id DESC', '1')) {
$asTime=time()-strtotime($asTime[0]); if($asTime<=$postRange) $antiSpam=1; else $antiSpam=0;
}
else $antiSpam=0;
}

if( ($user_id==1 or $isMod==1) or $antiSpam==0) {

$forum_id=$forum;
$topic_id=$topic;
$poster_id=$user_id;
$poster_name=$user_usr;
$post_text=$postText;
if(defined('TOPIC_TIME')) $post_time=constant('TOPIC_TIME'); else $post_time=date('Y-m-d H:i:s');
$post_status=0;

$inss=insertArray(array('forum_id', 'topic_id', 'poster_id', 'poster_name', 'post_text', 'post_time', 'poster_ip', 'post_status'),$Tp);

if($inss==0){
$topic_last_post_id=$insres;
$topic_last_post_time=$post_time;
$topic_last_poster=$poster_name;
if(updateArray(array('topic_last_post_id', 'topic_last_post_time', 'topic_last_poster'),$Tt,'topic_id',$topic)>0){
db_calcAmount($Tp,'forum_id',$forum,$Tf,'posts_count');
db_calcAmount($Tp,'topic_id',$topic,$Tt,'posts_count');
if($user_id!=0) db_calcAmount($Tp,'poster_id',$user_id,$Tu,$dbUserSheme['num_posts'][1],$dbUserId);
}

$tmpUe=$emailusers;
if (isset($preModerationType) and $preModerationType>0) $emailusers=0;

if ($genEmailDisable!=1 and ($emailusers>0 or (isset($emailadmposts) and $emailadmposts==1))) {
if(!isset($reply_to_email)) $reply_to_email=$admin_email;

if($fn=db_simpleSelect(0,$Tf,'forum_name','forum_id','=',$forum)) { $forum_title_em=convEnt($fn[0]); $forum_title=$fn[0]; } else $forum_title='';
$setTpls=array();
$pTxtSm=convEnt($postText);
$user_usr_em=convEnt($user_usr);
$postTextSmall=substr($pTxtSm,0,200);
if(strlen($postTextSmall)<strlen($pTxtSm)) $postTextSmall.='...';
$setTpls[$langOrig]=ParseTpl(makeUp('email_reply_notify_'.$langOrig));
//$msg=$setTpls[$langOrig];
$sub0=explode('SUBJECT>>', $setTpls[$langOrig]);
$sub0=explode('<<', $sub0[1]);
$msgg=explode('[USER_ID]', trim($sub0[1]));
$msg[$langOrig][0]=$msgg[0]; $msg[$langOrig][1]=$msgg[1];
$sub[$langOrig]=$sub0[0];
}

//Email all users about this reply if allowed
if($genEmailDisable!=1 and $emailusers>0) {

$allUsers=array();
if($row=db_simpleSelect(0,$Ts,'user_id,active','topic_id','=',$topic, '', '', 'user_id', '!=', $user_id)) do if($row[1]==1) $allUsers[]=$row[0]; while($row=db_simpleSelect(1));
$xtr=getClForums($allUsers,'where','',$dbUserId,'OR','=');
$allUsers=array();
if($row=db_simpleSelect(0,$Tu,"{$dbUserId}, {$dbUserSheme['user_email'][1]}, {$dbUserSheme['language'][1]}")) do $allUsers[$row[0]]=array($row[1], $row[2]); while($row=db_simpleSelect(1));
unset($xtr);

foreach($allUsers as $k=>$v){

if($emailusers==2){
/* Send email on user's language */
$eFile='email_reply_notify_'.$v[1];

if(file_exists($pathToFiles.'templates/'.$eFile.'.txt')) {

if(!isset($setTpls[$v[1]])) {
$setTpls[$v[1]]=ParseTpl(makeUp($eFile));
$sub0=explode('SUBJECT>>', $setTpls[$v[1]]);
$sub0=explode('<<', $sub0[1]);
$msgg=explode('[USER_ID]', trim($sub0[1]));
$msg[$v[1]][0]=$msgg[0]; $msg[$v[1]][1]=$msgg[1];
$sub[$v[1]]=$sub0[0];
}

}//file exists
else $v[1]=$langOrig;
}//send on user language
else $v[1]=$langOrig;

$subS=$sub[$v[1]];
$msgS=$msg[$v[1]][0].$k.$msg[$v[1]][1];

sendMail($v[0], $subS, $msgS, $reply_to_email, $reply_to_email);
}//foreach

if(sizeof($allUsers)>0){
$active=0;
updateArray(array('active'),$Ts,'topic_id',$topic);
}

}//email users

//Email admin if allowed
if ($genEmailDisable!=1 and isset($emailadmposts) and $emailadmposts==1 and $user_id!=1) {
$subS=$sub[$langOrig];
$msgS=$msg[$langOrig][0].'1'.$msg[$langOrig][1];
sendMail($admin_email, $subS, $msgS, $reply_to_email, $reply_to_email);
}

unset($setTpls);

$emailusers=$tmpUe;
$insresOrig=$insres;
//Insert user into email notifies if allowed
if (isset($_POST['CheckSendMail']) and emailCheckBox()!='' and substr(emailCheckBox(),0,8)!='<!--U-->') {
$ae=db_simpleSelect(0,$Ts,'count(*)','user_id','=',$user_id,'','','topic_id','=',$topic); $ae=$ae[0];
if($ae==0) { $topic_id=$topic; insertArray(array('user_id','topic_id'),$Ts); }
}
$insres=$insresOrig;

}//inserted post successfully

}
else {
$errorMSG=$l_antiSpam;
$title.=$l_antiSpam;

$displayFormElements=array('topicTitle'=>1, 'postText'=>1);
$antiWarn=$l_antiSpamWait;
$antiSpam=1;
include($pathToFiles.'bb_func_posthold.php');

echo load_header(); echo ParseTpl(makeUp('main_posthold')); return;
}

$anchor=$topic_last_post_id;
$totalPosts=$topic_d[2]+1;

}
?>