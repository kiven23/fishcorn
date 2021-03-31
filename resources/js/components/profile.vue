<template>
  <div class="row">
    <div class="example-cell" tabindex="0">
      <div class="q-pa-md" style="max-width: 400px">
        <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
          <q-input
            filled
            v-model="firstname"
            label="First Name *"
            hint="First Name"
            lazy-rules
            dense
            :rules="[
              (val) => (val && val.length > 0) || 'Please type something',
            ]"
          />
          <q-input
            filled
            v-model="lastname"
            label="Last Name"
            hint="LastName"
            lazy-rules
            dense
            :rules="[
              (val) => (val && val.length > 0) || 'Please type something',
            ]"
          />
          <q-input
            filled
            type="number"
            v-model="age"
            label="Your age *"
            lazy-rules
            dense
            :rules="[
              (val) => (val !== null && val !== '') || 'Please type your age',
              (val) => (val > 0 && val < 100) || 'Please type a real age',
            ]"
          />

          <div>
            <q-btn label="Submit" type="submit" color="primary" />
            <q-btn
              label="Reset"
              type="reset"
              color="prionRejected (rejectedEntries) {
                // Notify plugin needs to be installed
                // https://quasar.dev/quasar-plugins/notify#Installation
                this.$q.notify({
                  type: 'negative',
                  message: `${rejectedEntries.length} file(s) did not pass validation constraints`
                })
          },mary"
              flat
              dense
              class="q-ml-sm"
            />
          </div>
        </q-form>
      </div>
    </div>
    <div class="example-cell" tabindex="0">
      <div class="q-pa-md" style="max-width: 400px">
        <q-input
          filled
          v-model="email"
          label="Email"
          dense
          hint="Email"
          lazy-rules
          :rules="[(val) => (val && val.length > 0) || 'Please type something']"
        />
        <q-uploader
          style="max-width: 300px"
          label="Main Image"
          :factory="uploadFile"
          max-files="1"
          accept=".jpg, image/*"
          @rejected="onRejected"
        />
        <br />
      </div>
    </div>
  </div>
</template>

<script>
 
 var tok;
const axios = require("axios").default;
var Promise = require("promise");
var FormData = require("form-data");
 
export default {
 
  data() {
    return {
      lastname: null,
      firstname: null,
      age: null,
      accept: false,
      uploadPercentage: 0,
      token: null,
      email: null,
      file_path : null,
    
    };
  },

  methods: {
    uploadFile(files) {
      
   
      this.file_path = files[0]
      const fileData = new FormData()
       fileData.append('_token', tok)
       fileData.append('file_path', this.file_path)
       //Replace http://localhost:8000 with your API URL
       return new Promise((resolve, reject) => {     
       const uploadFile = axios.post('http://10.10.10.38:8001/uploadss', fileData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        }
      }).then((response) => {
    
        this.$router.go()
        resolve(files)
        console.log(response.data);    
        
        
       // Notify plugin needs to be installed
       // https://quasar.dev/quasar-plugins/notify#Installation
        this.$q.notify({
          type: 'possitive',
          message: `Image Uploaded`
        })
      });
       })
 },
 onRejected (rejectedEntries) {
      // Notify plugin needs to be installed
      // https://quasar.dev/quasar-plugins/notify#Installation
      this.$q.notify({
        type: 'negative',
        message: `${rejectedEntries.length} file(s) did not pass validation constraints`
      })
      },
      onSubmit() {
 

          return new Promise((resolve,reject)=>{
             axios.post('http://10.10.10.38:8001/createprofile',{
               email: this.email,
               firstname: this.firstname,
               lastname: this.lastname,
               age: this.age
             }).then((response)=>{
                  resolve(response.data);
                  console.log(response.data)
             })
          })
       console.log(s);
        this.$q.notify({
          color: "green-4",
          textColor: "white",
          icon: "cloud_done",
          message: "Submitted",
        });
    },
    onReset() {
      this.lastname = null;
      this.firstname = null;
      this.email = null;
      this.age = null;
    },
  },
 
  mounted() {
      tok = document
      .querySelector('meta[name = "csrf-token"]')
      .getAttribute("content");
           
        axios.get('http://10.10.10.38:8001/api/home').then((response)=>{
        this.firstname = response.data.info.firstname;
        this.lastname = response.data.info.lastname;
        this.email = response.data.info.mail;
        this.age = response.data.info.age;
        this.profile = response.data.info.profile;
          });
     
  },
};
</script>