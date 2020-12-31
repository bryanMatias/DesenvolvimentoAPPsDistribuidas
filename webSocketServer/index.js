var httpServer = require('http').createServer()
var io = require('socket.io')(httpServer)

let SessionManager = require("./SessionManager.js")

httpServer.listen(8080, function () {
    console.log('listening on *:8080')
})

let sessions = new SessionManager()

io.on('connection', function (socket) {

    //////////////////////////////////////////
    ////////////////PARA USERS////////////////
    //////////////////////////////////////////
    socket.on('user_logged', (user) => {
        if (user) {
            sessions.addUserSession(user, socket.id)
            socket.join(user.type)
            console.log(`User Logged: UserID=${user.id} SocketID=${socket.id}`)
            console.log(`  -> Total Sessions=${sessions.usersMap.size}`)
            io.to('EM').emit('user_logged_in', user)
        }
    })

    socket.on('user_logged_out', (user) => {
        if (user) {
            socket.leave(user.type)
            sessions.removeUserSession(user.id)
            console.log(`User logged OUT: UserID=${user.id} SocketID=${socket.id}`)
            console.log(`  -> Total Sessions=${sessions.usersMap.size}`)
            io.to('EM').emit('user_logged_out', user)
        }
    })

    //////////////////////////////////////////
    /////////////PARA DELIVERYMANS////////////
    //////////////////////////////////////////
    socket.on('order_requested', (order) => {
        console.log(`Order ID=${order.id} was request from a deliveryman...`);
        io.to('ED').emit('order_requested', order);
        io.to('EM').emit('order_requested', order);
    });

    socket.on('order_delivered', (order) => {
        console.log(`Order ID=${order.id} was delivered from a deliveryman...`);
        io.to('EM').emit('order_delivered', order);
    });

    //////////////////////////////////////////
    ///////////PARA MANAGERS//////////////////
    //////////////////////////////////////////
    socket.on('request_users', (payload) => {
        let session = sessions.getUserSession(payload.id);
        if (session) {
            io.to(session.socketID).emit('request_users', sessions.getUsersList());
        }
    });

    /*     socket.on('global_message', (payload) => {
            switch (payload.destination) {
                case 'ED':
                    io.to(payload.destination).emit('global_message', payload);
                    break;
                default:
                    io.emit('global_message', payload);
                    break;
            }
        }) */

})
