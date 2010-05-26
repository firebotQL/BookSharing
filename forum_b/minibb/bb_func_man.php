<?php
/*
This file is part of miniBB. miniBB is free discussion forums/message board software, without any warranty. See COPYING file for more details. Copyright (C) 2004 Paul Puzyrev, Sergei Larionov. www.minibb.net
Latest File Update: 2008-Mar-18
*/
if (!defined('INCLUDED776')) die ('Fatal error.');

$tpl=makeUp('faq');

$tplTmp=explode('{$manual}', $tpl);

$title.=$l_menu[4];
if(!defined('DISABLE_MANUAL_STYLE')){
$l_meta.=<<<out

<style type="text/css">
<!--
P{
font-family: georgia, tahoma, verdana, arial, sans-serif;
color: #000000;
text-decoration: none;
font-size: 12pt;
line-height:15pt;
padding-bottom:5pt;
text-indent: 4pt;
}


SMALL{
font-family: 'lucida grande', tahoma, verdana, arial, sans-serif;
color:#696969;
text-decoration: none;
font-size: 8pt;
}

PRE{
font-family: Helvetica,sans-serif;
color: #000000;
text-decoration: none;
font-size: 9pt;
padding:5pt;
margin-left:10pt;
margin-bottom:5pt;
background-color: #EEEEEE;
}

H1, H2{
font-family: 'lucida grande', tahoma, verdana, arial, sans-serif;
font-weight:bold;
color: #003A66;
margin-top: 10pt;
margin-bottom: 4px;
}

H1{
font-size:13pt;
}

H2{
font-size:12pt;
}

LI{
font-family: Verdana,Helvetica,sans-serif;
color: #000000;
text-decoration: none;
font-size: 10pt;
margin-top: 0px;
margin-bottom: 0px;
margin-right: 0px;
margin-left: 15px;
list-style: circle;
padding-bottom:5pt;
}

UL, OL{
font-family: Verdana,Arial,Helvetica,sans-serif;
color: #000000;
text-decoration: none;
font-size: 9pt;
margin-top: 0px;
margin-bottom: 0px;
margin-right: 15px;
margin-left: 15px;
list-style: circle;

}
//-->
</style>
out;
}
echo load_header();
echo $tplTmp[0];
if(file_exists($pathToFiles.'templates/manual_'.$lang.'.html')) include($pathToFiles.'templates/manual_'.$lang.'.html');
elseif(file_exists($pathToFiles.'templates/manual_eng.html')) include($pathToFiles.'templates/manual_eng.html');
echo $tplTmp[1];

?>