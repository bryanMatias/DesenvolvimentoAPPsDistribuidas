<template>
  <div>
    <span>{{ hours }}:{{ minutes }}:{{ seconds }}</span>
  </div>
</template>

<script>
export default {
  props: ["hours", "minutes", "seconds"],
  methods: {
    incrementMinutes: function () {
        this.$emit("increment-minutes");
    },
    incrementSecond: function () {
        this.$emit("increment-seconds");
    },
  },
};
</script>

<style>
</style>