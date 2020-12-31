<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <!-- Basic Card Example -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Gerir Produto</h6>
          </div>
          <div class="card-body">
            <div
              id="main-message"
              :class="[isError ? 'text-danger' : 'text-success']"
              v-if="invalidSignMessages.mainError"
            >
              {{ invalidSignMessages.mainError }}
            </div>
            <form enctype="multipart/form-data">
              <div class="form-group">
                <label for="productName">Nome: </label>
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="productName"
                  v-model="managedProduct.name"
                />
              </div>
              <span
                class="failed-message"
                role="alert"
                v-if="invalidSignMessages.name"
              >
                <strong>{{ invalidSignMessages.name }}</strong>
              </span>

              <div class="form-group">
                <label for="productType">Tipo: </label>
                <select id="productType" v-model="managedProduct.type">
                  <option value="hot dish">Comida Quente</option>
                  <option value="cold dish">Comida Fria</option>
                  <option value="drink">Bebida</option>
                  <option value="dessert">Sobremesa</option>
                </select>
              </div>
              <span
                class="failed-message"
                role="alert"
                v-if="invalidSignMessages.type"
              >
                <strong>{{ invalidSignMessages.type }}</strong>
              </span>

              <div class="form-group">
                <label for="productDescription">Descrição: </label>
                <textarea
                  id="productDescription"
                  name="description"
                  rows="6"
                  cols="50"
                  v-model="managedProduct.description"
                ></textarea>
              </div>
              <span
                class="failed-message"
                role="alert"
                v-if="invalidSignMessages.description"
              >
                <strong>{{ invalidSignMessages.description }}</strong>
              </span>

              <div class="form-group">
                <label for="productPrice">Preço: </label>
                <input
                  type="number"
                  min="0"
                  step="0.01"
                  v-model="managedProduct.price"
                />
              </div>
              <span
                class="failed-message"
                role="alert"
                v-if="invalidSignMessages.price"
              >
                <strong>{{ invalidSignMessages.price }}</strong>
              </span>

              <div class="form-group">
                <input
                  class="form-control"
                  type="file"
                  accept=".jpg,.png"
                  name="photo"
                  @change="fileChange($event.target.name, $event.target.files)"
                />
              </div>
              <span
                class="failed-message"
                role="alert"
                v-if="invalidSignMessages.photo_url"
              >
                <strong>{{ invalidSignMessages.photo_url }}</strong>
              </span>

              <div class="form-group">
                <a class="btn btn-default" v-on:click.prevent="addProduct"
                  >Novo</a
                >
                <a class="btn btn-default" v-on:click.prevent="updateProduct"
                  >Atualizar</a
                >
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <!-- Basic Card Example -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="form-group">
                <label for="productName">Nome: </label>
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="productName"
                  v-model="searchName"
                  v-on:keydown.enter.prevent="doNothing"
                />
              </div>

              <div class="form-group">
                <label for="productType">Tipo: </label>
                <select id="productType" v-model="searchType">
                  <option value="">Tudo</option>
                  <option value="hot dish">Comida Quente</option>
                  <option value="cold dish">Comida Fria</option>
                  <option value="drink">Bebida</option>
                  <option value="dessert">Sobremesa</option>
                </select>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Search by name -->
    <div class="row" v-if="searchName">
      <div
        class="col-12 col-sm-6 col-md-4 col-lg-3"
        v-for="product in filteredByName"
        v-bind:key="product.id"
      >
        <product-card :product="product" @remove-product="removeProduct" @fill-form="fillForm"></product-card>
      </div>
    </div>

    <!-- Display all products -->
    <div class="row" v-else-if="!searchType">
      <div
        class="col-12 col-sm-6 col-md-4 col-lg-3"
        v-for="product in products"
        v-bind:key="product.id"
      >
        <product-card :product="product" @remove-product="removeProduct" @fill-form="fillForm"></product-card>
      </div>
    </div>

    <!-- Search by type -->
    <div class="row" v-else>
      <div
        class="col-12 col-sm-6 col-md-4 col-lg-3"
        v-for="product in filterdByType"
        v-bind:key="product.id"
      >
        <product-card :product="product" @remove-product="removeProduct" @fill-form="fillForm"></product-card>
      </div>
    </div>
  </div>
</template>

<script>
import ProductCardComponent from "../components/Product.vue";
import { upload } from "../services/file-upload.service";

