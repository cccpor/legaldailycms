/**
 * HOW-TO : __LA.c(hdd, dis, pid);
 * TIPS   : after select, close the dialog box by click the 'X' on top right
 * op  : string : one from ['R', 'CAT', 'RO', 'RC', 'ROL', 'RCJ']
 * hdd : string : id of html element which hold the select value
 * dis : string : id of html element which hold the displaying of select value
 * pid : string : id of html element the select dialog box will appended to 
 * 
 */
var __LA={'k':'','id':'legal-system-select-wrapper','pid':'','hdd':'','dis':'','uuid':'','page':1,'url':'/ajax'};
__LA.get=function() { $.get(__LA.url+'?uuid='+__LA.uuid, function(data){__LA.d();$('#'+__LA.pid).css('position','relative');$('#'+__LA.pid).append($(data));}) }
__LA.c=function(hdd, dis, pid, uuid){__LA.hdd=hdd;__LA.dis=dis;__LA.pid=pid;__LA.uuid=uuid;__LA.get();} // create
__LA.d=function(){$('#'+__LA.pid).css('position', 'static');$('#'+__LA.id).remove();} // delete
__LA.r=function(uuid,text,page){$('#'+__LA.hdd).val(uuid);$('#'+__LA.dis).val(text);__LA.d();__LA.uuid=uuid;__LA.get();}