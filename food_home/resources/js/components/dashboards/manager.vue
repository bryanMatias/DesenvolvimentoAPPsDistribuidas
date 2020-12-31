<template>
  <div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Cozinheiros</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table
            class="table table-bordered"
            id="dataTable"
            width="100%"
            cellspacing="0"
          >
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Foto</th>
                <th>Estado</th>
                <th>Encomenda</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="user in cookers"
                v-bind:key="user.id"
                :class="user.state"
              >
                <td>{{ user.id }}</td>
                <td>{{ user.name }}</td>
                <td>
                  <img
                    class="img-profile rounded-circle avatar font-weight-bold"
                    :src="'/storage/fotos/' + user.photo_url"
                  />
                </td>
                <td>{{ user.state }}</td>
                <td>---</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Homens de Entrega</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table
            class="table table-bordered"
            id="dataTable"
            width="100%"
            cellspacing="0"
          >
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Foto</th>
                <th>Estado</th>
                <th>Encomenda</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="user in deliverymans"
                v-bind:key="user.id"
                :class="user.state"
              >
                <td>{{ user.id }}</td>
                <td>{{ user.name }}</td>
                <td>
                  <img
                    class="img-profile rounded-circle avatar font-weight-bold"
                    :src="'/storage/fotos/' + user.photo_url"
                  />
                </td>
                <td>{{ user.state }}</td>
                <td>
                  <ul v-if="user.order">
                    <li>ID: {{ user.order.id }}</li>
                    <li>Desde: {{ user.order.updated_at }}</li>
                    <li>btn</li>
                  </ul>
                  <span v-else>---</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Managers</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table
            class="table table-bordered"
            id="dataTable"
            width="100%"
            cellspacing="0"
          >
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Foto</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="user in managers"
                v-bind:key="user.id"
                :class="user.state"
              >
                <td>{{ user.id }}</td>
                <td>{{ user.name }}</td>
                <td>
                  <img
                    class="img-profile rounded-circle avatar font-weight-bold"
                    :src="'/storage/fotos/' + user.photo_url"
                  />
                </td>
                <td>{{ user.state }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <form>
      <div class="form-group">
        <label for="productType">Estado: </label>
        <select id="productType" v-model="searchOrderStatus">
          <option value="">Todos</option>
          <option value="H">Em espera</option>
          <option value="P">A preparar</option>
          <option value="R">Pronta</option>
          <option value="T">Em transito</option>
        </select>
      </div>
    </form>
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Encomandas ativas</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table
            class="table table-bordered"
            id="dataTable"
            width="100%"
            cellspacing="0"
          >
            <thead>
              <tr>
                <th>ID</th>
                <th>Estado atual</th>
                <th>Empregado</th>
                <th>Data</th>
                <th>btn</th>
              </tr>
            </thead>
            <tbody v-if="!searchOrderStatus"> <!-- Se não existem filtros... mostra todos -->
              <tr v-for="order in activeOrders" v-bind:key="order.id">
                <td>{{ order.id }}</td>
                <td>{{ order.status }}</td>
                <td>
                  <span v-if="order.employee"> {{ order.employee.name }} </span>
                  <span v-else>---</span>
                </td>
                <td>{{ order.current_status_at }}</td>
                <td>_btn</td>
              </tr>
            </tbody>
            <tbody v-else> <!-- Se existem filtros... -->
              <tr v-for="order in filterdOrderByStatus" v-bind:key="order.id">
                <td>{{ order.id }}</td>
                <td>{{ order.status }}</td>
                <td>
                  <span v-if="order.employee"> {{ order.employee.name }} </span>
                  <span v-else>---</span>
                </td>
                <td>{{ order.current_status_at }}</td>
                <td>_btn</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: function () {
    return {
      cookers: [],
      deliverymans: [],
      managers: [],
      activeOrders: [],
      authManager: this.$store.state.user,
      searchOrderStatus: "",
    };
  },
  methods: {
    doNothing: function () {
      console.log("Nothing happened!");
    },
    deliverOrder: function () {
      axios
        .put(`/api/orders/${this.order.id}/deliver`, {
          deliverStart: this.orderTimmer,
        })
        .then((response) => {
          this.order = undefined;
          this.customer = undefined;
          this.orderItems = [];
          this.requestOrdersReady();
          this.$socket.emit("order_delivered", "HI PPL!");
        })
        .catch((error) => {
          console.log("Nao foi possivel entregar a encomenda!");
          console.log(error.response.data);
        });
    },
    requestCookers: function () {
      axios
        .get("/api/cookers")
        .then(async (response) => {
          response.data.forEach((value, index, array) => {
            value.state = "offline";
            this.insertUserInList(value, false);
          });
        })
        .catch((error) => {
          console.log("Error on employees!");
        });
    },
    requestDeliveryMans: function () {
      axios
        .get("/api/deliverymans")
        .then(async (response) => {
          response.data.forEach((value, index, array) => {
            value.state = "offline";
            this.insertUserInList(value, false);
          });
          console.log(response.data);
        })
        .catch((error) => {
          console.log("Error on employees!");
          console.log(error);
        });
    },
    requestManagers: function () {
      axios
        .get("/api/managers")
        .then(async (response) => {
          response.data.forEach((value, index, array) => {
            value.state = "offline";
            this.insertUserInList(value, false);
          });
        })
        .catch((error) => {
          console.log("Error on employees!");
        });
    },
    requestActiveOrders: function () {
      axios
        .get("/api/active-orders")
        .then(async (response) => {
          this.activeOrders = response.data;
        })
        .catch((error) => {
          console.log("Error on active orders!");
        });
    },
    insertUserInList: function (user, isLogged) {
      var userList;
      switch (user.type) {
        case "EC":
          userList = this.cookers;
          break;
        case "ED":
          userList = this.deliverymans;
          break;
        case "EM":
          userList = this.managers;
          break;
      }

      var userReference;
      let index = userList.findIndex((x) => x.id === user.id);
      if (index == -1) {
        userList.push(user);
        userReference = user;
      } else {
        userReference = userList[index];
      }

      if (isLogged) {
        userReference.state = "online";
      }
    },
  },
  mounted() {
    this.requestCookers();
    this.requestDeliveryMans();
    this.requestManagers();
    this.requestActiveOrders();

    this.$socket.emit("request_users", this.$store.state.user);
  },
  components: {},
  sockets: {
    user_logged_in(user) {
      console.log("A user logged in!");

      this.insertUserInList(user, true);
    },
    user_logged_out(user) {
      console.log("A user logged out!");

      var userList;
      switch (user.type) {
        case "EC":
          userList = this.cookers;
          break;
        case "ED":
          userList = this.deliverymans;
          break;
        case "EM":
          userList = this.managers;
          break;
      }

      let index = userList.findIndex((x) => x.id === user.id);
      var userReference = userList[index];
      userReference.state = "offline";
    },
    request_users(users) {
      console.log(users);
      users.forEach((value, index, array) => {
        value.user.state = "online";
        this.insertUserInList(value.user, true);
      });
    },
    order_requested(payload) {

      var index = this.deliverymans.findIndex((x) => x.id === payload.delivered_by);
      var userReference = this.deliverymans[index];
      userReference.order = payload;

      var index = this.activeOrders.findIndex((x) => x.id === payload.id);
      var orderReference = this.activeOrders[index];
      orderReference.status = 'T';
      orderReference.employee = payload.employee;
      orderReference.current_status_at = payload.current_status_at;

    },
    order_delivered(payload) {

      var index = this.deliverymans.findIndex((x) => x.id === payload.delivered_by);
      var userReference = this.deliverymans[index];
      userReference.order = null;

      var index = this.activeOrders.findIndex((x) => x.id === payload.id);
      this.activeOrders.splice(index, 1);

    },
  },
  computed:{
    filterdOrderByStatus() {
      return this.activeOrders.filter((order) => {
        return order.status === this.searchOrderStatus;
      });
    },
  },
};
</script>

<style>
.ed-container {
  color: white;
}
.ed-card {
  padding: 10px;
}
#timmer {
  font-size: 20px;
  font-weight: bold;
}
h4.ed-card-section {
  font-weight: bold;
  text-decoration: underline;
}
hr.ed-divider {
  border: 1px solid white;
}

tr.offline {
  background-color: #ffcccc;
}

tr.online {
  background-color: #ccffcc;
}
</style>