<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <!-- Basic Card Example -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
          </div>
          <div class="card-body">
            <form method="POST">
              <label for="productName">Nome: </label>
              <input
                type="text"
                class="form-control"
                name="name"
                id="productName"
                v-model="searchName"
              />

              <label for="productType">Tipo: </label>
              <select id="productType" v-model="searchType">
                <option value="">Tudo</option>
                <option value="hot dish">Comida Quente</option>
                <option value="cold dish">Comida Fria</option>
                <option value="drink">Bebida</option>
                <option value="dessert">Sobremesa</option>
              </select>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row" v-if="searchName">
      <div
        class="col-12 col-sm-6 col-md-4 col-lg-3"
        v-for="product in filteredByName"
        v-bind:key="product.id"
      >
        <product-card :product="product"></product-card>
      </div>
    </div>
    <div class="row" v-else-if="!searchType">
      <div
        class="col-12 col-sm-6 col-md-4 col-lg-3"
        v-for="product in products"
        v-bind:key="product.id"
      >
        <product-card :product="product"></product-card>
      </div>
    </div>
    <div class="row" v-else>
      <div
        class="col-12 col-sm-6 col-md-4 col-lg-3"
        v-for="product in filterdByType"
        v-bind:key="product.id"
      >
        <product-card :product="product"></product-card>
      </div>
    </div>
  </div>
</template>

<script>
import ProductCardComponent from "../components/Product.vue";

export default {
  data: function () {
    return {
      searchName: "",
      searchType: "",
      products: [],
    };
  },
  methods: {
    getProducts: function () {
      axios.get("api/products").then((response) => {
        this.products = response.data;
      });
    },
  },
  mounted() {
    this.getProducts();
  },
  components: {
    "product-card": ProductCardComponent,
  },
  computed: {
    filterdByType() {
      return this.products.filter((product) => {
        return product.type === this.searchType;
      });
    },
    filteredByName() {
      return this.products.filter((product) => {
        return (
          product.name.toLowerCase().indexOf(this.searchName.toLowerCase()) > -1
        );
      });
    },
  },
};
</script>

<style>
#welcome-msg {
  font-size: 60px;
}
ul {
  list-style-type: none;
  float: left;
}
</style>