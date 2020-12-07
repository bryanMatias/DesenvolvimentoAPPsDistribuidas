<template>
  <div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">All Orders</h1>

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
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in displayedUsers" v-bind:key="user.id">
                <td>{{ user.id }}</td>
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.type }}</td>
                <td>{{ user.blocked }}</td>
                <td>[PHOTO]</td>
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
    };
  },
  methods: {
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