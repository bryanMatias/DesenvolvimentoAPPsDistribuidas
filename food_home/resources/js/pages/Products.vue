<template>
  <div class="container">
    <div class="row">
      <div
        class="col-12 col-sm-6 col-md-4 col-lg-3"
        v-for="product in products"
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
      products: undefined,
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