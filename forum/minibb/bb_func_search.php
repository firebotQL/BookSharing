<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2005-2008 Paul Puzyrev. www.minibb.com
Latest File Update: 2009-Jun-23
*/

if (!defined('INCLUDED776')) die ('Fatal error.');

$dstr=200; // how much text to cut from beginning and end

if($row=db_simpleSelect(0,$Tp,'post_time','','','','post_id ASC',1)) $pDate=$row[0]; else $pDate='2002-02-05 12:40:06';
$pDate=explode(' ',$pDate); $pDate=explode('-',$pDate[0]);

$startYear=$pDate[0]+0;
$startMonth=$pDate[1]+0;
$startDay=$pDate[2]+0;

if(isset($_GET['loc']) and $_GET['loc']==1){
if(isset($themeDesc) and in_array($topic,$themeDesc)) $vv=TRUE; else $vv=FALSE;
if(!$vv) $sg='<='; else $sg='>=';
$anchor=$page;

if($row=db_simpleSelect(0, $Tp, 'count(*)', 'post_id', $sg, $page, '', '', 'topic_id', '=', $topic)) $pt=$row[0]; else $pt=0;

if($pt<=$viewmaxreplys) $page=PAGE1_OFFSET+1;
//elseif((integer)($pt/$viewmaxreplys)==($pt/$viewmaxreplys)) $page=$pt/$viewmaxreplys+PAGE1_OFFSET;
else $page=ceil($pt/$viewmaxreplys)+PAGE1_OFFSET;

if(isset($mod_rewrite) and $mod_rewrite) $furl=addTopicURLPage(genTopicURL($main_url, $forum, '#GET#', $topic, '#GET#'), $page)."#msg{$anchor}";
else $furl="{$main_url}/{$indexphp}action=vthread&forum={$forum}&topic={$topic}&page={$page}#msg{$anchor}";
header("Location: {$furl}");
exit;
}

foreach(array('tbSearchType', 'tbSearchWhere', 'tbSearchForum', 'tbSearchDate', 'tbSearchPoster') as $v) ${'view'.$v}='0';

if(isset($_GET['phrase'])) $phrase=htmlspecialchars(str_replace(array('%', "\'"), array('', "'"), trim($_GET['phrase'])),ENT_QUOTES); else $phrase='';

if(strlen($phrase)<3) $phrase='';

if(isset($_GET['posterName']) and trim($_GET['posterName'])!='') {
$posterName=htmlspecialchars(stripslashes(trim($_GET['posterName'])),ENT_QUOTES);
$viewtbSearchPoster=1;
}
else {
$posterName='';
$viewtbSearchPoster=0;
}

if(isset($_GET['where']) and (int)$_GET['where']!=1) {
$where=(int)$_GET['where'];
$viewtbSearchWhere=1;
}
else {
$where=1;
$viewtbSearchWhere=0;
}
if($where!=0 and $where!=1) $where=0;

if(isset($_GET['searchType']) and (int)$_GET['searchType']!=3) {
$searchType=(int)$_GET['searchType'];
$viewtbSearchType=1;
}
else {
$searchType=3;
$viewtbSearchType=0;
}

if(isset($_GET['searchGo'])) $searchGo=(integer) $_GET['searchGo']+0; else $searchGo=0;

if(isset($_GET['sDay'])) $sDay=(integer) $_GET['sDay']+0; else $sDay=$startDay;
if(isset($_GET['sDay'])) $sMonth=(integer) $_GET['sMonth']+0; else $sMonth=$startMonth;
if(isset($_GET['sYear'])) $sYear=(integer) $_GET['sYear']+0; else $sYear=$startYear;

if(isset($_GET['eDay'])) $eDay=(integer) $_GET['eDay']+0; else $eDay=date('j');
if(isset($_GET['eDay'])) $eMonth=(integer) $_GET['eMonth']+0; else $eMonth=date('n');
if(isset($_GET['eYear'])) $eYear=(integer) $_GET['eYear']+0; else $eYear=date('Y');

$st=0;
$frm=$forum;

if (isset($clForumsUsers)) $closedForums=getAccess($clForums, $clForumsUsers, $user_id); else $closedForums='n';
if ($closedForums!='n') $xtr=getClForums($closedForums,'where','','forum_id','and','!='); else $xtr='';

$forums=array();
$forums[0]='&mdash;';
if($row=db_simpleSelect(0, $Tf, 'forum_id, forum_name', '','','', 'forum_order')) { do { $forums[$row[0]]=$row[1]; } while ($row=db_simpleSelect(1)); }

