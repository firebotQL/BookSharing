<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004 Paul Puzyrev, Sergei Larionov. www.minibb.net
Latest File Update: 2006-Jul-04
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

if(isset($_POST) and count($_POST)>0){

foreach($dbUserSheme as $k=>$v){
if(isset($_POST[$v[2]])) $s=trim(htmlspecialchars(stripslashes($_POST[$v[2]]),ENT_QUOTES)); else $s='';
${$v[1]}=$s; ${$v[2]}=$s;
}

if(!isset($_POST['passwd2'])) $passwd2=''; else $passwd2=trim(htmlspecialchars(stripslashes($_POST['passwd2']),ENT_QUOTES));
if(isset($login)) $login=preg_replace("#[\s]{2,}#", ' ', $login);

}
else die('Unexpected error');
?>