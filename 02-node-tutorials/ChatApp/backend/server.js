const express = require('express');
const path = require('path');

const app = express();
const server = require('http').createServer(app);
const io = require('socket.io')(server);

app.use(express.static(path.join(__dirname, 'public')));
app.set('views', path.join(__dirname, 'public'));
app.engine('html', require('ejs').renderFile);
app.set('view engine', 'html');

app.use('/', (req, res) => {
  res.render('index.html');
});

const getOnlineUsers = () => {
  let clients = io.sockets.clients().connected;
  let sockets = Object.values(clients);
  let users = sockets.map(s => s.id);
  return users;
}

let messages = [];

io.on('connection', socket => {
  console.log(`socket conectado: ${socket.id}`);

  socket.emit('onlineUsers', getOnlineUsers());
  socket.broadcast.emit('onlineUsers', getOnlineUsers());

  socket.emit('previousMessages', messages);

  socket.on('sendMessage', data => {
    messages.push(data);
    socket.broadcast.emit('receivedMessage', data);
  })

  socket.on('disconnect', function () {
    console.log('disconnected')
    socket.broadcast.emit('onlineUsers', getOnlineUsers());
  });
});

server.listen(3333);

