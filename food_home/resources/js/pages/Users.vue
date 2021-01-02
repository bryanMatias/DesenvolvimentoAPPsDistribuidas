<template>
  <div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">All users</h1>

    <!-- Basic Card Example -->
    <div class="jumbotron" v-if="userEdited">
      <div
        id="main-message"
        :class="[isError ? 'text-danger' : 'text-success']"
        v-if="invalidSignMessages.mainError"
      >
        {{ invalidSignMessages.mainError }}
      </div>
      <h2>Editar Utilizador</h2>
      <form enctype="multipart/form-data">
        <div class="form-group">
          <label for="inputName">Nome completo</label>
          <input
            type="text"
            class="form-control"
            v-model="credentials.name"
            name="name"
            id="inputName"
            placeholder="Nome completo"
          />
          <span
            class="failed-message"
            role="alert"
            v-if="invalidSignMessages.name"
          >
            <strong>{{ invalidSignMessages.name }}</strong>
          </span>
        </div>
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
          <span
            class="failed-message"
            role="alert"
            v-if="invalidSignMessages.email"
          >
            <strong>{{ invalidSignMessages.email }}</strong>
          </span>
        </div>
        <div v-if="isCustomer">
          <div class="form-group">
            <label for="inputAddress">Morada</label>
            <input
              type="text"
              class="form-control"
              v-model="credentials.address"
              name="address"
              id="inputAddress"
            />
            <span
              class="failed-message"
              role="alert"
              v-if="invalidSignMessages.address"
            >
              <strong>{{ invalidSignMessages.address }}</strong>
            </span>
          </div>
          <div class="form-group">
            <label for="inputPhone">Telefone</label>
            <input
              type="text"
              class="form-control"
              v-model="credentials.phone"
              name="phone"
              id="inputPhone"
              placeholder="Número de telefone"
            />
            <span
              class="failed-message"
              role="alert"
              v-if="invalidSignMessages.phone"
            >
              <strong>{{ invalidSignMessages.phone }}</strong>
            </span>
          </div>
          <div class="form-group">
            <label for="inputNif">NIF</label>
            <input
              type="text"
              class="form-control"
              v-model="credentials.nif"
              name="nif"
              id="inputNif"
            />
            <span
              class="failed-message"
              role="alert"
              v-if="invalidSignMessages.nif"
            >
              <strong>{{ invalidSignMessages.nif }}</strong>
            </span>
          </div>
        </div>
        <div class="form-group">
          <a class="btn btn-default" v-on:click.prevent="updateUser"
            >Atualizar</a
          >
        </div>
      </form>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
        <button v-on:click="showAllUsers()">All</button>
        <button v-on:click="filterCustomers()">Clients</button>
        <button v-on:click="filterCookers()">Cookers</button>
        <button v-on:click="filterDeliveryMans()">DeliveryMans</button>
        <button v-on:click="filterManagers()">Managers</button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table
            class="table table-bordered"
            id="dataTable"
            width="100%"
            cellspacing="0"
          >
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>e-mail</th>
                <th>Type</th>
                <th>Blocked?</th>
                <th>Photo</th>
                <th>Gerir</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in displayedUsers" v-bind:key="user.id">
                <td>{{ user.id }}</td>
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.type }}</td>
                <td>{{ user.blocked }}</td>
                <td>
                  <img
                    class="img-profile rounded-circle avatar font-weight-bold"
                    :src="'/storage/fotos/' + user.photo_url"
                  />
                </td>
                <td>
                  <div v-if="user.id != $store.state.user.id">
                    <a class="btn btn-success btn-circle btn-sm" v-on:click.prevent="editUser(user)" title="Editar"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-danger btn-circle btn-sm" v-on:click.prevent="destroyUser(user)" title="Eliminar"><i class="far fa-times-circle"></i></a>
                    <a class="btn btn-danger btn-circle btn-sm" v-on:click.prevent="flipBlockState(user)" title="Bloquear"><i class="fas fa-user-circle"></i></a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: function () {
    return {
      allUsers: undefined,
      displayedUsers: undefined,
      userEdited: null,
      isCustomer: false,
      credentials: {
        id: null,
        name: "",
        email: "",
        address: "",
        phone: "",
        nif: "",
        password: null,
      },
      invalidSignMessages: {
        isError: false,
        mainError: null,
        name: null,
        email: null,
        address: null,
        phone: null,
        nif: null,
      },
    };
  },
  methods: {
    editUser: function(user){
      this.credentials.id = user.id;
      this.credentials.name = user.name;
      this.credentials.email = user.email;

      if(user.type === 'C'){
        this.credentials.address = user.address;
        this.credentials.phone = user.phone;
        this.credentials.nif = user.nif;
        this.isCustomer = true;
      }

      this.userEdited = user;
    },
    updateUser: function (){
      this.isError = false;
      this.invalidSignMessages.mainError = "";
      this.invalidSignMessages.name = null;
      this.invalidSignMessages.email = null;
      this.invalidSignMessages.address = null;
      this.invalidSignMessages.phone = null;
      this.invalidSignMessages.nif = null;

      axios.put(`/api/user/${this.credentials.id}/update`, this.credentials, {
        headers: { Accept: "application/json" },
      })
      .then((response) => {
        this.isError = false;
        this.invalidSignMessages.mainError = "Atualização bem sucedida!";
        this.invalidSignMessages.name = null;
        this.invalidSignMessages.email = null;
        this.invalidSignMessages.address = null;
        this.invalidSignMessages.phone = null;
        this.invalidSignMessages.nif = null;

        this.userEdited.name = this.credentials.name;
        this.userEdited.email = this.credentials.email;
        
        if(this.userEdited.type === 'C'){
          this.userEdited.address = this.credentials.address;
          this.userEdited.phone = this.credentials.phone;
          this.userEdited.nif = this.credentials.nif;
        }

        this.credentials.id = null;
        this.credentials.name = "";
        this.credentials.email = "";
        this.credentials.address = "";
        this.credentials.phone = "";
        this.credentials.nif = "";
        this.isCustomer = false;
        
        this.userEdited = null;
      })
      .catch((error) => {
        this.isError = true;
        this.invalidSignMessages.mainError = "Falha na atualização!";
        this.invalidSignMessages.name = error.response.data.errors.fullName === undefined ? null : error.response.data.errors.fullName[0];
        this.invalidSignMessages.email = error.response.data.errors.email === undefined ? null : error.response.data.errors.email[0];
        this.invalidSignMessages.address = error.response.data.errors.address === undefined ? null : error.response.data.errors.address[0];
        this.invalidSignMessages.phone = error.response.data.errors.phone === undefined ? null : error.response.data.errors.phone[0];
        this.invalidSignMessages.nif = error.response.data.errors.nif === undefined ? null : error.response.data.errors.nif[0];
        });
    },
    destroyUser: function(user){
      axios.delete(`/api/users/${user.id}`)
      .then((response) => {
        console.log(`User ${user.name} deleted!`);
        let index = this.allUsers.findIndex(x => x.id === user.id);
        this.allUsers.splice(index, 1);
      })
      .catch((error) => {
        console.log("Failed at delete user!");
      });
    },
    flipBlockState: function(user){
      axios.put(`/api/users/${user.id}/flip-block`)
      .then((response) => {
        console.log(`User ${user.name} block state changed!`);
        if(user.blocked == 0){
          user.blocked = 1;
          this.$socket.emit("blocked_user", user);
        } else {
          user.blocked = 0;
        }
      })
      .catch((error) => {
        console.log("Failed at fliping block state!");
      });
    },
    showAllUsers: function () {
      this.displayedUsers = this.allUsers;
    },
    filterCustomers: function () {
      this.displayedUsers = this.allUsers.filter((user) => {
        return user.type == "C";
      });
    },
    filterCookers: function () {
      this.displayedUsers = this.allUsers.filter((user) => {
        return user.type == "EC";
      });
    },
    filterDeliveryMans: function () {
      this.displayedUsers = this.allUsers.filter((user) => {
        return user.type == "ED";
      });
    },
    filterManagers: function () {
      this.displayedUsers = this.allUsers.filter((user) => {
        return user.type == "EM";
      });
    },
    getUsers: function () {
      axios.get("api/users").then((response) => {
        this.allUsers = response.data;
        this.displayedUsers = response.data;
      });
    },
  },
  mounted() {
    this.getUsers();
  },
};
</script>

<style>
</style>