var redis_ip='127.0.0.1',  
    redis_port ='6379';

var socket_id=0;
var redisKey="Socket_Map_"+socket_id;
var redisLogin="Socket_login_"+socket_id;


var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

var socketUser={};
app.get('/', function(req, res){
	  res.sendfile('index.html');
});
 
var redis = require('redis').createClient(redis_port,redis_ip);
var redis2 = require('redis').createClient(redis_port,redis_ip);

//var redis = require('redis').createClient(redis_port,redis_ip,{"auth_pass":"k0R3zIqQfvDbNLswgkkG"});
//var redis2 = require('redis').createClient(redis_port,redis_ip,{"auth_pass":"k0R3zIqQfvDbNLswgkkG"});
redis2.DEL(redisLogin);

io.on('connection', function(socket) {
	console.log('a user connected  '+socket.id);
	io.sockets.emit('conn', { socketid:socket.id});
	socketUser[socket.id] = {'username':socket.id,'socket':socket}
	socket.on('disconnect', function() {
		socketReflashClient(socket.id);
		console.log('user disconnect: ' + socket.id);
	});
	
	socket.on('login', function(data,msg) {
		socketUser[data.username] = {'username':data.username,'socket':socket}
		console.log('userlogin: ' + data.username+"   socketid:"+socket);
		userLogin(data.username);
	});
});

http.listen(3000, function() {
	console.log('listening on *:3000');
});


getMessage();

function getMessage() {
	redis.BRPOP(redisKey, 0, function(err, data) {
		var json= eval("("+data[1]+")");
		console.log("gameuid or username:"+json.gameuid);
		if(json.username!=''){
			console.log("sendto:" + json.username);
			sendToClient(json.username,data[1]);
		}
		console.log("punish:" + data[1]);
		getMessage();
	});
}

function userLogin(username){
	var myDate = new Date();
	redis2.HSET(redisLogin,username,myDate.getTime(),function(err, data) {
		console.log("username add to redis:"+username);
	});
}


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