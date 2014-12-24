<? if(!defined('IN_MOOPHP')) exit('Access Denied');?>
<script>
function agree(id){
	$.post("command/command_base.php?mod=punish&action=agree", {
		id : id
	}, function(data) {
		if(data!=1){
			alert(data);
		}
		else{
			showGlobalAlert("审核通过");
			$("#content_"+id).hide();
		}
	}, "html");
}
function del(id){
	$.post("command/command_base.php?mod=punish&action=del", {
		id : id
	}, function(data) {
		if(data==1){
			$("#content_"+id).hide();
			$(".bg-success").fadeIn();
		}
		else{
			alert("操作失败"+data);
		}
	}, "html");
}

</script>
<div>
<h3>惩罚审核</h3>
<p class="bg-success" style='padding:10px;display:none'>成功</p>
	<table class="table table-striped">
		<tbody>
			<?php foreach((array)$ret as $key=>$value) {?>
			<tr id="content_<?php echo $value['_id'];?>">
				<td>
				<div style="padding:10px 0px;">
				<?php if($value['type']=='1') { ?>
				<span class="label label-success" style='margin-right:5px'>真心话</span>
				<?php } elseif ($value['type']=='2') { ?>
				<span class="label label-danger" style='margin-right:5px'>大冒险</span>
				<?php } else { ?>
				<span class="label label-info" style='margin-right:5px'>看演技</span>
				<?php } ?>
				<?php echo $value['content'];?>
	</div>
				<div class="btn-group  btn-group-xs  pull-right ">
				<button type="button" class="btn btn-default btn-sm" onclick='agree(<?php echo $value["_id"];?>)'>通过</button>	
				<button type="button" class="btn btn-default btn-sm" onclick='del(<?php echo $value["_id"];?>)'>删除</button>
				</div>
				
				</td>

			</tr>
			<?php }?>
		</tbody>
	</table>
</div>
