<template>
  <div>
    <div class="container ed-container" v-if="order">
      <div class="card ed-card shadow bg-secondary mb-4">
        <h4 class="ed-card-section">Cliente:</h4>
        <div class="row" v-if="customer">
          <div class="col-2">
            <img :src="'/storage/fotos/' + customer.photo_url" />
          </div>
          <div class="col-10">
            <ul>
              <li>Nome: {{ customer.name }}</li>
              <li>Morada: {{ customer.address }}</li>
              <li>Telefone: {{ customer.phone }}</li>
              <li>E-mail: {{ customer.email }}</li>
            </ul>
          </div>
        </div>
        <hr class="ed-divider" />
        <h4 class="ed-card-section">Encomenda #{{ order.id }}:</h4>
        <p>Items:</p>
        <ul>
          <li v-for="orderItem in orderItems" v-bind:key="orderItem.id">
            {{ orderItem.id }} - {{ orderItem.product.name }}
            {{ orderItem.quantity }}x
          </li>
        </ul>
        <p v-if="order.notes">Notas: {{ order.notes }}</p>
        <hr class="ed-divider" />
        <h4 class="ed-card-section">Temporizadores:</h4>
        <div v-if="orderTimmer">
          Tempo desde o inicio da entrega:
          <timmer :dateStart="orderTimmer"></timmer>
          <p>Inicio: {{ order.current_status_at }}</p>
        </div>
        <hr class="ed-divider" />
        <form method="POST">
          <button
            type="submit"
            class="btn btn-primary"
            v-on:click.prevent="deliverOrder()"
          >
            Finalizar entrega!
          </button>
        </form>
      </div>
    </div>

    <div class="container ed-container" v-else-if="ordersReadyLength">
      <div class="card ed-card shadow bg-secondary mb-4">
        <div class="card-header bg-secondary">
          <h4 class="ed-card-section">Encomendas prontas para entrega:</h4>
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item bg-success">
            <div class="row">
              <div class="col-10">
                <ul>
                  <li>Encomenda #{{ ordersReady[0].id }}</li>
                  <li>Cliente: {{ ordersReady[0].customer.name }}</li>
                  <li>Pronta às {{ ordersReady[0].current_status_at }}</li>
                </ul>
              </div>
              <div class="col-2 justify-content-center align-items-center">
                <form method="POST">
                  <button
                    type="submit"
                    class="btn btn-primary"
                    v-on:click.prevent="requestNextOrder()"
                  >
                    Entregar!
                  </button>
                </form>
              </div>
            </div>
          </li>
          <li
            class="list-group-item bg-secondary"
            v-for="i in ordersReadyLength - 1"
            v-bind:key="i"
          >
            <ul>
              <li>Encomenda #{{ ordersReady[i].id }}</li>
              <li>Cliente: {{ ordersReady[i].customer.name }}</li>
              <li>Pronta às {{ ordersReady[i].current_status_at }}</li>
            </ul>
          </li>
        </ul>
      </div>
    </div>

    <div class="container ed-container" v-else>
      <div class="card ed-card shadow bg-secondary mb-4">
        <h4 class="ed-card-section">
          De momento não existem encomendas prontas para entrega. Aguarde um
          momento.
        </h4>
      </div>
    </div>
  </div>
</template>

<script>
import TimmerComponent from "../components/Timmer.vue";

export default {
  data: function () {
    return {
      customer: undefined,
      order: undefined,
      orderItems: [],
      ordersReady: undefined,
      ordersReadyLength: 0,
      orderTimmer: undefined,
      deliveryMan: JSON.parse(sessionStorage.getItem("userAuth")),
    };
  },
  methods: {
    doNothing: function () {
      console.log("Nothing happened!");
    },
    refreshTimmer: function () {},
    deliverOrder: function () {
      axios
        .put(`/api/orders/${this.order.id}/deliver`, {deliverStart : this.orderTimmer})
        .then((response) => {
          //console.log("Encomenda entregue!");
          //console.log(response.data);
          this.order = undefined;
          this.customer = undefined;
          this.orderItems = [];
          this.requestOrdersReady();
          this.$socket.emit('order_delivered', "HI PPL!");

        })
        .catch((error) => {
          console.log("Nao foi possivel entregar a encomenda!");
          console.log(error.response.data);
        });
    },
    requestAssignedOrder: function(){
      axios
        .get(`/api/deliverymans/${this.deliveryMan.id}/order`)
        .then(async (response) => {
          this.order = response.data;
          await axios
            .get(`/api/orders/${response.data.id}/order-items`)
            .then((response) => {
              this.orderItems = response.data;
            });
          await axios
            .get(`/api/customers/${this.order.customer_id}`)
            .then((response) => {
              this.customer = response.data;
            });
          this.orderTimmer = new Date(this.order.current_status_at);
        })
        .catch((error) => {
          console.log("Might not have any orders assigned!");
        });
    },
    requestNextOrder: function() {
      axios.put("/api/orders-ready/next", {delviveryman_id: this.deliveryMan.id})
        .then((response) => {
          //console.log("Requested order to deliver!");
          //console.log(response.data);
          this.$socket.emit('order_requested', response.data);
          this.requestAssignedOrder();
        })
        .catch((error) => {
          console.log("There is no order to deliver yet!");
        });
    },
    requestOrdersReady: function () {
      axios.get("/api/orders-ready").then((response) => {
        this.ordersReady = response.data.data;
        this.ordersReadyLength = response.data.data.length;
      });
    },
  },
  mounted() {
    this.requestAssignedOrder();
    this.requestOrdersReady();
  },
  components: {
    timmer: TimmerComponent,
  },
  sockets: {
    order_delivered(payload){
      console.log(payload);
    },
    order_requested(payload){
      console.log("Hey, someone requested the following order:");
      console.log(payload);
      this.requestOrdersReady();
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
</style>