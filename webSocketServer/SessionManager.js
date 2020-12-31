class SessionManager {
    constructor() {
        this.usersMap = new Map()
        this.usersList = []
    }
    getUserSession(userID) {
        return this.usersMap.get(userID)
    }
    getUsersList(){
        return this.usersList;
    }
    addUserSession(user, socketID) {
        // If already exists a session for this user ID, reuse the session
        let userSession = this.getUserSession(user.id)
        if (userSession) {
            userSession.user = user
            userSession.socketID = socketID
            return
        }
        // Otherwise, create a new session
        userSession = {
            user: user,
            socketID: socketID
        }
        this.usersMap.set(user.id, userSession)
        this.usersList.push(userSession)
        return userSession
    }
    removeUserSession(userID) {
        let userSession = this.getUserSession(userID)
        if (!userSession) {
            return null
        }
        let cloneUserSession = Object.assign({}, userSession)
        this.usersMap.delete(cloneUserSession.user.id)
        this.usersList.splice(this.usersList.findIndex(x => x.user.id === cloneUserSession.user.id), 1)

        return cloneUserSession
    }
}
module.exports = SessionManager
