<template>
  <div id="wrapper">
    <sidebar v-if="retUser"></sidebar>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <topbar :user="retUser" @logout="logout"></topbar>

        <router-view></router-view>
      </div>
      <!-- End of Main Content -->

      <foot></foot>
    </div>
    <!-- End of Content Wrapper -->
  </div>
</template>

<script>
import SideBarComponent from "./components/SideBar.vue";
import TopBarComponent from "./components/TopBar.vue";
import FooterComponent from "./components/Footer.vue";

export default {
  data: function () {
    return { user: null };
  },
  methods: {
    logout() {
      axios
        .post("/api/logout")
        .then((response) => {
          console.log("User has logged out");
          this.$store.commit('signOut');
          sessionStorage.removeItem("userAuth");
          this.$router.push('/welcome');
        })
        .catch((error) => {
          console.log("Invalid Logout");
        });
    },
  },
  components: {
    sidebar: SideBarComponent,
    topbar: TopBarComponent,
    foot: FooterComponent,
  },
  mounted() {
    if (sessionStorage.getItem("userAuth")) {
      try {
        this.$store.state.user = JSON.parse(sessionStorage.getItem("userAuth"));
      } catch (e) {
        sessionStorage.removeItem("userAuth");
      }
    }
  },
  computed: {
    retUser() {
      return this.$store.state.user;
    },
  },
};
</script>

<style>
#welcome-msg {
  font-size: 60px;
}
</style>