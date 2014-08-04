/* 首页 添加标签操作   开始  */

function ask_a_tab(){
	$("#addmsgtab_k").show();
	$("#addmsg_subbtn_k").show();
	$(".ask_a_tab_btn").hide();
}
function addmsgtag_sub(obj){
	$("#addmsgtab_k").hide();
	$("#addmsg_subbtn_k").hide();
	$(".ask_a_tab_btn").show();
	$("#msg_send_tag_search").html('');
	var tagarr = new Array();
	$(".tagSmall").each(function() {
		tagarr.push($(this).html());
	});
	
	$.post("basehtml/command/command_base.php?mod=message&action=addtag", {
		tagarr : tagarr,
		msgid : obj
	}, function(data) {
		showGlobalAlert(data);
	}, "html");
}

function getByteLen(val) {
	var len = 0;
	for (var i = 0; i < val.length; i++) {
		if (val[i].match(/[^\x00-\xff]/ig) != null) //全角
			len += 2;
		else
			len += 1;
	}
	return len;
}
function getmsgTag(obj) {
	if ($("#msgtagSend").val() == '') {
		msghideTagSearch();
		return;
	}
	var text = $("#msgtagSend").val();
	var tablen = getByteLen(text);
	
	if(tablen>10){
		var tabsubstr =text.substring(0,10) ;
		$("#msgtagSend").val(tabsubstr);
	}else{
		$("#msg_send_tag_search").load(
				"index_detail.php?mod=index&action=tag&type=search", {
					tag : $("#msgtagSend").val(),
					tagarea : "msgdetail"
		});
	}
}

function entermsgTag(event,obj){ 
	if($("#msgtagSend").val()==''){
		msghideTagSearch();
		return;
	}else{
		var lKeyCode = (event.which)?event.which:window.event.keyCode;
		if (lKeyCode==13 ){
			msgaddtotag($(obj).val());
		} 
	} 
}

var msgnowTag = [];
var addmsgtagtotle = 1;
function msgaddtotag(tag) {
	$("#addmsgtab_k").hide();
	$("#msgtagSend").val('');
	if(addmsgtagtotle>1){
		showGlobalAlert('只能添加一个标签！');
		$("#addmsg_subbtn_k").hide();
		$("#ask_a_tab_btn").hide();
	}else{
		if (msgnowTag.indexOf(tag) < 0) {
			addmsgtagtotle++;
			msgnowTag.push(tag);
			var appendto = '<span class="tagSmall" onClick="msgdeltag(\'' + tag
					+ '\',this)">' + tag + '</span>';
			$("#msgdetailtablist").append(appendto);
		}
		msghideTagSearch();
	}
}
function msgdeltag(tag, obj) {
	$(obj).hide();
	var msgtagNew = [];
	for ( var i = 0; i < msgnowTag.length; i++) {
		if (msgnowTag[i] != tag) {
			msgtagNew.push(msgnowTag[i]);
		}
	}
	msgnowTag = msgtagNew;
}
function msghideTagSearch() {
	$("#msg_send_tag_search").empty();
	$("#msgtagSend").val("");
}
/* 添加标签操作   结束  */

//评论
function msgflowdiscussmsg(obj1,obj2) {
	var shouqiplcount =  $("#pl_content_dd_"+obj1).attr("data-count");
	var pl_cont_on = $("#pl_content_dd_"+obj1).attr("data-class");
	if(pl_cont_on == "on"){
		$("#pl_content_dd_"+obj1).hide();
		$("#pl_content_dd_"+obj1).html('');
		$("#pl_content_dd_"+obj1).attr("data-class","");
		$("#pinglunmsgcon_"+obj1).html("评论("+shouqiplcount+")");
		pldatapage=1;
	}else{
		$("#pinglunmsgcon_"+obj1).html("收起评论");
		$("#pl_content_dd_"+obj1).attr("data-class","on");
		$("#pl_content_dd_"+obj1).show();
		$("#pl_content_dd_"+obj1).load("index_detail.php?mod=review&action=show&type=list",{
			messageid:obj2,
			msgcontentid:obj1
		});
	}
}

function getUser(){
	if($("#user_search_content").val()==''){
		//hideTagSearch();
		return;
	}
	$("#search_user_show").load("index_detail.php?mod=index&action=user&type=search",{
		content : $("#user_search_content").val()
	});
	$("#search_message_show").load("index_detail.php?mod=index&action=message&type=search",{
		content : $("#user_search_content").val()
	});
	//window.location.href="index.php?mod=index&action=search";
}

