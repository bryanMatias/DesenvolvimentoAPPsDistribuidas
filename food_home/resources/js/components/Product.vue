<template>
  <div class="card shadow mb-3">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">{{ product.name }}</h6>
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
};
</script>

<style>
img {
    max-width:100%;
}
</style>