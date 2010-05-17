<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004, 2006-2008 Paul Puzyrev, Sergei Larionov. www.minibb.com
Latest File Update: 2008-Jan-07
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

if(isset($uname_minlength)) $umin=$uname_minlength; else $umin=3;
if(isset($uname_maxlength)) $umax=$uname_maxlength; else $umax=40;

$userRegExp="#^[".$userRegName."]{".$umin.",".$umax."}\$#";

if(!isset($correct)) $correct=0;

if($user_id==0){

foreach($disallowNames as $dn) {
if(substr_count(strtolower(${$dbUserSheme['username'][1]}),strtolower($dn))>0) { $correct=1; break; }
}

if(isset($disallowNamesIndex)){
foreach($disallowNamesIndex as $dn) {
if(strtolower(${$dbUserSheme['username'][1]})==strtolower($dn)) { $correct=1; break; }
}
}

}//id=0

if ($action=='register' and (!preg_match($userRegExp,${$dbUserSheme['username'][1]}) or preg_match("#^[0-9]+$#", ${$dbUserSheme['username'][1]})) ) $correct=1;
elseif($act=='reg' and !eregi("^[A-Za-z0-9_]{5,32}$", ${$dbUserSheme['user_password'][1]})) $correct=2;
elseif($act=='upd' and ${$dbUserSheme['user_password'][1]}!='' and !eregi("^[A-Za-z0-9_]{5,32}$", ${$dbUserSheme['user_password'][1]})) $correct=2;
elseif(${$dbUserSheme['user_password'][1]}!=$passwd2) $correct=3;
elseif(!eregi("^[0-9a-z]+([._-][0-9a-z_]+)*_?@[0-9a-z]+([._-][0-9a-z]+)*[.][0-9a-z]{2}[0-9A-Z]?[0-9A-Z]?$", ${$dbUserSheme['user_email'][1]})) $correct=4;
elseif(isset($dbUserSheme['user_website']) and isset(${$dbUserSheme['user_website'][1]}) and ${$dbUserSheme['user_website'][1]}!='' and !eregi("^(f|ht)tp[s]?:\/\/[^<>]+$", ${$dbUserSheme['user_website'][1]})) $correct=6;

?>