//居中显示需要
function centershow(obj) {
    var screenWidth = $(window).width();
    var screenHeight = $(window).height();  //当前浏览器窗口的 宽高
    var scrolltop = $(document).scrollTop();//获取当前窗口距离页面顶部高度
    var objLeft = (screenWidth - obj.width())/2 ;
    var objTop = (screenHeight - obj.height())/2 + scrolltop;
    //$('.msgdetail_content').css("left" , "'"+objLeft + "px'");
    obj.css( "left" , objLeft + "px");
    obj.css("display" , "block");
    //obj.css("top" , "'"+objTop + "px'");
    //浏览器窗口大小改变时
    $(window).resize(function() {
        screenWidth = $(window).width();
        screenHeight = $(window).height();
        scrolltop = $(document).scrollTop();
        objLeft = (screenWidth - obj.width())/2 ;
        //objTop = (screenHeight - obj.height())/2 + scrolltop;
        obj.css("left" , objLeft + "px");
    });
    //浏览器有滚动条时的操作、
    $(window).scroll(function() {
        screenWidth = $(window).width();
        screenHeight = $(widow).height();
        scrolltop = $(document).scrollTop();
        objLeft = (screenWidth - obj.width())/2 ;
        //objTop = (screenHeight - obj.height())/2 + scrolltop;
        obj.css("left" , objLeft + "px");
    });
   
}
//首页显示详细信息
function showDetailMsg(obj){
	$('#pro_bg').show();
	centershow($('.msgdetail_content'));
	$("#msgdetail_content").load("index_detail.php?mod=index&action=message&type=showdetail",{msgid:obj});
	msgnowTag = [];
	addmsgtagtotle = 1;
	$.post("basehtml/command/command_base.php?mod=message&action=read", {
		message_id : obj
	}, function(data) {
		showGlobalAlert(data);
	}, "html");
}

//取消关注
function tagattention(gameuid,obj){
	if($(obj).val()=="1")
	{
		$.post("basehtml/command/command_base.php?mod=attention&action=del", {
			attention_gameuid : gameuid
		}, function(data) {
			// $("#error_content").html(data);
			showGlobalAlert(data);
			$(obj).html("关注");
			$(obj).attr('value','0');
		}, "html");
	}else{
		$.post("basehtml/command/command_base.php?mod=attention&action=add", {
			attention_gameuid : gameuid
		}, function(data) {
			showGlobalAlert(data);
			$(obj).html("取消关注");
			$(obj).attr('value','1');
		}, "html");
	}
}

// 删除自己发布的信息
function myselfmsgdel(messageid,userid){
	var statu = confirm("您确定要删除此条信息吗?");
    if(!statu){
        return false;
    }else{
		$.post("basehtml/command/command_base.php?mod=message&action=myselfmsgdel", {
			recommendid : messageid,
			userid:userid
		}, function(data) {
			showGlobalAlert(data);
			$("#myselfmsgdelbtn_"+messageid).css("color","#999");
			$("#myselfmsgdelbtn_"+messageid).removeAttr("onclick");
			$("#myselfmsgdelbtn_"+messageid).html("已删除");
		}, "html");
    }
}

// 推荐功能
function msgrecommend(messageid){
	$.post("basehtml/command/command_base.php?mod=message&action=recommend", {
		recommendid : messageid,
		review:""
	}, function(data) {
		showGlobalAlert(data);
	}, "html");
}
// 推荐功能回调
function getFromServerPush(content) {
	// console.log(content);
	if (content.type == 'new') {
		addNew(changeContentFromPush(content));
	}else if (content.type == 'limit') {
		showGlobalAlert("毁灭星来啦");
		// addNew(content);
	}
	else if (content.type == 'power') {
		showGlobalAlert("能量星来啦");
		// addNew(content);
	}
	else if (content.type == 'chat') {
		showGlobalAlert("有新消息");
		// addNew(content);
		newchatcome(content.gameuid,content.message_content);
	}
}

// 主页

function removeHomeIndex(){
	$("#mycollectionpage").removeClass("active");
	$("#mypersonaldata").removeClass("active");
	$("#myhomepage").removeClass("active");
	$("#myalbumpage").removeClass("active");
}

