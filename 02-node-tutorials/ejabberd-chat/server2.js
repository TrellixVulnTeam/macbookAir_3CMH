const { client, xml } = require('@xmpp/client')
const debug = require('@xmpp/debug')

const xmpp = client({
    service: 'xmpp://localhost:5222',
    domain: 'localhost',
    resource: '',
    username: 'user1',
    password: 'passw0rd',
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

        await xmpp.send(xml('presence', { type: 'available' }))
        // await xmpp.stop()
    }
})

xmpp.on('online', async address => {
    // Makes itself available
    await xmpp.send(xml('presence'))
    const message = xml(
        'message',
        { type: 'chat', to: 'admin@localhost' },
        xml('body', {}, 'hello from node')
    )
    xmpp.send(message)
});

const message = xml(
    'message',
    { type: 'chat', to: 'admin@localhost' },
    xml('body', {}, 'hello João from node')
)
xmpp.send(message)

xmpp.start().catch(console.error)
