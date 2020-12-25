<template>
  <span>
    {{ padNumber(hour, 2) }}:{{ padNumber(minute, 2) }}:{{ padNumber(second, 2) }}
  </span>
</template>

<script>
export default {
  props: ["dateStart"],
  data: function () {
    return {
      hour: 0,
      minute: 0,
      second: 0,
      t: null,
    };
  },
  methods: {
    incrementMinutes: function () {
      if (this.minute == 59) {
        this.minute = 0;
        this.hour += 1;
      } else {
        this.minute += 1;
      }
    },
    incrementSeconds: function () {
      if (this.second == 59) {
        this.second = 0;
        this.incrementMinutes();
      } else {
        this.second += 1;
      }
    },
    padNumber: function (number, pad) {
      var s = String(number);
      while (s.length < (pad || 2)) {
        s = "0" + s;
      }
      return s;
    },
  },
  mounted(){
    var now = new Date();
    
    var diffDate = new Date(now.getTime() - this.dateStart.getTime());
    this.hour = diffDate.getHours() - 1;
    this.minute = diffDate.getMinutes();
    this.second = diffDate.getSeconds();
    this.t = setInterval(() => {
      this.incrementSeconds();
    }, 1000);
  },
  beforeDestroy(){
    clearInterval(this.t);
  },
};
</script>

<style>
</style>