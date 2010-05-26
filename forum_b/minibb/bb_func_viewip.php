<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004-2006 Paul Puzyrev, Sergei Larionov. www.minibb.net
Latest File Update: 2007-Jan-24
*/
if (!defined('INCLUDED776')) die ('Fatal error.');
$title.=$l_userIP;

$postip=(isset($_GET['postip'])?htmlspecialchars($_GET['postip'],ENT_QUOTES):'');

$avMods=array();
foreach($mods as $k=>$v) if(is_array($v)) foreach($v as $vv) if(!in_array($vv,$avMods)) $avMods[]=$vv;

if ($user_id==1 or in_array($user_id,$avMods)) {
$listUsers='';
$l_usersIPs=$l_usersIPs." ".$postip;

if ($row=db_simpleSelect(0,$Tp,'DISTINCT poster_name, poster_id','poster_ip','=',$postip,'poster_name')) {
$listUsers.="<ul>";
do {
$lnk1=($row[1]!=0?"<a href=\"{$main_url}/{$indexphp}action=userinfo&amp;user={$row[1]}\">":'');
$lnk2=($row[1]!=0?'</a>':'');
$listUsers.="<li class=\"limbb\">{$lnk1}{$row[0]}{$lnk2}</li>";
}
while($row=db_simpleSelect(1));
$listUsers.="</ul>";
}
else $listUsers="<span class=\"txtNr\">&nbsp;{$l_userNoIP}<br /><br /></span>";
}

$banLink=($user_id==1?"<span class=\"txtNr\"><a href=\"{$main_url}/{$bb_admin}action=banUsr1&amp;banip={$postip}\">{$l_ban}</a></span>":'');

echo load_header(); echo ParseTpl(makeUp('tools_userips')); return;
?>