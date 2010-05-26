<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2006 Paul Puzyrev, Sergei Larionov. www.minibb.net
Latest File Update: 2006-Dec-08
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

if(($viewTopicsIfOnlyOneForum!=1 or defined('ADMIN_PANEL')) and $row=db_simpleSelect(0,$Tf,'forum_id, forum_name, 
forum_group, forum_icon, forum_desc, topics_count','','','','forum_order')){

//$st: 1 - dont show included forum, 0 - show all (select included)
$forumsList='';
$forumsArray=array();
//$keyAr=0;

$i=0;
$listForums='';
$optGroups=array();
$currOg=0;

$tpl=makeUp('main_forums_list');
do {

$forumsArray[$row[0]]=array($row[1], $row[3], $row[4], $row[5]);

if ($row[2]!='') { $currOg++; $optGroups[$currOg][0]=$row[2]; }

if($user_id!=1 and isset($clForums) and in_array($row[0],$clForums) and isset($clForumsUsers[$row[0]]) and !in_array($user_id,$clForumsUsers[$row[0]])) $show=FALSE; else $show=TRUE;

if($show){

$sel='';

$forumItem='';

if (isset($st) and $st==1) {
if($row[0]!=$frm) $forumItem="<option value=\"{$row[0]}\">{$row[1]}</option>\n";
}
else {
if ($row[0]==$frm) $sel='selected="selected" ';
$forumItem="<option {$sel}value=\"{$row[0]}\">{$row[1]}</option>\n";
}

if($forumItem!='') $optGroups[$currOg][$row[0]]=$forumItem;

$i++;
}

}
while($row=db_simpleSelect(1));
unset($result);unset($countRes);

for($a=0;$a<=$currOg;$a++){
if(isset($optGroups[$a]) and sizeof($optGroups[$a])>=1){
$fins=''; foreach($optGroups[$a] as $k=>$v) if($k!=0) $fins.=$v;
if(isset($optGroups[$a][0]) and $fins!='') $listForums.="<optgroup label=\"{$optGroups[$a][0]}\">{$fins}</optgroup>";
else $listForums.=$fins;
}
}

if ($i>1) $forumsList=ParseTpl($tpl);
}
?>