function myhomepageload(){
	removeHomeIndex();
	$("#homepagetype").val('1');
	$("#myhomepage").addClass("active");
	$("#home_content").load("index_detail.php?mod=home&action=user&type=myhomepage",{
		idSec:$("#idsec").val()
	});
}
// 个人主页-我的发现
function mycollectionpageload(){
	removeHomeIndex();
	$("#homepagetype").val('2');
	$("#home_content").html('');
	$("#mycollectionpage").addClass("active");
	$("#home_content").load("index_detail.php?mod=home&action=user&type=mycollectionpage",{
		idSec:$("#idsec").val()
	});
}
// 个人主页-个人资料
function mypersonaldataload(){
	removeHomeIndex();
	$("#homepagetype").val('3');
	$("#home_content").html('');
	$("#mypersonaldata").addClass("active");
	$("#home_content").load("index_detail.php?mod=home&action=user&type=mypersonaldata",{
		idSec:$("#idsec").val()
	});
}

function myalbumload(){
	removeHomeIndex();
	$("#myalbumpage").addClass("active");
	$("#home_content").load("index_detail.php?mod=home&action=user&type=myalbum",{
		idSec:$("#idsec").val()
	});
}

// 列表页查看详细信息
function seemoremsginfo(obj){
	$.post("basehtml/command/command_base.php?mod=message&action=seemoremsginfo", {
		messageid : obj
	}, function(data) {
		$("#seemoremsgcontent_"+obj).html(data);
		$("#seemoremsgcontent_"+obj).show();
		$("#msgcontentlittle_"+obj).hide();
		
		$("#msgimagescontent_"+obj).hide();
		$("#videomsg_description_"+obj).hide();
		
		$("#shouqimsgcontent_"+obj).show();
		$("#shouqicline_"+obj).show();
		$("#xianshimsgcontent_"+obj).hide();
		$("#xianshicline_"+obj).hide();
	}, "html");
}
// 收起列表页查看详细信息
function shouqimoremsginfo(obj){
		$("#seemoremsgcontent_"+obj).hide();
		$("#msgcontentlittle_"+obj).show();
		$("#shouqimsgcontent_"+obj).hide();
		$("#shouqicline_"+obj).hide();
		$("#msgimagescontent_"+obj).show();
		$("#videomsg_description_"+obj).show();
		$("#xianshimsgcontent_"+obj).show();
		$("#xianshicline_"+obj).show();
}
// 收藏
function collectmessage(obj){
	$.post("basehtml/command/command_base.php?mod=message&action=collection", {
		messageid : obj
	}, function(data) {
		showGlobalAlert(data);
		$("#collmsgbtn_"+obj).css("color","#999");
		$("#collmsgbtn_"+obj).removeAttr("onclick");
		$("#collmsgbtn_"+obj).html("取消发现");
		collectNumOp(1);
	}, "html");
}
// 取消收藏
function uncollectmessage(obj){
	var statu = confirm("您确定要取消发现此信息吗?");
    if(!statu){
        return false;
    }else{
		$.post("basehtml/command/command_base.php?mod=message&action=uncollection", {
			messageid : obj
		}, function(data) {
			showGlobalAlert(data);
			$("#uncollmsgbtn_"+obj).css("color","#999");
			$("#uncollmsgbtn_"+obj).removeAttr("onclick");
			$("#uncollmsgbtn_"+obj).html("发现");
			collectNumOp(0);
		}, "html");
	}
}
function submit_nikename(str) {	
	    $.post("basehtml/command/command_base.php?mod=member&action=changeuserinfo", {
			nickname:$("#nikename_in").val(),
		}, function(data) {
			if(data==0){
				$("#nikename_w").html(str);
			}else{
			    $("#nikename_w").html(data);
				$("#nickname_header").text(data);
			} 
		}, "html");
	}
function submit_des(str) {
    	$.post("basehtml/command/command_base.php?mod=member&action=changeuserinfo", {
			description:$("#user_des_in").find(".uitxt").val(),
		}, function(data) {
			if(data==0){
				$("#user_des_in").parent().html('<span id="user_des_w" onclick="desInput(this)">个人签名：'+str+'</span>');
			}else{
			    $("#user_des_in").parent().html('<span id="user_des_w" onclick="desInput(this)">个人签名：'+data+'</span>');
			} 
		}, "html");
	}