$forum=$frm;
include($pathToFiles.'bb_func_forums.php');

//$forumDropDown=makeValuedDropDown($forums,'forum');
$whereDropDown=makeValuedDropDown(array(1=>$l_search[5], 0=>$l_search[4]),'where');

$sDays=array();
for($i=1;$i<32;$i++) $sDays[$i]=$i;
$sMonths=array();
for($i=1;$i<12;$i++) $sMonths[$i]=$i;
$sYears=array();
for($i=$startYear;$i<=date('Y');$i++) $sYears[$i]=$i;

$l_amonths=array();
$mm=explode(':',$l_months);
for($i=0;$i<12;$i++) $l_amonts[$i+1]=$mm[$i];

$sDayDropDown=makeValuedDropDown($sDays,'sDay');
$sMonthDropDown=makeValuedDropDown($l_amonts,'sMonth');
$sYearDropDown=makeValuedDropDown($sYears,'sYear');

$eDayDropDown=makeValuedDropDown($sDays,'eDay');
$eMonthDropDown=makeValuedDropDown($l_amonts,'eMonth');
$eYearDropDown=makeValuedDropDown($sYears,'eYear');

$searchTypeDroDown=makeValuedDropDown(array(0=>$l_search[7], 3=>$l_search[13], 2=>$l_search[9]), 'searchType');

$title=$title.$l_searchSite;
if($phrase!='') $title.=' - '.$phrase;
if($page>PAGE1_OFFSET+1) $title.=' - '.$l_page.' '.($page-PAGE1_OFFSET);

if($searchGo==1){

function strtolower_rus($text){
$capsArray=array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', '¨', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß');
$lowerArray=array('à', 'á', 'â', 'ã', 'ä', 'å', '¸', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', '÷', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ');
return str_replace($capsArray, $lowerArray, strtolower($text));
}

function getBytes($text,$word){
//text and word must be in lowercase
$len=strlen($word);
for($i=0;$i<=strlen($text);$i++){
if(substr($text,$i,$len)==$word) { $GLOBALS['bytes'][0][]=$i; $GLOBALS['bytes'][1][]=$i+$len; }
}
}

function boldText($text,$bytes){
$final=''; $cls=TRUE;
for($i=0;$i<=strlen($text);$i++){
if(is_array($bytes) and isset($bytes[0]) and is_array($bytes[0]) and in_array($i,$bytes[0])) { $final.='<strong>'; $cls=FALSE; }
if(is_array($bytes) and isset($bytes[1]) and is_array($bytes[1]) and in_array($i,$bytes[1])) { $final.='</strong>'; $cls=TRUE; }
if(isset($text[$i])) $final.=$text[$i];
}
if(!$cls) $final.='</strong>';
return $final;
}

function cutText($text,$bytes){
//remove portions of text before the zero and one occurence of $bytes
if(is_array($bytes) and isset($bytes[0]) and is_array($bytes[0]) and isset($bytes[0][0])) $start=$bytes[0][0]-$GLOBALS['dstr']; else $start=0;
if($start<=0) { $start=0; $start_s=''; } else $start_s='...';

if(is_array($bytes) and isset($bytes[1]) and is_array($bytes[1]) and isset($bytes[1][0])) $end=$bytes[1][0]+$GLOBALS['dstr']; else $end=$GLOBALS['dstr'];

if($end>strlen($text)) $end_s=''; else $end_s='...';
//$end=$end-$start;

$middleTxt=substr($text,$start,$end-$start);

/* Avoid special symbols extract */
if(substr_count($middleTxt, '&')>0){
$tmpArr=explode ('&', $middleTxt);
$last=sizeof($tmpArr)-1;
if ($last>0) {
if (substr_count($tmpArr[$last], ';')==0) array_pop($tmpArr);
$middleTxt=implode ('&', $tmpArr);
}
}

/* Avoid unclosed tags */
$mt=''; $cotag=FALSE; $tagOpen=0; //0 - <strong> 1 - </strong>
$fmt='';

for($i=0;$i<strlen($middleTxt);$i++){
if($middleTxt[$i]=='<') { $cotag=TRUE; $mt.=$middleTxt[$i]; continue; }
if($middleTxt[$i]=='>') { $cotag=FALSE; $fmt.=$mt.$middleTxt[$i]; if($mt=='<strong') $tagOpen=1; else $tagOpen=0; $mt=''; continue; }
if(!$cotag) $fmt.=$middleTxt[$i]; else $mt.=$middleTxt[$i];
}
if($tagOpen==1) $fmt.='</strong>';

return $start_s.$fmt.$end_s;
}

