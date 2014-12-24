<? if(!defined('IN_MOOPHP')) exit('Access Denied');?>
<script>
var lasttype=1;
function showContent(type,page){
	if(lasttype!=type){
		page=0;
	}
	lasttype=type;
	$("#pageContent").html("正在加载");
	if(type==1){
		$("#pageContent").load("index_detail.php?showpage=adminpunish&page="+page);	
	}else if (type==2){
		$("#pageContent").load("index_detail.php?showpage=adminpunishedit&page="+page);
	}
}

var page=0;
function pageBefore(){
	if(page>0){
		page--;
	}
	showContent(lasttype,page);	
}

function pageNext(){
	page++;
	showContent(lasttype,page);
}

$(document).ready(function () {
	$("#pageContent").load("index_detail.php?showpage=adminpunish");
});
</script>
<div >

<div class="btn-group pull-right">
  <button type="button" class="btn btn-default" onclick="showContent(1,0)">惩罚审核</button>
  <button type="button" class="btn btn-default"  onclick="showContent(2,0)">惩罚管理</button>
</div>
<div id="pageContent" style="padding:10px 0px"></div>
		<ul class="pager">
  <li><a href="javascript:void(0);" onclick="pageBefore()" id="btnBefore">上一页</a></li>
  <li><a href="javascript:void(0);" onclick="pageNext()">下一页</a></li>
</ul>
</div>