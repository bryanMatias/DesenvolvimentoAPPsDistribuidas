<template>
  <div>
    <div class="container ed-container">
      <div class="card ed-card shadow bg-secondary mb-4">
        <h4 class="ed-card-section">Cliente:</h4>
        <div class="row">
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
          <li v-for="orderItem in order.order_items" v-bind:key="orderItem.id">
            {{ orderItem.id }} - {{ orderItem.product.name }}
            {{ orderItem.quantity }}x
          </li>
        </ul>
        <p v-if="order.notes">Notas: {{ order.notes }}</p>
        <hr class="ed-divider" />
        <h4 class="ed-card-section">Temporizadores:</h4>
        <timmer
          :hours="hours"
          :minutes="minutes"
          :seconds="seconds"
          @increment-seconds="incrementSeconds"
          @increment-minutes="incrementMinutes"
        ></timmer>
        <p>Inicio: 12:00:00 12/12/2000</p>
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

    <div class="container ed-container" v-if="ordersReadyLength">
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
      ordersReady: undefined,
      ordersReadyLength: 0,
      hours: 0,
      minutes: 0,
      seconds: 0,
    };
  },
  methods: {
    doNothing: function () {},
    refreshTimmer: function () {},
    incrementMinutes: function () {
      if (this.minutes == 59) {
        this.minutes = 0;
        this.hours += 1;
      } else {
        this.minutes += 1;
      }
    },
    incrementSeconds: function () {
      if (this.seconds == 59) {
        this.seconds = 0;
        this.incrementMinutes();
      } else {
        this.seconds += 1;
      }
    },
    padNumber: function (number, pad) {
      var s = String(number);
      while (s.length < (pad || 2)) {
        s = "0" + s;
      }
      return s;
    },
    loadData: function () {
      axios.get("api/customers/74").then((response) => {
        this.customer = response.data;
      });
      axios.get("api/orders/45").then((response) => {
        this.order = response.data;

        //Inicializar o contador de tempo [Atenção à hora por causa do fuso horario]
        var tempoAntes = new Date(2020, 10, 25, 10, 10, 0);
        var tempoAgora = Date.now();
        var begindate = new Date(tempoAgora - tempoAntes.getTime());
        this.hours = begindate.getHours();
        this.minutes = begindate.getMinutes();
        this.seconds = begindate.getSeconds();
        window.setInterval(() => {
          this.incrementSeconds();
        }, 1000);
      });
      axios.get("api/orders-ready").then((response) => {
        if (response.data.data.length) {
          this.ordersReady = response.data.data;
          this.ordersReadyLength = response.data.data.length;
        }
      });
    },
  },
  mounted() {
    this.loadData();
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