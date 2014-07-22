<!--
/**
 * 极光推送-V2. PHP服务器端
 * @author 夜阑小雨
 * @Email 37217911@qq.com
 * @Website http://www.yelanxiaoyu.com
 * @version 20130118 
 */
-->
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
               
            });       
			 function sendPushall(){
                var data = $('form#all').serialize();
                $('form#all').unbind('submit');  
				             
                $.ajax({
                    url: "send.php?action=send",
                    type: 'GET',
                    data: data,
                    beforeSend: function() {
						
						   html = "<b>请等待，正在发送中……</b>";
						    $('.info').html(html);
                        
                    },
                    success: function(data, textStatus, xhr) {
                         // $('.txt_message').val("");						  
						  $('.info').html(data);
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        
                    }
                });
                return false;
            }
		 
        </script>     
    </head>
    <body>
    	<div class="head">
         <h1>Andriod消息推送管理</h1>
         <span><a href="index.php">发送推送消息</a></span>
         <span><a href="index.php?action=pushList">查看发送记录</a></span>
          <hr/>
        </div>    
         <?php
        include_once 'config.inc.php';
		include_once 'db.class.php'; 
		dataConnect();
		$sql = "SELECT * FROM ".DB_TAB." ORDER BY `created` DESC";
        $users = mysql_query($sql);
		//$info1 = mysql_fetch_row($users); 
		//print_r($info1);
		
        if ($users != false)
            $no_of_users = mysql_num_rows($users);
        else
            $no_of_users = 0;
		
		 switch ($_REQUEST['action']){
			 	case 'pushList':
				?>
      			<div class="container list">            
				 <ul class="devices">
                 <p style="font-weight:bold; color:#06F;"><span>序号</span><span style="width:150px;">发送时间</span><span style="width:400px;">消息内容</span><span>发送结果</span><span>满足条件</span><span>推送成功</span></p>
              <?php
                if ($no_of_users > 0) {
                    ?>
                    <?php
                    while ($row = mysql_fetch_array($users)) {
                        ?>
                          <p><span><?php echo $row["sendno"] ?></span><span style="width:150px;"><?php echo $row["created"] ?></span><span style="width:400px;"><?php echo $row["n_content"] ?></span><span><?php echo $row["errmsg"] ?></span><span><?php echo $row["total_user"] ?></span><span><?php echo $row["send_cnt"] ?></span></span></p>
                    <?php }
                } else { ?> 
                    <li>
                        No Users Registered Yet!
                    </li>
                <?php } ?>
            </ul>
            </div>
            <?php 			
				break;
				default: 			 
			 ?>        
        <div class="container">            
            <hr/>
            
            <ul class="devices">            
                 <li>
                <form id="all" method="post" onsubmit="return sendPushall()">  
                <p><label>标    题:</label><input type="text" name="n_title" placeholder="Type message title here" ></span></p>
                 <p><label>消息内容:</label>                   
                <textarea rows="3" name="n_content" cols="105" class="n_content" placeholder="Type message here"></textarea><p>
               	<p><input type="submit" class="send_btn" value="Send" onclick=""/></p>
                 </form>    
                </li>
                <li>
                <h3>回馈信息：</h3>
                <div class="info">
                </div>
            </ul>
           </div>   
        
        
        <?php } ?>  
        
        
        
        
           <style type="text/css">		    
			 .head,
            .container{
                width: 950px;
                margin: 0 auto;
                padding: 0;
            }
			 .list p{ line-height:24px; padding:0; margin:0;}
			 .list span{
				 display:inline-block;
				 padding:5px;
				 border:1px solid #666;
				 width:60px;
				 font-size:12px;		  				 
			 }
			 .head span a{
				 color:#F00;
				 font-size:16px;
				 border:1px #FF0 solid;
				 
				 
				 
			 }
			
            h1{
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 24px;
                color:#900;
            }
			h2{
				font-size: 16px;
				color:#00F;
				
			}
            div.clear{
                clear: both;
            }
            ul.devices{
                margin: 0;
                padding: 0;
            }
            ul.devices li{                
                list-style: none;
                border: 1px solid #dedede;
                padding: 10px;
                margin: 0 15px 25px 0;
                border-radius: 3px;
                -webkit-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
                -moz-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
                box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                color: #555;
            }
            ul.devices li label, ul.devices li span{
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 12px;
                font-style: normal;
                font-variant: normal;
                font-weight: bold;
                color: #393939;
                display: block;
                float: left;
            }
			ul.devices li span{
				margin-right:10px;
				font-weight:300;
			}
				
			ul.devices li p{
				
				display:block;
				margin-right:10px;
			}
            ul.devices li label{
				color:#06F;
				margin-right:10px;
			            
            }
            ul.devices li textarea{
                
                resize: none;
            }
            ul.devices li .send_btn{
                background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#0096FF), to(#005DFF));
                background: -webkit-linear-gradient(0% 0%, 0% 100%, from(#0096FF), to(#005DFF));
                background: -moz-linear-gradient(center top, #0096FF, #005DFF);
                background: linear-gradient(#0096FF, #005DFF);
                text-shadow: 0 1px 0 rgba(0, 0, 0, 0.3);
                border-radius: 3px;
                color: #fff;
            }
			
			 b{
				 color:#F00;
			 }
			 .info{ 
				 color:#F00;
			 }
        </style>
    </body>
</html>