function highlightText($text,$phrase,$searchType){
$text=strip_tags(str_replace('<br />', ' ', $text));
$text1=strtolower_rus($text);
$phrase=str_replace('$', '&#036;', $phrase);

$GLOBALS['bytes']=array();

/* Array containing list of begin and end bytes, between them "bold" selection will be inserted. List if formed basing on search phrase(s) when analyzing text by getBytes() */

if($phrase!='' and $searchType!=2){
$words=explode(' ',$phrase);
foreach($words as $w) {
$w=trim(strtolower_rus($w));
if($w!='' and strlen($w)>2) getBytes($text1,$w);
//$text1=str_replace($w, '<strong>'.$w.'</strong>', $text1);
}
}//phrase
elseif($phrase!='' and $searchType==2) {
getBytes($text1,trim(strtolower_rus($phrase)));
}

$text=boldText($text,$GLOBALS['bytes']);
return cutText($text,$GLOBALS['bytes']);
}

/* SQLs: 0 - by datetime, 1 - by closed forums, 2 - specified forums, 3 - phrase, 4 - author */

$sql=array();
$navUrl='';

if($viewtbSearchType==1) $navUrl.="&amp;searchType={$searchType}";

if($where==0) { $date_field='post_time'; $poster_id_field='poster_id'; $poster_field='poster_name'; $table=$Tp; }
elseif($where==1) { $date_field='topic_time'; $poster_id_field='topic_poster'; $poster_field='topic_poster_name'; $table=$Tt; }

if($where!=1) $navUrl.="&amp;where={$where}";

$startDate=$sYear.'-'.($sMonth<10?'0'.$sMonth:$sMonth).'-'.($sDay<10?'0'.$sDay:$sDay).' 00:00:00';
$endDate=$eYear.'-'.($eMonth<10?'0'.$eMonth:$eMonth).'-'.($eDay<10?'0'.$eDay:$eDay).' 23:59:59';

$startDayS=($startDay<10?'0'.$startDay:$startDay);
$startMonthS=($startMonth<10?'0'.$startMonth:$startMonth);

if($startDate=="$startYear-$startMonthS-$startDayS 00:00:00" and $endDate==date('Y-m-d 23:59:59')) {}  
else {
$sql[0]=" ($date_field>='$startDate' and $date_field<='$endDate') ";
$viewtbSearchDate=1;
$navUrl.="&amp;sDay={$sDay}&amp;sMonth={$sMonth}&amp;sYear={$sYear}&amp;eDay={$eDay}&amp;eMonth={$eMonth}&amp;eYear={$eYear}";
}

if ($closedForums!='n') $sql[1]=getClForums($closedForums,'','','forum_id','and','!=');
else $sql[1]='';

if((int)$forum!=0) {
$sql[2]=" forum_id=$forum ";
$viewtbSearchForum=1;
$navUrl.="&amp;forum={$forum}";
}

$words=explode(' ',$phrase);
if(sizeof($words)>2) $searchType=2;
if($phrase!='') {
$sql[3]=db_genPhrase($phrase,$where,$searchType);
}

if($posterName!='') {
$posterName1=urlencode($posterName);
if($row=db_simpleSelect(0, $Tu, $dbUserId, $caseComp.'('.$dbUserSheme['username'][1].')', '=', strtolower_rus($posterName))) $sql[4]=" $poster_id_field={$row[0]} ";
else $sql[4]=" $poster_field='$posterName' ";
$navUrl.="&amp;posterName={$posterName1}";
}

foreach($sql as $k=>$sq) if(trim($sq)=='') unset($sql[$k]); else $sql[$k]=' ('.$sql[$k].') ';
$sqlStr=implode('and', $sql);

//echo "select count(*) from $table where $sqlStr";
//echo $sqlStr;

if($row=db_searchSelect(0,$table,'count(*)',$sqlStr)) $numRows=$row[0]; else $numRows=0;

if($numRows==0){
$warning='<span class="warning">'.$l_searchFailed.'</span>';
}

