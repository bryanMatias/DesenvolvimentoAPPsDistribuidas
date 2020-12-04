<template>
  <div class="jumbotron">
    <div id="failed-message" v-if="invalidAuthMessage">{{ invalidAuthMessage }}</div>
    <h2>Login</h2>
    <div class="form-group">
      <label for="inputEmail">Email</label>
      <input
        type="email"
        class="form-control"
        v-model="credentials.email"
        name="email"
        id="inputEmail"
        placeholder="Email address"
      />
    </div>
    <div class="form-group">
      <label for="inputPassword">Password</label>
      <input
        type="password"
        class="form-control"
        v-model="credentials.password"
        name="password"
        id="inputPassword"
      />
    </div>
    <div class="form-group">
      <a class="btn btn-default" v-on:click.prevent="login">Login</a>
    </div>
  </div>
</template>

<script>
export default {
  data: function () {
    return {
      credentials: {
        email: "",
        password: "",
      },
      invalidAuthMessage: null,
    };
  },
  methods: {
    login() {
      axios.get("/sanctum/csrf-cookie").then((response) => {
        axios
          .post("/api/login", this.credentials)
          .then((response) => {
            //console.log("User has loggeg in");
            console.dir(response.data);
            this.$store.commit('signIn', response.data);
            sessionStorage.setItem('userAuth', JSON.stringify(this.$store.state.user));
            this.$router.push('/welcome');
          })
          .catch((error) => {
            this.invalidAuthMessage = error.response.data.message;
            this.credentials.email = "";
            this.credentials.password = "";
          });
      });
    },
  },
};
</script>

<style>
#failed-message{
  color: red;
  font-size: 20px;
  font-weight: bold;
}
</style>