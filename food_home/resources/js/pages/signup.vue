<template>
  <div class="jumbotron">
    <div id="failed-main-message" v-if="invalidSignMessages.mainError">
      {{ invalidSignMessages.mainError }}
    </div>
    <h2>Sign Up</h2>
    <form enctype="multipart/form-data">
      <div class="form-group">
        <label for="inputFullName">Nome completo</label>
        <input
          type="text"
          class="form-control"
          v-model="credentials.fullName"
          name="fullName"
          id="inputFullName"
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
      <div class="form-group">
        <label for="inputPassword">Password</label>
        <input
          type="password"
          class="form-control"
          v-model="credentials.password"
          name="password"
          id="inputPassword"
        />
        <span
          class="failed-message"
          role="alert"
          v-if="invalidSignMessages.password"
        >
          <strong>{{ invalidSignMessages.password }}</strong>
        </span>
        <label for="inputPassword2">Repita a password</label>
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
        <a class="btn btn-default" v-on:click.prevent="signup">Criar conta</a>
      </div>
    </form>
  </div>
</template>

<script>
import { upload } from "../services/file-upload.service";

const STATUS_INITIAL = 0,
  STATUS_SAVING = 1,
  STATUS_SUCCESS = 2,
  STATUS_FAILED = 3;

export default {
  data: function () {
    return {
      credentials: {
        fullName: "",
        email: "",
        address: "",
        phone: "",
        nif: "",
        password: "",
        password_confirmation: "",
        photo_url: undefined,
      },
      photoFormData: null,
      invalidSignMessages: {
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
  computed: {
    isInitial() {
      return this.currentStatus === STATUS_INITIAL;
    },
    isSaving() {
      return this.currentStatus === STATUS_SAVING;
    },
    isSuccess() {
      return this.currentStatus === STATUS_SUCCESS;
    },
    isFailed() {
      return this.currentStatus === STATUS_FAILED;
    },
  },
  methods: {
    reset() {
      // reset form to initial state
      this.credentials.currentStatus = STATUS_INITIAL;
      this.credentials.uploadedPhoto = null;
      this.credentials.uploadError = null;
    },
    save(formData) {
      // upload data to the server
      this.credentials.currentStatus = STATUS_SAVING;

      upload(formData)
        .then((x) => {
          this.credentials.uploadedPhoto = x;
          this.credentials.currentStatus = STATUS_SUCCESS;
        })
        .catch((err) => {
          this.credentials.uploadError = err.response;
          this.credentials.currentStatus = STATUS_FAILED;
        });
    },
    fileChange(fieldName, files) {

      if (!files) return;

      var file = files[0];

      const formData = new FormData();
      formData.append(fieldName, file);
      this.photoFormData = formData;

    },
    async signup() {
      //Post da foto caso exista!
      if(this.photoFormData){
        await axios.post("/api/upload-photo", this.photoFormData, { headers: { Accept: "application/json" }, })
          .then((response) => {
            this.credentials.photo_url = response.data;
            console.log("Url foto: " + response.data);
          })
          .catch((error) => {
            console.log("Falhou upload da foto!");
            console.log(error.response.data);
        });
        
      }

      axios.post("/api/signup", this.credentials, { headers: { Accept: "application/json" }, })
        .then((response) => {
          //console.log("User has loggeg in");
          console.dir(response.data);
          this.$router.push("/welcome");
        })
        .catch((error) => {
          console.log("Invalid Sign Up!");
          console.log(error.response.data);
          this.invalidSignMessages.mainError = error.response.data.message;
          this.invalidSignMessages.name = error.response.data.errors.fullName === undefined ? null : error.response.data.errors.fullName[0];
          this.invalidSignMessages.email = error.response.data.errors.email === undefined ? null : error.response.data.errors.email[0];
          this.invalidSignMessages.address = error.response.data.errors.address === undefined ? null : error.response.data.errors.address[0];
          this.invalidSignMessages.phone = error.response.data.errors.phone === undefined ? null : error.response.data.errors.phone[0];
          this.invalidSignMessages.nif = error.response.data.errors.nif === undefined ? null : error.response.data.errors.nif[0];
          this.invalidSignMessages.password = error.response.data.errors.password === undefined ? null : error.response.data.errors.password[0];
      });

    },
    mounted() {
      this.reset();
    },
  },
};
</script>

<style>
#failed-main-message {
  color: red;
  font-size: 20px;
  font-weight: bold;
}

.failed-message {
  color: red;
  font-size: 14px;
  font-weight: bold;
}
</style>