export default {
  data: function () {
    return {
      productID: null,
      managedProduct: {
        name: "",
        type: "",
        description: "",
        price: 0.0,
        photo_url: "",
      },
      photoFormData: null,
      invalidSignMessages: {
        isError: false,
        mainError: null,
        name: null,
        type: null,
        description: null,
        price: null,
        photo_url: null,
      },
      searchName: "",
      searchType: "",
      products: [],
    };
  },
  methods: {
    doNothing: function () {},
    resetForm: function(){
      this.managedProduct.name = "";
      this.managedProduct.type = "";
      this.managedProduct.description = "";
      this.managedProduct.price = 0.0;
      this.managedProduct.photo_url = "";
    },
    fillForm: function(product){
      this.productID = product.id;
      this.managedProduct.name = product.name;
      this.managedProduct.type = product.type;
      this.managedProduct.description = product.description;
      this.managedProduct.price = product.price;
      this.managedProduct.photo_url = "";
    },
    resetErrors: function(){
      this.invalidSignMessages.name = null;
      this.invalidSignMessages.type = null;
      this.invalidSignMessages.description = null;
      this.invalidSignMessages.price = null;
      this.invalidSignMessages.photo_url = null;
    },
    getProducts: function () {
      axios.get("api/products").then((response) => {
        this.products = response.data;
      });
    },
    addProduct: async function () {
      this.resetErrors();
      if (this.photoFormData) {
        await axios
          .post("/api/upload-photo-product", this.photoFormData, {
            headers: { Accept: "application/json" },
          })
          .then((response) => {
            this.managedProduct.photo_url = response.data;
            console.log("Url foto: " + response.data);
          })
          .catch((error) => {
            console.log("Falhou upload da foto!");
            console.log(error.response.data);
          });
      }

      axios
        .post("/api/products/create", this.managedProduct, {
          headers: { Accept: "application/json" },
        })
        .then((response) => {
          this.products.push(response.data);
          this.isError = false;
          this.invalidSignMessages.mainError = "Novo produto adicionado!";
          this.resetForm();
        })
        .catch((error) => {
          console.log("Invalid new product!");
          console.log(error.response.data);
          this.isError = true;
          this.invalidSignMessages.mainError = error.response.data.message;
          this.invalidSignMessages.name = error.response.data.errors.name === undefined ? null : error.response.data.errors.name[0];
          this.invalidSignMessages.type = error.response.data.errors.type === undefined ? null : error.response.data.errors.type[0];
          this.invalidSignMessages.description = error.response.data.errors.description === undefined ? null : error.response.data.errors.description[0];
          this.invalidSignMessages.price = error.response.data.errors.price === undefined ? null : error.response.data.errors.price[0];
          this.invalidSignMessages.photo_url = error.response.data.errors.photo_url === undefined ? null : error.response.data.errors.photo_url[0];
          this.resetForm();
        });
    },
    updateProduct: async function(product){
      this.resetErrors();
      if (this.photoFormData) {
        await axios
          .post("/api/upload-photo-product", this.photoFormData, {
            headers: { Accept: "application/json" },
          })
          .then((response) => {
            this.managedProduct.photo_url = response.data;
            console.log("Url foto: " + response.data);
          })
          .catch((error) => {
            console.log("Falhou upload da foto!");
            console.log(error.response.data);
          });
      }

      axios
        .put(`/api/products/${this.productID}`, this.managedProduct, {
          headers: { Accept: "application/json" },
        })
        .then((response) => {
          //this.products.push(response.data);
          let index = this.products.findIndex(x => x.id === this.productID);
          this.products[index] = response.data;

          this.isError = false;
          this.invalidSignMessages.mainError = "Produto atualizado!";
          this.resetForm();
        })
        .catch((error) => {
          console.log("Invalid product update!");
          console.log(error.response.data);
          this.isError = true;
          this.invalidSignMessages.mainError = error.response.data.message;
          this.invalidSignMessages.name = error.response.data.errors.name === undefined ? null : error.response.data.errors.name[0];
          this.invalidSignMessages.type = error.response.data.errors.type === undefined ? null : error.response.data.errors.type[0];
          this.invalidSignMessages.description = error.response.data.errors.description === undefined ? null : error.response.data.errors.description[0];
          this.invalidSignMessages.price = error.response.data.errors.price === undefined ? null : error.response.data.errors.price[0];
          this.invalidSignMessages.photo_url = error.response.data.errors.photo_url === undefined ? null : error.response.data.errors.photo_url[0];
          this.resetForm();
        });
    },
    removeProduct: function(product){
      axios.delete(`/api/products/${product.id}`)
        .then((response) => {
          let index = this.products.findIndex(x => x.id === product.id);
          this.products.splice(index, 1);
        })
        .catch((error) => {
          console.log("Failed to remove that product!");
        });
    },
    fileChange(fieldName, files) {
      if (!files) return;

      var file = files[0];

      const formData = new FormData();
      formData.append(fieldName, file);
      this.photoFormData = formData;
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