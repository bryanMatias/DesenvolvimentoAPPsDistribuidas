<template>
  <div class="jumbotron">
    <div id="main-message" :class="[isError ? 'text-danger' : 'text-success']" v-if="invalidSignMessages.mainError">
      {{ invalidSignMessages.mainError }}
    </div>
    <h2>Editar Conta</h2>
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
        <label for="oldPassword">Password Atual</label>
        <input
          type="password"
          class="form-control"
          v-model="credentials.oldPassword"
          name="oldPassword"
          id="oldPassword"
        />
        <span
          class="failed-message"
          role="alert"
          v-if="invalidSignMessages.password"
        >
          <strong>{{ invalidSignMessages.password }}</strong>
        </span>
        <label for="inputPassword1">Nova Password</label>
        <input
          type="password"
          class="form-control"
          v-model="credentials.password"
          name="password"
          id="inputPassword1"
        />
        <label for="inputPassword2">Repita a Password</label>
        <input
          type="password"
          class="form-control"
          v-model="credentials.password_confirmation"
          name="password_confirmation"
          id="inputPassword2"
        />
      </div>
      <div class="form-group">
        <input
          class="form-control"
          type="file"
          accept=".jpg,.png"
          name="photo"
          @change="fileChange($event.target.name, $event.target.files)"
        />
      </div>
      <div class="form-group">
        <a class="btn btn-default" v-on:click.prevent="updateProfile">Atualizar</a>
      </div>
    </form>
  </div>
</template>

<script>
import { upload } from "../services/file-upload.service";

export default {
  data: function () {
    return {
      isCustomer: this.$store.state.user.type === 'C',
      credentials: {
        name: this.$store.state.user.name,
        email: this.$store.state.user.email,
        address: this.$store.state.user.address,
        phone: this.$store.state.user.phone,
        nif: this.$store.state.user.nif,
        oldPassword: "",
        password: "",
        password_confirmation: "",
        photo_url: undefined,
      },
      photoFormData: null,
      invalidSignMessages: {
        isError: false,
        mainError: null,
        name: null,
        email: null,
        address: null,
        phone: null,
        nif: null,
        password: null,
      },
    };
  },
  methods: {
    fileChange(fieldName, files) {
      if (!files) return;

      var file = files[0];

      const formData = new FormData();
      formData.append(fieldName, file);
      this.photoFormData = formData;
    },
    async updateProfile() {
      //Post da foto caso exista!
      if (this.photoFormData) {
        await axios
          .post("/api/upload-photo", this.photoFormData, {
            headers: { Accept: "application/json" },
          })
          .then((response) => {
            this.credentials.photo_url = response.data;
            console.log("Url foto: " + response.data);
          })
          .catch((error) => {
            console.log("Falhou upload da foto!");
            console.log(error.response.data);
            return;
          });
      }

      axios
        .put(`/api/user/${this.$store.state.user.id}/update`, this.credentials, {
          headers: { Accept: "application/json" },
        })
        .then((response) => {
          console.dir(response.data);
          //this.$router.push("/welcome");
          this.$store.commit("update", response.data);
          this.isError = false;
          this.invalidSignMessages.mainError = "Atualização bem sucedida!";
          this.invalidSignMessages.name = null;
          this.invalidSignMessages.email = null;
          this.invalidSignMessages.address = null;
          this.invalidSignMessages.phone = null;
          this.invalidSignMessages.nif = null;
          this.invalidSignMessages.password = null;

          this.credentials.oldPassword = "";
          this.credentials.password = "";
          this.credentials.password_confirmation = "";
        })
        .catch((error) => {
          console.log("Invalid UPDATE!");
          console.log(error.response.data);
          this.isError = true;
          this.invalidSignMessages.mainError = "Falha na atualização!";
          this.invalidSignMessages.name = error.response.data.errors.fullName === undefined ? null : error.response.data.errors.fullName[0];
          this.invalidSignMessages.email = error.response.data.errors.email === undefined ? null : error.response.data.errors.email[0];
          this.invalidSignMessages.address = error.response.data.errors.address === undefined ? null : error.response.data.errors.address[0];
          this.invalidSignMessages.phone = error.response.data.errors.phone === undefined ? null : error.response.data.errors.phone[0];
          this.invalidSignMessages.nif = error.response.data.errors.nif === undefined ? null : error.response.data.errors.nif[0];
          this.invalidSignMessages.password = error.response.data.errors.password === undefined ? null : error.response.data.errors.password[0];
        });
    },
  },
};
</script>

<style>
#main-message {
  font-size: 20px;
  font-weight: bold;
}

.secondary-message{
  font-size: 14px;
  font-weight: bold;
}

.success-message{
    color: green;
}

.failed-message {
  color: red;
}
</style>