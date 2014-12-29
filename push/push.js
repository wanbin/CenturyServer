//这个进行push推送
var redis_ip='127.0.0.1',  
    redis_port ='6379';

var push_id=0;
var redisKey="Push_Map_"+push_id;

var redis = require('redis').createClient(redis_port,redis_ip);

//var JPush = require("/Users/wanbin/node_modules/jpush-sdk/lib/JPush/JPush.js");
var JPush = require("jpush-sdk");
var client = JPush.buildClient('1fca24a4ae00341567e84792', '2a8228f3e58823fc5f4ace55');

getMessage();

function getMessage() {
	redis.BRPOP(redisKey, 0, function(err, data) {
	var json= eval("("+data[1]+")");
	client.push().setPlatform(json.channel)
    .setAudience(JPush.alias(json.alias))
    .setNotification(JPush.android(json.content, null, 1))
    //.setMessage('msg content')
    .setOptions(null, 60)
    .send(function(err, res) {
        if (err) {
            console.log(err.message);
        } else {
            console.log('Sendno: ' + res.sendno);
            console.log('Msg_id: ' + res.msg_id);
        }
    });
		getMessage();
	});
}

function userLogin(username){
	var myDate = new Date();
	redis2.HSET(redisLogin,username,myDate.getTime(),function(err, data) {
		console.log("username add to redis:"+username);
	});
}


//鍒锋柊瀹㈡埛绔垪琛�
function socketReflashClient(id){
	 for(var values in socketUser){
         if(socketUser[values].socket.id==id){
        	 redis2.HDEL(redisLogin,socketUser[values].username);
        	 console.log("get and remove the disconnect id:"+id);
        	 return;
         }
     }
	 
}


function sendToClient(username, content) {
	if(socketUser[username]==null)
	{
		console.log("can't find "+username);
		return false;
	}
	socketUser[username].socket.emit('server_send', {
				'data' : content
			});
}