var app = require('express')();
var server =require('http').Server(app);
var io = require('socket.io')(server);
server.listen(3000);

 
io.on('connection', function(socket){
     socket.on('request', function(req){
         io.emit('request',req);
     });
     socket.on('typings', function(req){
        io.emit('typings',req);
    });
    socket.on('seen', function(req){
        io.emit('seen',req);
    });
});