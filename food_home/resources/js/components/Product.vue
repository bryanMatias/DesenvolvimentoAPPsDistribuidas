<template>
  <div class="card shadow mb-3">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">{{ product.name }}</h6>
      <div v-if="isManager">
        <a class="btn btn-success btn-circle btn-sm" v-on:click.prevent="fillForm"><i class="fas fa-edit"></i></a>
        <a class="btn btn-danger btn-circle btn-sm" v-on:click.prevent="removeProduct"><i class="fas fa-trash"></i></a>
      </div>
    </div>
    <div class="card-body">
      <img :src="'/storage/products/' + product.photo_url" />
    </div>
    <div class="card-footer">
      <ul>
        <li>Tipo: {{ product.type }}</li>
        <li>Preço: {{ product.price }}€</li>
        <hr/>
        <li>{{ product.description }}</li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: ["product"],
  data: function(){
    return {
      isManager: this.$store.state.user.type === 'EM',
    }
  },
  methods:{
    removeProduct(){
      this.$emit("remove-product", this.product);
    },
    fillForm(){
      this.$emit("fill-form", this.product);
    }
  },
};
</script>

<style>
img {
    max-width:100%;
}
</style>