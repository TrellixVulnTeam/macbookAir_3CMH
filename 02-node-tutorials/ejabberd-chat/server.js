process.env["NODE_TLS_REJECT_UNAUTHORIZED"] = 0;

const { client, xml } = require('@xmpp/client')
const debug = require('@xmpp/debug')
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

io.on('connection', socket => {

    let messages = [];

    const xmpp = client({
        service: 'ws://localhost:5222',
        domain: 'localhost',
        resource: '',
        username: 'user1',
        password: 'farcryw023',
    })

    debug(xmpp, true)

    xmpp.on('error', err => {
        console.error(err)
    })

    xmpp.on('offline', () => {
        console.log('offline')
    })

    xmpp.on('stanza', async stanza => {

        if (stanza.is('presence')) {
            console.log('Usuário online')
            console.log(stanza.attrs.from)
        }

        if (stanza.is('message')) {

            const data = {
                message: stanza.getChild('body').text(),
                author: stanza.attrs.from
            };

            console.log(data);

            socket.emit('receivedMessage', data);

            await xmpp.send(xml('presence', { type: 'available' }))
            // await xmpp.stop()
        }
    })

    xmpp.on('online', async address => {
        // Makes itself available
        await xmpp.send(xml('presence'))
    });

    xmpp.start().catch(console.error)

    console.log(`socket conectado: ${socket.id}`);

    socket.emit('previousMessages', messages);

    socket.on('sendMessage', async (data) => {
        messages.push(data);
        // Sends a chat message to itself
        const message = xml(
            'message',
            { type: 'chat', to: 'admin@localhost' },
            xml('body', {}, data.message)
        )
        await xmpp.send(message)

        socket.broadcast.emit('receivedMessage', data);
    })

    socket.on('disconnect', function () {
        console.log('disconnected')
    });
});

server.listen(3333);