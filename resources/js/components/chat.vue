 
<template>
  <div class="q-pa-md" style="max-width: 350px">
    <q-toolbar class="bg-primary text-white shadow-2">
      <q-toolbar-title>Contacts</q-toolbar-title>
    </q-toolbar>
<q-scroll-area style="height: 200px">
    <q-list bordered v-for="contact in contacts" :key="contact.creatorid">
      <q-item class="q-my-sm" clickable v-ripple  @click="createconversation(contact.creatorid)">
       
        <q-item-section avatar>
          <q-avatar color="primary" text-color="white">
            <img :src="contact.profilepicture" />
          </q-avatar>
        </q-item-section>

        <q-item-section>
          <q-item-label>{{ contact.firstname }} {{ contact.lastname }}</q-item-label>
          <q-item-label caption lines="1">{{ contact.mail }}</q-item-label>
        </q-item-section>

        <q-item-section side>
          <q-icon name="chat_bubble" color="green" />
        </q-item-section>
      </q-item>

  
    </q-list>
  </q-scroll-area>
  </div>
</template>
<script>
const axios = require('axios').default;
export default {
  data() {
    return {
      filter: null,
      contacts: [],
    };
  },
  methods: {
     getuser(){
       axios.get('http://10.10.10.38:8001/api/chat').then((response)=>{
        this.contacts = response.data
        console.log(response.data)
       })
     },
     createconversation(id){
      axios.post('http://10.10.10.38:8001/api/chat/conversation/create',{
        data:{
          id: id
        }
      }).then(
        (res)=>{
          console.log(res.data.token);
            this.$router.push({name: 'chat', params: { token: res.data.token }});
      },(error)=>{
         console.log(error)
      })
      
     }
  },
  mounted(){
    this.getuser();
 
  }
};
</script>