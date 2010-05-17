var selektion;
var globalPost=0;

function insertAtCursor(myField, myValue, bbCode1, bbCode2, endOfLine) {
var bbb;
if(bbCode1=='[url=null]') { bbCode1=''; bbCode2=''; }
if(bbCode1=='[imgs]' && myValue==null) { bbCode1=''; bbCode2=''; myValue=''; }
if(bbCode1=='[imgs=null]') { bbCode1=''; bbCode2=''; myValue=''; }
if(bbCode2=='null[/imgs]') { bbCode2='[/imgs]'; myValue=''; }

//MOZILLA/NETSCAPE/OPERA support
if (typeof(myField.selectionStart) == 'number') {
var startPos = myField.selectionStart;
var endPos = myField.selectionEnd;
var scrollTop = myField.scrollTop;
var bbb2, bbV, eoll;
if(myValue=='') myValue = myField.value.substring(startPos, endPos);
//alert(myValue);
myField.value = myField.value.substring(0, startPos) + bbCode1 + myValue + bbCode2 + endOfLine + myField.value.substring(endPos, myField.value.length);
if(myValue=='') {

if(bbCode1.substring(0,4)=='[img'  || bbCode1.substring(0,4)=='[url'){
bbb=bbCode1.length + myValue.length + bbCode2.length;
myField.selectionStart=startPos+bbb; myField.selectionEnd=startPos+bbb;
}
else{
bbb=bbCode1.length;
myField.selectionStart=startPos+bbb;
myField.selectionEnd=endPos+bbb;
}

}
else {
bbb=bbCode1.length;
bbb2=bbCode2.length;
bbV=myValue.length;
eoll=endOfLine.length;
myField.selectionStart=startPos+bbV+bbb+bbb2+eoll;
myField.selectionEnd=myField.selectionStart;
}
myField.focus();
myField.scrollTop = scrollTop;
return;
}

else if (document.selection) {
//IE support
var str = document.selection.createRange().text;
myField.focus();
sel = document.selection.createRange();
sel.text = bbCode1 + myValue + bbCode2 + endOfLine;
if(myValue=='') {
bbb=bbCode2.length; 
if(bbCode1.substring(0,4)=='[img' ) bbb=0; else bbb=-bbb;
sel.moveStart('character',bbb); sel.moveEnd('character',bbb);
}
sel.select();
return;
}

else {
myField.value += myValue;
return;
}
}

function paste_strinL(strinL, isQuote, bbCode1, bbCode2, endOfLine, User, Post){
if(isQuote==1 && strinL=='') {
alert(l_quoteMsgAlert);
return;
}
else if(isQuote==2 && strinL=='') {
globalPost=Post;
bbCode1='[b]' + User + '[/b]\n'; bbCode2=''; endOfLine='';
//alert(l_quoteMsgAlert);
}
else{
globalPost=Post;
if (isQuote==1) {
bbCode1='[quote=' + User + ']'; bbCode2='[/quote]'; endOfLine='\n';
}
if (isQuote==2) {
strinL=User;
bbCode1='[b]'; bbCode2='[/b]'; endOfLine='\n';
}
}
var isForm=document.getElementById('postMsg');
if (isForm) {
var input=document.getElementById('postText');
//var input=document.forms["postMsg"].elements["postText"];
insertAtCursor(input, strinL, bbCode1, bbCode2, endOfLine);
}
else alert(l_accessDenied);
//}
}

function pasteSel() {
selektion='';
if(window.getSelection) {
this.thisSel=window.getSelection()+'';
selektion=this.thisSel.toString();
}
else if(document.getSelection) selektion=document.getSelection()+'';
else if(document.selection) selektion=document.selection.createRange().text;
}


function trimTxt(s) {
while (s.substring(0,1) == ' ') {
s = s.substring(1,s.length);
}
while (s.substring(s.length-1,s.length) == ' ') {
s = s.substring(0,s.length-1);
}
return s;
}

function submitForm(){

var pf=document.forms['postMsg'];
var ftitle=false, ftext=false, flogin=false, fpass=false, user_usr='', user_pwd='', topicTitle='', postText='', fsubmit=true;
if(pf.elements['user_usr']) { flogin=true; user_usr=trimTxt(pf.elements['user_usr'].value); }
if(pf.elements['user_pwd']) { fpass=true; user_pwd=trimTxt(pf.elements['user_pwd'].value); }
if(pf.elements['postText']) { ftext=true; postText=trimTxt(pf.elements['postText'].value); }
if(pf.elements['topicTitle']) { ftitle=true; topicTitle=trimTxt(pf.elements['topicTitle'].value); }
if(pf.elements['CheckSendMail'] && pf.elements['CheckSendMail'].checked) { tlength=0; }

if(flogin && fpass && user_usr!='' && user_pwd!='') fsubmit=true;
else if(flogin && fpass && anonPost==0 && user_pwd=='') fsubmit=false;
else if(ftext && postText.length<tlength) fsubmit=false;
else if(ftitle && topicTitle.length<tlength) fsubmit=false;

if(fsubmit) { pf.elements['subbut'].disabled=true; document.forms['postMsg'].submit(); } else { alert(l_accessDenied); return; }
}
