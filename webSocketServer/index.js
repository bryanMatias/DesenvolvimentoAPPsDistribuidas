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
            if(user.type === 'EC'){ //Se for cozinheiro...
                //sessions.availableCooks -> Se está nesta lista é porque esta disponivel
                    //sessions.holdingOrders -> se tem alguma coisa atribui a este cooker

            }

            /*
            VERIFICAR SE ESTA NA LISTA: let index = this.LISTA.findIndex(x => x.id === USER.id);
                se devolver -1 é porque não está
                se devolver o indice é porque está

            REMOVER ELEMENTO COM INDICE 'index' NA LISTA: this.LISTA.splice(index, 1);
            */

        }
    });

    socket.on('user_logged_out', (user) => {
        if (user) {
            socket.leave(user.type)
            sessions.removeUserSession(user.id)
            console.log(`User logged OUT: UserID=${user.id} SocketID=${socket.id}`)
            console.log(`  -> Total Sessions=${sessions.usersMap.size}`)
            io.to('EM').emit('user_logged_out', user)
        }
    });

    socket.on('blocked_user', (user) => {
        let session = sessions.getUserSession(user.id);
        if (session) {
            console.log(`Blocking a user -> ${user.name}`);
            io.to(session.socketID).emit('blocked_user');
        }
    });

    socket.on('order_canceled', (order) => {
        let userID;
        switch(order.status){
            case 'P':
            case 'T':
                userID = order.employee.id;
                break;
            default:
                return;
        }
        let session = sessions.getUserSession(userID);
        if (session) {
            console.log(`Canceling order -> ${order.id}`);
            io.to(session.socketID).emit('order_canceled');
        }
    });

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
