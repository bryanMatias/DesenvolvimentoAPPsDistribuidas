<template>
  <dashboardED v-if="userRole === 'ED'"></dashboardED>
  <dashboardEM v-else-if="userRole === 'EM'"></dashboardEM>
</template>

<script>
import DashboardEDComponent from "../components/dashboards/deliveryman.vue";
import DashboardEMComponent from "../components/dashboards/manager.vue";

export default {
  props:["user"],
  data: function () {
    return {
      userRole: this.$store.state.user.type
    };
  },
  methods: {
    
  },
  mounted() {
  },
  components: {
    dashboardED: DashboardEDComponent,
    dashboardEM: DashboardEMComponent,
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