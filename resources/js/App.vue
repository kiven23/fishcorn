<template>
  <div class="q-pa-md">
    <q-layout view="lHh Lpr lff">
      <q-header elevated class="bg-cyan-8">
        <q-toolbar>
          <q-btn flat @click="drawer = !drawer" round dense icon="menu" />
          <q-toolbar-title> Welcome</q-toolbar-title>

          <div class="text-h4">
            <q-btn dense round flat icon="email">
              <q-badge color="red" floating transparent> 5 </q-badge>
            </q-btn>
            <q-btn class="glossy" color="teal" label="Logout" />
          </div>
        </q-toolbar>
      </q-header>

      <q-drawer v-model="drawer" show-if-above :width="200" :breakpoint="400">
        <q-scroll-area
          style="
            height: calc(100% - 150px);
            margin-top: 150px;
            border-right: 1px solid #ddd;
          "
        >
          <q-list padding>
            <q-item clickable v-ripple to="/homepage">
              <q-item-section avatar>
                <q-icon name="home" />
              </q-item-section>

              <q-item-section> Home </q-item-section>
            </q-item>

            <q-item clickable v-ripple to="/profile">
              <q-item-section avatar>
                <q-icon name="account_box" />
              </q-item-section>

              <q-item-section> Profile </q-item-section>
            </q-item>

            <q-item clickable v-ripple to="/friends">
              <q-item-section avatar>
                <q-icon name="group" />
              </q-item-section>

              <q-item-section> Friends </q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/gallery">
              <q-item-section avatar>
                <q-icon name="photo_library" />
              </q-item-section>

              <q-item-section> Gallery </q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/messenger">
              <q-item-section avatar>
                <q-icon name="send" />
              </q-item-section>

              <q-item-section> Messenger </q-item-section>
            </q-item>
          </q-list>
        </q-scroll-area>

        <q-img
          class="absolute-top"
          src="https://cdn.quasar.dev/img/material.png"
          style="height: 150px"
        >
          <div class="absolute-bottom bg-transparent">
            <q-avatar size="56px" class="q-mb-sm">
              <img :src="profile" />
            </q-avatar>
            <div class="text-weight-bold">{{ username }}</div>
            <div class="text-weight-bold">Age: {{ age }}</div>
            <div>@{{ email }}</div>
          </div>
        </q-img>
      </q-drawer>

      <q-page-container>
        <!-- <div class="q-pa-md row items-start q-gutter-md">
          <div class="example-cell" tabindex="0">
            <q-card  bordered class="bg-white-3  my-card">
                <q-card-section> -->
        <router-view></router-view>
        <!-- </q-card-section>
            </q-card>
          </div>
 
        </div> -->
      </q-page-container>
    </q-layout>
  </div>
</template>
<script>
const axios = require("axios").default;
var getclientId = [];
var notif;
export default {
  data() {
    return {
      drawer: false,
      username: null,
      email: null,
      age: null,
      profile: null,
      id: null,
      conid: null,
      clientcon: [],
      notify: null,
    };
  },

  methods: {
    renderprofile() {
      
       axios.get("http://10.10.10.38:8001/api/chat/conversation/users").then((response)=>{
            
           response.data.filter(function(value, key) {
              
              getclientId.push(value.conversationid)
  
              
           })
       

          })
      axios.get("http://10.10.10.38:8001/api/home").then((response) => {
       
        this.username = response.data.info.username;
        this.email = response.data.email;
        this.age = response.data.info.age;
        this.profile = response.data.info.profile;
        this.id = response.data.id;
       

        this.$options.sockets.request = (res) => {
         console.log(res.data.data.data[0]["conversation_id"])
          getclientId.filter(function(value, key) {
          if(value == res.data.data.data[0]["conversation_id"]){
              if (res.data.data.data[0]["sender_id"] == response.data.id) {
          } else {
            if (
              res.data.data.data[0]["conversation_id"] == value 
 
            ) {
            //alert(res.data.data.data[0]['message_body'])
            }
          }
    
          }else{

          }
           })
        };
      });
          
    },
    checkmessageinbox(){
     
    },
  },

  mounted() {
    this.renderprofile();
    this.checkmessageinbox();
    
  },
};
</script>