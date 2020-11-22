<template>
  <div class="card shadow mb-3">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">{{ product.name }}</h6>
    </div>
    <div class="card-body">PHOTO</div>
    <div class="card-footer">
      <ul>
        <li>Type: {{ product.type }}</li>
        <!-- <li>Description: {{ product.description }}</li> -->
        <li>Price: {{ product.price }}€</li>
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
</style>