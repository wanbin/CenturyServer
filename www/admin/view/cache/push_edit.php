<? if(!defined('IN_MOOPHP')) exit('Access Denied');?>
<script>
function del(pushid){
	var content=$("#push_content").val();
	$.post("command/command_base.php?mod=push&action=del", {
		pushid:pushid
	}, function(data) {
		alert(data);
		$("#tr_"+pushid).fadeOut();
	}, "html");
}
</script>
<div class="container">

<h3>推送管理</h3>
	<table class="table table-striped">
		<tbody>
			<tr>
				<td>
				序号
				</td>
				<td>
				内容
				<td>
				发送时间
				</td>
				<td>
				总共
				</td>
					<td>
				剩余
				</td>
					<td>
				状态
				</td>
					<td>
				操作
				</td>
			</tr>
			<?php foreach((array)$ret as $key=>$value) {?>
			<tr id="tr_<?php echo $value['_id'];?>">
			<td>
				<?php echo $value['_id'];?>
				</td>
				<td>
				<?php echo $value['content'];?>
				<td>
				<?php echo $value['sendtime'];?>
				</td>
				<td>
				<?php echo $value['total'];?>
				</td>
					<td>
				<?php echo $value['remain'];?>
				</td>
					<td>
					<?php if($value['issend']=='1') { ?>
					已发送
					<?php } else { ?>
					<span style='color:green'>未发送</span>
				<?php } ?>
				</td>
				<td>
					<button type="button" class="btn btn-default btn-sm" onclick='del(<?php echo $value["_id"];?>)'>删除</button>
				</td>
			</tr>
			<?php }?>
		</tbody>
	</table>

</div>