<template>
  <div>
    <div class="container ed-container" v-if="order">
      <div class="card ed-card shadow bg-secondary mb-4">
        <h4 class="ed-card-section">Cliente:</h4>
        <div class="row" v-if="customer">
          <div class="col-2">[FOTO CLIENTE]</div>
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
            v-on:click.prevent="doNothing()"
          >
            Entregue!
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
                    v-on:click.prevent="doNothing()"
                  >
                    Entregar!
                  </button>
                </form>
              </div>
            </div>
          </li>
          <li
            class="list-group-item bg-secondary"
            v-for="i in ordersReadyLength - 2"
            v-bind:key="i"
          >
            <ul>
              <li>Encomenda #{{ ordersReady[i + 1].id }}</li>
              <li>Cliente: {{ ordersReady[i + 1].customer.name }}</li>
              <li>Pronta às {{ ordersReady[i + 1].current_status_at }}</li>
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
    };
  },
  methods: {
    doNothing: function () {},
    refreshTimmer: function () {},
    loadData: async function () {
      this.customer = await axios
        .get(`/api/customers/${this.order.customer_id}`)
        .then((response) => {
          return response.data;
        });
      axios.get("/api/orders-ready").then((response) => {
        if (response.data.data.length) {
          this.ordersReady = response.data.data;
          this.ordersReadyLength = response.data.data.length;
        }
      });
    },
  },
  async mounted() {
    this.order = this.$store.state.order.data;
    if (this.order) {
      this.orderTimmer = new Date(this.order.current_status_at);
      this.orderItems = this.$store.state.order.orderItems;
      await this.loadData();
    }
  },
  components: {
    timmer: TimmerComponent,
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