else{
$warning=$l_recordsFound.' '.$numRows;

/* Finally! We get all topics/messages list */

$phrase1=urlencode($phrase);
if($phrase1!='') $navUrl.="&amp;phrase={$phrase1}";

$pageNav=pageNav($page, $numRows, "{$main_url}/{$indexphp}action=search{$navUrl}&amp;searchGo=1", $viewmaxsearch, FALSE, 'Gen');
$makeLim=makeLim($page, $numRows, $viewmaxsearch);

$searchResults='';

IF($where==0){
/* If we search by messages, first select the messages, then their topic titles. 2 requests */

$searchTopics=array();
$searchData=array();
$i=0;

if($row=db_searchSelect(0,$Tp,'post_id, forum_id, topic_id, post_time, post_text', $sqlStr, $makeLim, 'post_id desc')) {
do {
if(!in_array($row[2],$searchTopics)) $searchTopics[]=$row[2];
$txt=highlightText($row[4],$phrase,$searchType);

$searchData[$i]=array('post_id'=>$row[0], 'forum_id'=>$row[1], 'forum_name'=>(isset($forums[$row[1]])?$forums[$row[1]]:'N/A'), 'topic_id'=>$row[2], 'datetime'=>$row[3], 'text'=>$txt);
$i++;
}
while ($row=db_searchSelect(1));
}

$xtrTopics=getClForums($searchTopics,'','','topic_id','OR','=');
unset($searchTopics);
$searchTopics=array();

if($row=db_searchSelect(0,$Tt,'topic_id, topic_title', '('.$xtrTopics.')')) {
do $searchTopics[$row[0]]=$row[1];
while ($row=db_searchSelect(1));
}

/* Output to screen */
for($i=0;$i<$viewmaxsearch;$i++){
if(isset($searchData[$i])) {
$num=$i+1+$page*$viewmaxsearch-(PAGE1_OFFSET+1)*$viewmaxsearch;
$datetime=convert_date($searchData[$i]['datetime']);
if(isset($searchData[$i]['topic_id']) and isset($searchTopics[$searchData[$i]['topic_id']])) $topic_name=$searchTopics[$searchData[$i]['topic_id']]; else $topic_name='N/A';

if(isset($preModerationType) and $preModerationType>0){
if(isset($premodTopics) and in_array($searchData[$i]['topic_id'], $premodTopics)) $topic_name=$l_topicQueued;
if(isset($premodPosts) and in_array($searchData[$i]['post_id'], $premodPosts)) $searchData[$i]['text']=$l_postQueued;
}

$searchResults.=<<<out
<table class="tbTransparent" style="width:100%"><tr><td class="tbTransparentCell">{$num}. <span class="txtSm"><strong>{$l_posted}</strong>: {$datetime} - <strong>{$searchData[$i]['forum_name']}</strong></span> / <strong><a href="{$main_url}/{$indexphp}action=search&amp;loc=1&amp;forum={$searchData[$i]['forum_id']}&amp;topic={$searchData[$i]['topic_id']}&amp;page={$searchData[$i]['post_id']}">{$topic_name}</a></strong><br />
&nbsp;&nbsp;&nbsp; <span class="txtNr">{$searchData[$i]['text']}&nbsp;</span>
</td></tr></table><br />
out;
}
}

}//where = messages

elseif($where==1){
/* Simply search by topic titles. 1 request */

if($row=db_searchSelect(0,$Tt,'topic_id, forum_id, topic_title, topic_time', $sqlStr, $makeLim, 'topic_id desc')) {

$num=1+$page*$viewmaxsearch-(PAGE1_OFFSET+1)*$viewmaxsearch;
do {
$txt=highlightText($row[2],$phrase,$searchType);
$datetime=convert_date($row[3]);
$forum_name=(isset($forums[$row[1]])?$forums[$row[1]]:'N/A');

if(isset($preModerationType) and $preModerationType>0 and isset($premodTopics) and in_array($row[0], $premodTopics)) $txt=$l_topicQueued;

if(isset($mod_rewrite) and $mod_rewrite) $furl=addTopicURLPage(genTopicURL($main_url, $row[1], $forum_name, $row[0], $row[2]), PAGE1_OFFSET+1);
else $furl="{$main_url}/{$indexphp}action=vthread&amp;forum={$row[1]}&amp;topic={$row[0]}";

$searchResults.=<<<out
<table class="tbTransparent" style="width:100%"><tr><td class="tbTransparentCell">{$num}. <span class="txtSm"><strong>{$l_posted}</strong>: {$datetime} - <strong>{$forum_name}</strong></span> / <a href="{$furl}">{$txt}</a>
</td></tr></table><br />
out;
$num++;
}
while ($row=db_searchSelect(1));
}

}//where topics

}//numRows>0

}

else{
$warning=$l_search[10];
}

echo load_header(); echo ParseTpl(makeUp('search'));
return;
?>