//登录

	function login() {
		$('#pro_bg').show();
		$('#content').load("index.php?mod=member&action=login", {});
	}
	function login_sub_kj() {
		$.post("basehtml/command/command_base.php?mod=member&action=checklogin", {
			userName : $("#kj_loginname").val(),passWord : $("#kj_loginpw").val()
		}, function(data) {
			if(data==1){
				window.location.href='index.php';
			}else{
				// $('#pro_bg').show();
		       // $('#content').load("index.php?mod=member&action=login", {});
			   window.location.href='index.php?mod=member&action=loginshow';
			} 
		}, "html");
	}
	// 注册
	function reg_kj() {
		$('#loginbox').show();
		$('#loginbox').load("index.php?mod=member&action=regkj", {});
	}
	function reg() {
		$('#pro_bg').show();
		$('#content').load("index.php?mod=member&action=reg", {});
	}
	// 退出登陆
	function loginout() {
		$.post("basehtml/command/command_base.php?mod=member&action=loginout",
				{}, function(data) {
					window.location.href='index.php';
				}, "html");
	}
	// 关闭登录框
	function close_login_k(){
		document.getElementById("login_k").style.display="none";
		document.getElementById("pro_bg").style.display="none";
	}
	// 打开登录框
	function open_login_k(){
		document.getElementById("login_k").style.display="block";
		document.getElementById("pro_bg").style.display="block";
	}
	// 关闭注册框
	function close_reg_k(){
		document.getElementById("reg_k").style.display="none";
		document.getElementById("pro_bg").style.display="none";
	}
	// 打开注册框
	function open_reg_k(){
		document.getElementById("reg_k").style.display="block";
		document.getElementById("pro_bg").style.display="block";
	}
	// 协议框
	function xqtpsyxy_k(){ 
		var xqtpsyxy =  document.getElementById("xqtpsyxy").style.display;
		if(xqtpsyxy=="none"){
			document.getElementById("xqtpsyxy").style.display="block";
		}else{
			document.getElementById("xqtpsyxy").style.display="none";
		}
	}
	// 点击立即注册显示注册注册框
	function just_reg(){
		document.getElementById("login_k").style.display="none";
		document.getElementById("reg_k").style.display="block";
	}
	// 点击立即登录显示注册注册框
	function just_login(){
		document.getElementById("login_k").style.display="block";
		document.getElementById("reg_k").style.display="none";
	}
	//
	function login_sub() {
		$.post("basehtml/command/command_base.php?mod=member&action=checklogin", {
			userName : $("#loginusername").val(),passWord : $("#loginpassword").val()
		}, function(data) {
			if(data==1){
				window.location.href='index.php';
			}else if(data==0){
				$("#lrfk_top").html("<font style='color:red;'>用户名或密码错误！</font>");
			}else{
				$("#lrfk_top").html("<font style='color:red;'>错误连接！</font>");
			} 
		}, "html");
	}
	 
	/* 注册需要的 */
	// 检查用户名
	function regcheckusername(){ 
		var regusername=document.getElementById("regusername").value;  
		if(regusername=='' || regusername ==null || regusername=='null'){
			document.getElementById("regusername_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>不能为空！</div>";
		}else if(regusername.length>16){
			document.getElementById("regusername_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>长度在2~16个字符！</div>";
		}else if(regusername.length<2){
			document.getElementById("regusername_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>长度在2~16个字符！</div>";
		}else{
			$.post("basehtml/command/command_base.php?mod=member&action=checkusername",{
				userName : regusername
			}, function(data) {
					if(data==1){
						document.getElementById("regusername_error").innerHTML ="<div class='check_res_k alert alert-success' role='alert'>输入正确！</div>";
					}else{
						document.getElementById("regusername_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>用户名存在！</div>";
					}
				}, "html");
		}
	}
	// 检查密码
	function regcheckpassword(){
		var regpassword=document.getElementById("regpassword").value;  
		if(regpassword=='' || regpassword ==null || regpassword=='null'){
			document.getElementById("regpassword_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>密码不能为空！</div>";
		}else if(regpassword.length>20){
			document.getElementById("regpassword_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>长度在2~20个字符！</div>";
		}else if(regpassword.length<2){
			document.getElementById("regpassword_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>长度在2~20个字符！</div>";
		}else{
			document.getElementById("regpassword_error").innerHTML ="<div class='check_res_k alert alert-success' role='alert'>输入正确！</div>";
		}
	}
	
	// 检查昵称
	function regchecknickname(){ 
		var regnickname=document.getElementById("regnickname").value;  
		if(regnickname=='' || regnickname ==null || regnickname=='null'){
			document.getElementById("regnickname_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>昵称不能为空！</div>";
		}else if(regnickname.length>16){
			document.getElementById("regnickname_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>长度在2~16个字符！</div>";
		}else if(regnickname.length<2){
			document.getElementById("regnickname_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>长度在2~16个字符！</div>";
		}else{
			
			$.post("basehtml/command/command_base.php?mod=member&action=checknickname",{
				nickName : regnickname
			}, function(data) {
					if(data==1){
						document.getElementById("regnickname_error").innerHTML ="<div class='check_res_k alert alert-success' role='alert'>输入正确！</div>";
					}else{
						document.getElementById("regnickname_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>昵称存在！</div>";
					}
				}, "html");
		}
	}
	// 检查email
	function regcheckemail(){
		var regemail=document.getElementById("regemail").value;   
		var regm = /^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
		 if (!regemail.match(regm))
		 {
		  document.getElementById("regemail_error").innerHTML="<div class='check_res_k alert alert-danger' role='alert'>格式错误或含有非法字符!</div>";
		 } else{
			 $.post("basehtml/command/command_base.php?mod=member&action=checkemail",{
				 regemail : regemail
				}, function(data) {
					if(data==1){
						document.getElementById("regemail_error").innerHTML ="<div class='check_res_k alert alert-success' role='alert'>输入正确！</div>";
					}else{
						document.getElementById("regemail_error").innerHTML ="<div class='check_res_k alert alert-danger' role='alert'>邮箱已存在！</div>";
					}
				}, "html");
		 }
		
	}
	function regcheckemail_kj(){
		var regemail=document.getElementById("regemail").value;   
		var regm = /^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
		 if (!regemail.match(regm))
		 {
		  $("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>格式错误或含有非法字符!</div>");
		 } else{
			 $.post("basehtml/command/command_base.php?mod=member&action=checkemail",{
				 regemail : regemail
				}, function(data) {
					if(data==1){
						// $("#msgbox").html("<div class='msgerror'><span
						// class='glyphicon
						// glyphicon-exclamation-sign'></span>输入正确！</div>");
					}else{
						$("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>邮箱已存在！</div>");
					}
				}, "html");
		 }
		
	}
	// 检查星座
	function regcheckxz_kj(){
		var regpassword=document.getElementById("constellation").value;  
		if(regpassword=='' || regpassword ==null || regpassword=='null'){
			$("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>星座不能为空！</div>");
		}else{
			// $("#msgbox").html("<div class='msgerror'><span class='glyphicon
			// glyphicon-exclamation-sign'></span>输入正确！</div>");
		}
	}
	// 检查密码
	function regcheckpassword_kj(){
		var regpassword=document.getElementById("regpassword").value;  
		if(regpassword=='' || regpassword ==null || regpassword=='null'){
			$("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>密码不能为空！</div>");
		}else if(regpassword.length>20){
			$("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>长度在2~20个字符！</div>");
		}else if(regpassword.length<2){
			$("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>长度在2~20个字符！</div>");
		}else{
			// $("#msgbox").html("<div class='msgerror'><span class='glyphicon
			// glyphicon-exclamation-sign'></span>输入正确！</div>");
		}
	}
	// 检查验证码
	function regyzm(){ 
				$.post("basehtml/command/command_base.php?mod=member&action=checkyzmcode", {
					yzm : $("#regyzm").val()
				}, function(data) {
					if(data=='1'){
						document.getElementById("regyzm_error").innerHTML="<div class='check_res_k alert alert-success' role='alert'>输入正确！</div>";
					}else{
						document.getElementById("regyzm_error").innerHTML="<div class='check_res_k alert alert-danger' role='alert'>输入验证码错误！</div>";
					}
				}, "html");
	}
	
	function click_reg_agreement(){
		$("#reg_btn").removeAttr("disabled");
		$("#reg_btn").removeClass("donotclick");
		$("#reg_btn").addClass("btn-warning");
	}
	function click_reg_all(){
		var r1=false;
		var r2=false;
		var r3=false;
		var regemail=document.getElementById("regemail").value;   
		var regm = /^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
		 if (!regemail.match(regm))
		 {
		  $("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>格式错误或含有非法字符!</div>");
		 } else{
			 $.post("basehtml/command/command_base.php?mod=member&action=checkemail",{
				 regemail : regemail
				}, function(data) {
					if(data!=1){
						$("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>邮箱已存在！</div>");
						return false;
					}
				}, "html");
		 }
		 
		var regpassword=document.getElementById("constellation").value;  
		if(regpassword=='' || regpassword ==null || regpassword=='null'){
			$("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>星座不能为空！</div>");
			return false;
		}
		
		var regpassword=document.getElementById("regpassword").value;  
		if(regpassword=='' || regpassword ==null || regpassword=='null'){
			$("#msgbox").append("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>密码不能为空！</div>");
			return false;
		}else if(regpassword.length>20){
			$("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>长度在2~20个字符！</div>");
			return false;
		}else if(regpassword.length<2){
			$("#msgbox").html("<div  class='msgerror'><span class='glyphicon glyphicon-exclamation-sign'></span>长度在2~20个字符！</div>");
			return false;
		}
		$("#msgbox").html();
		return true;
	}
	function click_reg_btn_kj(){
			//$("#reg_btn").attr("disabled","disabled");
			//$("#reg_btn").html("提交中...");
			if(!click_reg_all()){
				return;
			}
			//$("#reg_btn").attr("disabled","disabled");
			//$("#reg_btn").html("提交中...");
			$.post("basehtml/command/command_base.php?mod=member&action=reginsert2", {
				passWord : $("#regpassword").val(),
				email : $("#regemail").val(), 
				constellation:$("#constellation").val()
			}, function(data) {
				alert(data);
				window.location.href='index.php?mod=user&action=guid';
			}, "html");
				
	}
	
	// 发送邮件
	function regsendemail(obj2){
		$.post("basehtml/command/command_base.php?mod=mail&action=send", {
			username : obj2,
			email : obj2
		}, function(data) {
			if(data=='发送邮件成功'){
				window.location.href='index.php';
			}else{
				alert("邮件发送失败！请检查邮箱是否正确！");
			} 
		}, "html");
	}
	// 重新发送邮件
	function reregsendemail(){
		$.post("basehtml/command/command_base.php?mod=mail&action=send", {
			username : $("#reg_username_val").val(),email : $("#reg_email_val").val()
		}, function(data) {
			if(data=='发送邮件成功'){
				alert("邮件已经发送！");
			}else{
				alert("邮件发送失败！请检查邮箱是否正确！");
			}
		}, "html");
	}
	
	
	// 显示发送邮件界面
	function regsendemailpage(obj) {
		$('#pro_bg').show();
		$('#content').load("index.php?mod=member&action=regsendemail", {email:obj});
	}
	
	
/* 视频播放 */

	function switchVideo (id,type,host,flashvar){
		if( type == 'close' ){
			$('#msg_video_k_'+id).hide();
			$("#video_mini_show_"+id).show();
			$("#video_content_"+id).html( '' );
			$("#video_show_"+id).hide();
		}else{
			$('#msg_video_k_'+id).show();
			$("#video_mini_show_"+id).hide();
			$("#video_content_"+id).html( showFlash(host,flashvar) );
			$("#video_show_"+id).show();
		}
	}

	// 显示视频
	function showFlash( host, flashvar) {
		if(host=='youtube.com'){
			var flashHtml = '<iframe width="560" height="315"  src="http://www.youtube.com/embed/'+flashvar+'" frameborder="0" allowfullscreen></iframe>';
		}else{
		var flashHtml = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="430" height="400">'
	        + '<param value="transparent" name="wmode"/>'
			+ '<param value="'+flashvar+'" name="movie" />'
			+ '<embed src="'+flashvar+'" wmode="transparent" allowfullscreen="true" type="application/x-shockwave-flash" width="525" height="420"></embed>'
			+ '</object>';
		}
		return flashHtml;
	}
	
// 发布信息
	// 获取表情包
	function get_face(){
		$("#videos_k").hide();
		$("#music_k").hide();
		$.post("basehtml/command/command_base.php?mod=face&action=get", {}, function(data) {
			var _imgHtml = '';
			if(_imgHtml == '') {
				for(var k in data) {
					_imgHtml += '<a href="javascript:void(0)" title="'+data[k].title+'" onclick="face_chose(this)";><img src="./expression/'+data[k].type+'/'+ data[k].filename +'" width="24" height="24" /></a>';
				}
			}
			_imgHtml += '<div class="c"></div>';
			$("#emotions").show();
			$('#emot_content').html(_imgHtml);
		}, 'json');
	}
	// 选择表情
	function face_chose(obj){
		var imgtitle = $(obj).attr('title'); 
		var val = $("#message").val();
		val += '['+imgtitle+']' ;
		$("#message").val(val);
		$("#emotions").hide();
		$('#emot_content').empty();
		$("#send_message").attr('event-args','postface');
	}
    function get_face1(){
		$("#videos_k").hide();
		$("#music_k").hide();
		$.post("basehtml/command/command_base.php?mod=face&action=get", {}, function(data) {
			var _imgHtml = '';
			if(_imgHtml == '') {
				for(var k in data) {
					_imgHtml += '<a href="javascript:void(0)" title="'+data[k].title+'" onclick="face_chose1(this)";><img src="./expression/'+data[k].type+'/'+ data[k].filename +'" width="24" height="24" /></a>';
				}
			}
			_imgHtml += '<div class="c"></div>';
			$("#emotions").show();
			$('#emot_content').html(_imgHtml);
		}, 'json');
	}
	// 选择表情
	function face_chose1(obj){
		var imgtitle = $(obj).attr('title'); 
		var val = $("#chat_send_detail").val();
		val += '['+imgtitle+']' ;
		$("#chat_send_detail").val(val);
		$("#emotions").hide();
		$('#emot_content').empty();
		$("#send_chat_detail").attr('event-args','postface');
	}
	/*
	 * function minChatDetail(){
	 * $("#starry_chat_detail").css({"height":"30PX","width":"200px"});
	 * $("#minChatDetail").removeClass("nodpy");
	 * $("#Chatdetail").addClass("nodpy"); }
	 */
	function showChatDetail(){
		$("#starry_chat_detail").show();
	}
	function hideChatDetail(){
		$("#starry_chat_detail").hide();
	}
	
	function close_face(){
		$("#emotions").hide();
		$('#emot_content').empty();
	}
	

	

	
	function open_music_k(){
		$("#music_k").show();
		$("#emotions").hide();
		$("#videos_k").hide();
		$("#musicsurl").val('');
	}
	function close_music_k(){
		$("#music_k").hide();
		$("#musicsurl").val('');
	}
	
	function music_add(){
		$('#video_callback').val('');
		$('#music_callback').val('');
		var musicsurl = $('#musicsurl').val();
		var val = $("#message").val();
		val += musicsurl;
		$('#music_callback').val(musicsurl);
		$("#message").val(val);
		$("#music_k").hide();
		$("#musicsurl").val('');
		$("#send_message").attr('event-args','postmusic');
	}
	
	function shanshuo() {
		var num = 30;
		for ( var i = 0; i < num; i++) {
			var posL = Math.random() * $(window).width() + 'px';
			var posT = Math.random() * ($(window).height() - 200) + 'px';
			$('#body_content').append(
					"<div class='star_shanshuo' style='"+"left:"+posL+";top:"+posT+";z-index: 999'></div>");
			dark();
		}
		function dark() {
			var star = $('#body_content').find('.star_shanshuo');
			star.animate({
				'opacity' : '0'
			}, 500, function() {
				light(star)
			});

		}
		function light(obj) {
			obj.animate({
				'opacity' : '0.8'
			}, 100, function() {
				dark()
			});
		}
	}
	
	function changeMessage(type){
		changeMessageToType(type);	
	}
	
	function selectKindOfMessage(type){
		changeStarray(type,1);
		changeType(type);
	}
	// 引入发布信息框
	function willSend(obj) {
		// $("#msgdetail_content").load("index_detail.php?mod=index&action=send&type=full");
		$('#myModal').load("index_detail.php?mod=index&action=message&type=sendarea", {});
		$('#myModal').modal();
		// window.location.href="index.php?mod=member&action=index&showtype=send";
		// $("#create_img_bg_btn").rotate(45);
	}
	// 关闭发布信息框
	function close_msg_sendarea(){
		$('#myModal').empty();
	}
	// 发布信息
	function send_message() {
		var msg = $("#message").val();
		if( msg =="" || msg ==null || msg=="null" ){
			alert("发布内容不能为空！");
			return false;
		}
		var type = $("#send_message").attr('event-args');
		
		var imgpatharr = new Array();
		$("#img_callback img").each(function() {
			imgpatharr.push($(this).attr("src"));
		}); 
		var videopatharr = '';
		videopatharr = $("#video_callback").val();
		var videoimgpatharr = '';
		videoimgpatharr = $("#videoimg_callback").val();
		var musicpatharr = '';
		musicpatharr = $("#music_callback").val();
		$.post("basehtml/command/command_base.php?mod=message&action=send", {
			message : $("#message").val(),type:type,imgspath:imgpatharr,videospath:videopatharr,videoimgpath:videoimgpatharr,musicspath:musicpatharr
		}, function(data) {
			$("#send_message").attr('event-args','post');
			$("#message_error").html(data);
		}, "html");
		$('#message').val('');
		$('#videosurl').val('');
		$('#musicsurl').val('');
		$('#img_callback').val('');
		$('#video_callback').val('');
		$('#music_callback').val('');
	}
	
	
	function moveStyle(obj, type) {
	}
	
	// 上传图片
	function upImage() {
		var add = "<img src='http://starry.b0.upaiyun.com/%2F2014%2F06%2F1973f8472d7c8cbe.jpg' style='height:100px'>";
		$("#message_plain").append(add);
	}
	
	// 注册和登录点击关闭按钮用到
	function closethis(obj) {
		$('#pro_bg').hide();
		$('#' + obj).remove();
		socketon();
	}
	// 修改昵称和描述
	function changeuserinfo() {
		$.post("basehtml/command/command_base.php?mod=member&action=changeuserinfo",{
				nickname : $("#edit_nickname").val(),
				description : $("#reg_description").val()
			}, function(data) {
				alert(data);
			}, "html");
	}
	
/*  图片的旋转        开始  */
function revolving (type, id) {
	var img = $("#image_index_"+id);
	img.rotate(type);
}
$.fn.rotate = function(p){
  var img = $(this)[0],
    n = img.getAttribute('step');
  // 保存图片大小数据
  if (!this.data('width') && !$(this).data('height')) {
    this.data('width', img.width);
    this.data('height', img.height);
  };
  this.data('maxWidth',img.getAttribute('maxWidth'))

  if(n == null) n = 0;
  if(p == 'left'){
    (n == 0)? n = 3 : n--;
  }else if(p == 'right'){
    (n == 3) ? n = 0 : n++;
  };
  img.setAttribute('step', n);

  // IE浏览器使用滤镜旋转
  if(document.all) {
    if(this.data('height')>this.data('maxWidth') && (n==1 || n==3) ){
      if(!this.data('zoomheight')){
        this.data('zoomwidth',this.data('maxWidth'));
        this.data('zoomheight',(this.data('maxWidth')/this.data('height'))*this.data('width'));
      }
      img.height = this.data('zoomwidth');
      img.width  = this.data('zoomheight');
      
    }else{
      img.height = this.data('height');
      img.width  = this.data('width');
    }
    
    img.style.filter = 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ n +')';
    // IE8高度设置
    if ($.browser.version == 8) {
      switch(n){
        case 0:
          this.parent().height('');
          //this.height(this.data('height'));
          break;
        case 1:
          this.parent().height(this.data('width') + 10);
          //this.height(this.data('width'));
          break;
        case 2:
          this.parent().height('');
          //this.height(this.data('height'));
          break;
        case 3:
          this.parent().height(this.data('width') + 10);
          //this.height(this.data('width'));
          break;
      };
    };
  // 对现代浏览器写入HTML5的元素进行旋转： canvas
  }else{
    var c = this.next('canvas')[0];
    if(this.next('canvas').length == 0){
      this.css({'visibility': 'hidden', 'position': 'absolute'});
      c = document.createElement('canvas');
      c.setAttribute('class', 'maxImg canvas');
      img.parentNode.appendChild(c);
    }
    var canvasContext = c.getContext('2d');
    switch(n) {
      default :
      case 0 :
        img.setAttribute('height',this.data('height'));
        img.setAttribute('width',this.data('width'));
        c.setAttribute('width', img.width);
        c.setAttribute('height', img.height);
        canvasContext.rotate(0 * Math.PI / 180);
        canvasContext.drawImage(img, 0, 0);
        break;
      case 1 :
        if(img.height>this.data('maxWidth') ){
          h = this.data('maxWidth');
          w = (this.data('maxWidth')/img.height)*img.width;
        }else{
          h = this.data('height');
          w = this.data('width');
        }
        c.setAttribute('width', h);
        c.setAttribute('height', w);
        canvasContext.rotate(90 * Math.PI / 180);
        canvasContext.drawImage(img, 0, -h, w ,h );
        break;
      case 2 :
        img.setAttribute('height',this.data('height'));
        img.setAttribute('width',this.data('width'));
        c.setAttribute('width', img.width);
        c.setAttribute('height', img.height);
        canvasContext.rotate(180 * Math.PI / 180);
        canvasContext.drawImage(img, -img.width, -img.height);
        break;
      case 3 :
        if(img.height>this.data('maxWidth') ){
          h = this.data('maxWidth');
          w = (this.data('maxWidth')/img.height)*img.width;
        }else{
          h = this.data('height');
          w = this.data('width');
        }
        c.setAttribute('width', h);
        c.setAttribute('height', w);
        canvasContext.rotate(270 * Math.PI / 180);
        canvasContext.drawImage(img, -w, 0,w,h);
        break;
    };
  };
};
/*  图片的旋转      结束  */
