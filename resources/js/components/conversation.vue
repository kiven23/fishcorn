<template>
  <div class="q-pa-md row items-start q-gutter-md">
    <div class="example-cell col-11" tabindex="0">
      <q-table
        title="Inbox"
        dense
        :filter="filter"
        :data="data"
        :columns="columns"
         
      >
      <q-chip square>
        <q-avatar>
          <img src="https://cdn.quasar.dev/img/boy-avatar.png">
        </q-avatar>
        John
      </q-chip>
        <template v-slot:body-cell-Actions="props">
            
          <router-link :to="{ name: 'chat', params: { token: props.row.conversationid } }">
            <div class="q-pa-md q-gutter-sm">
              <q-btn color="red" icon="chat"  @click="createconversation1(props.row.conversationid)" />
            </div>
          </router-link>
        </template>

        <template v-slot:top-right>
          <q-input
            borderless
            dense
            debounce="300"
            v-model="filter"
            placeholder="Search"
          >
            <q-icon slot="append" name="search" />
          </q-input>
        </template>
      </q-table>
    </div>
      <div class="example-cell" tabindex="0">
        <q-card bordered class="bg-white-3 my-card">
            <router-view name="chats"></router-view>  
        </q-card>
      </div>
  </div>
</template>

<script>
const axios = require('axios').default;
export default {
  data() {
    return {
      filter: null,
      columns: [
        {
          name: "UserName",
          label: "UserName",
          field: "receivername",
          align: "left",
          sortable: true,
        },
         
        {
          name: "Actions",
          label: "Action",
          field: "actions",
          align: "left",
        },
      ],
      data: [],
    };
  },
  methods: {
     getuser(){
       axios.get('http://10.10.10.38:8001/api/chat/conversation/users').then((response)=>{
        this.data = response.data
        console.log(response.data)
       })
     },
   
 
  },
  mounted(){
    this.getuser();
    
  }
};
</script>