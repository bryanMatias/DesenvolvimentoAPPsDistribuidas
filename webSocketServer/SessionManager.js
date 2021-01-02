var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;

class SessionManager {
    constructor() {
        this.usersMap = new Map();
        this.usersList = [];

        this.holdingOrders = [];
        this.availableCooks = [];

        const Http = new XMLHttpRequest();
        const url='http://homestead.test/api/orders-holding';

        var self = this;
        Http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                self.holdingOrders = JSON.parse(this.responseText);
            }
        };
        Http.open("GET", url);
        Http.send();

    }
    getUserSession(userID) {
        return this.usersMap.get(userID);
    }
    getUsersList(){
        return this.usersList;
    }
    addUserSession(user, socketID) {
        // If already exists a session for this user ID, reuse the session
        let userSession = this.getUserSession(user.id);
        if (userSession) {
            userSession.user = user;
            userSession.socketID = socketID;
            return;
        }
        // Otherwise, create a new session
        userSession = {
            user: user,
            socketID: socketID,
        }
        this.usersMap.set(user.id, userSession);
        this.usersList.push(userSession);

        if(user.type == 'EC'){ //Se for cozinheiro
            const Http = new XMLHttpRequest();
            const url=`http://homestead.test/api/cooks/${user.id}/order`;
    
            var index;
            var self = this;
            Http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //console.log(JSON.parse(this.responseText));
                    if(this.responseText){ //Se este cozinheiro esta a preparar uma encomenda...
                        console.log("Cook ocupied");
                    } else {  //Se este cozinheiro está disponivel ...                        
                        self.availableCooks.push(user);
                    }
                    //self.holdingOrders.push(JSON.parse(this.responseText));
                }
            };
            Http.open("GET", url);
            Http.send();
        }

        return userSession;
    }
    removeUserSession(userID) {
        let userSession = this.getUserSession(userID);
        if (!userSession) {
            return null;
        }
        let cloneUserSession = Object.assign({}, userSession);
        this.usersMap.delete(cloneUserSession.user.id);
        this.usersList.splice(this.usersList.findIndex(x => x.user.id === cloneUserSession.user.id), 1);
        if(cloneUserSession.user.type === 'EC'){
            let index = this.availableCooks.findIndex(x => x.id === cloneUserSession.user.id);
            if(index != -1){
                this.availableCooks.splice(index, 1);
            }
        }
        return cloneUserSession;
    }
}
module.exports = SessionManager;
