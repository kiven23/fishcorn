
<template >
  <div class="q-pa-md bg-cyan-5" style="max-width: 350px">
    <q-toolbar class="bg-teal-2 text-white shadow-2">
      <q-toolbar-title>
        <q-chip color="teal" text-color="white">
          <q-avatar>
            <img :src="receiver_profile" />
          </q-avatar>
          {{ receiver_username }}
        </q-chip>
      </q-toolbar-title>
    </q-toolbar>
    <q-scroll-area style="height: 300px; max-width: 300px" ref="chatScroll">
      <infinite-loading
        direction="top"
        @infinite="infiniteHandler"
        :distance="1"
      ></infinite-loading>

      <div v-for="(item, $index) in list" :key="$index">
        <h4>🤓</h4>
        <q-chat-message
          :avatar="userid == item.sender_id ? none : item.profile_picture"
          stamp=""
          :sent="userid == item.sender_id ? true : false"
          :text-color="userid == item.sender_id ? 'white' : false"
          :bg-color="userid == item.sender_id ? 'primary' : 'light-blue-1 '"
        >
          <p>{{ item.message_body }}</p>
          <q-chip dense size="xs" text-color="black">
            <timeago :datetime="item.created" locale="en" />
          </q-chip>
        </q-chat-message>

        <!-- <q-chat-message>
              <q-chip >
                    <q-avatar>
                      <img src="https://cdn.quasar.dev/img/avatar5.jpg">
                    </q-avatar>
                    John
              </q-chip>
               <timeago :datetime="item.created" locale='en'/>  
        </q-chat-message> -->

        <!-- <div :class="userid == item.sender_id ? 'container darker': 'container'">
            <img :src="userid == item.sender_id ? false :  item.profile_picture" :class="userid == item.sender_id ? 'right': 'left'" style="width:100%;">
            <p :style="userid == item.sender_id ? 'color: white; font-family: calibre; max-width:auto;': 'color: black; font-family: calibre; max-width:auto;' ">{{item.message_body}}</p>
            <a>{{userid == item.sender_id ? '': item.name}}</a>
            <span :class="userid == item.sender_id ? 'time-left': 'time-right' "> <timeago :datetime="item.created" locale='en'/>  </span>
          </div> -->
      </div>
      <div v-if="type == 1">
        <q-spinner-dots size="2rem"></q-spinner-dots>Typing
      </div>
      <div v-if="seen == 1">
        <q-chip color="white">seen</q-chip>
      </div>

      <!-- <q-scroll-area style="height: 230px;" reverse>
      <div
        v-infinite-scroll="loadMore"
        infinite-scroll-disabled="busy"
        infinite-scroll-distance="limit"
        direction="top"
      >
        <div style="width: 100%; max-width: 500px" v-for="msg in messages">
          <q-chat-message
            :name="msg.name"
            avatar="https://cdn.quasar.dev/img/avatar3.jpg"
            :text="['hey, how are <sender_id
      </div>

    </q-scroll-area> -->
    </q-scroll-area>
    <q-card-section>
      <q-input
        v-model="message"
        @input="typing()"
        @click="seenclick()"
        filled
        autogrow
      >
        <template v-slot:before>
          <q-avatar>
            <img :src="profile" />
          </q-avatar>
        </template>
        <template>
          <twemoji-picker
            :emojiData="emojiDataAll"
            :emojiGroups="emojiGroups"
            :skinsSelection="false"
            :searchEmojisFeat="true"
            searchEmojiPlaceholder="Search here."
            searchEmojiNotFound="Emojis not found."
            isLoadingLabel="Loading..."
            @emojiUnicodeAdded="onEnterKey"
          >
          </twemoji-picker>
        </template>
        <template v-slot:after>
          <q-btn round dense flat icon="send" @click="send()" />
        </template>
      </q-input>
    </q-card-section>
  </div>
</template>
 
 
<script>
import axios from "axios";
import infiniteScroll from "vue-infinite-scroll";
import Vue from "vue";
import VueTimeago from "vue-timeago";
import { TwemojiPicker } from "@kevinfaguiar/vue-twemoji-picker";
import EmojiAllData from "@kevinfaguiar/vue-twemoji-picker/emoji-data/en/emoji-all-groups.json";
import EmojiDataAnimalsNature from "@kevinfaguiar/vue-twemoji-picker/emoji-data/en/emoji-group-animals-nature.json";
import EmojiDataFoodDrink from "@kevinfaguiar/vue-twemoji-picker/emoji-data/en/emoji-group-food-drink.json";
import EmojiGroups from "@kevinfaguiar/vue-twemoji-picker/emoji-data/emoji-groups.json";
Vue.use(VueTimeago, {
  name: "Timeago", // Component name, `Timeago` by default
  locale: "en", // Default locale
  locales: {
    "zh-CN": require("date-fns/locale/zh_cn"),
  },
});

let api;
Vue.use(infiniteScroll);
export default {
  name: "Home",
  components: {
    "twemoji-picker": TwemojiPicker,
  },
  data() {
    return {
      page: 1,
      list: [],
      message: null,
      userid: null,
      receiver_id: null,
      messages: [],
      busy: false,
      limit: 1,
      token: null,
      profile: null,
      type: null,
      seen: null,
      seenid: null,
      senderid: null,
      receiver_profile: null,
      receiver_username: null,
    };
  },
  sockets: {
    connect: function () {
      console.log("socket connected");
    },
    customEmit: function (val) {
      console.log(
        'this method fired by socket server. eg: io.emit("customEmit", data)'
      );
    },
  },
  computed: {
    emojiDataAll() {
      return EmojiAllData;
    },
    emojiGroups() {
      return EmojiGroups;
    },
  },
  methods: {
    onEnterKey(e) {
       
      
      this.message +=  e
    },

    typing() {
      //this.type = 1;
      this.$socket.emit("typings", [
        { value: 1, token: this.$route.params.token, sender: this.userid },
      ]);
      if (this.message == "") {
        //this.type = 0;
        this.$socket.emit("typings", [
          { value: 0, token: this.$route.params.token, sender: this.userid },
        ]);
      }
    },
    seenclick() {
      this.scroll();

      axios
        .post("http://10.10.10.38:8001/api/chat/conversation/updateseen", {
          data: {
            tokenid: this.$route.params.token,
            senderid: this.userid,
          },
        })
        .then((response) => {
          this.$socket.emit("seen", [
            {
              seenid: response.data[0]["seen"],
              tokenid: response.data[0]["conversation_id"],
              senderid: response.data[0]["sender_id"],
              receiver_id: response.data[0]["receiver_id"],
            },
          ]);
        });
    },
    send() {
      axios
        .post("http://10.10.10.38:8001/api/chat/conversation/chatfunction", {
          data: {
            message_body: this.message,
            sender_id: this.userid,
            receiver_id: this.receiver_id,
            conversation_id: this.$route.params.token,
          },
        })
        .then((res) => {
          this.$socket.emit("request", res);
          this.message = ''
          // console.log(res)
        });
      this.scroll();
    },

    getreceiver() {
      axios
        .get(
          "http://10.10.10.38:8001/api/chat/conversation/receiver/" +
            this.$route.params.token
        )
        .then((response) => {
          this.data = response.data;
          console.log(response);
          this.receiver_username = response.data.username;
          this.receiver_profile = response.data.profile;
        });
    },
    loadMore() {
      console.log("scrolling");

      this.busy = true;
      axios
        .get("https://api.mocki.io/v1/55702f82")
        .then((res) => {
          const append = res.data.slice(
            this.messages.length,
            this.messages.length + this.limit
          );

          this.messages = this.messages.concat(append);

          this.busy = false;
        })
        .catch((err) => {
          this.busy = false;
        });
    },
    infiniteHandler($state) {
      axios
        .get(api, {
          params: {
            page: this.page,
          },
        })
        .then(({ data }) => {
          this.$socket.emit("seen", [
            {
              seenid: data.seen,
              tokenid: data.data.data[0]["conversation_id"],
              senderid: data.sender_id,
            },
          ]);
          this.seenid = data.seen;
          this.senderid = data.sender_id;
          this.token = data.data.data[0]["conversation_id"];
          this.receiver_id = data.receiver_id;
          if (data.data.data.length) {
            this.page += 1;
            this.list.unshift(...data.data.data.reverse());
            $state.loaded();
          } else {
            $state.complete();
          }
        });
    },
    scroll() {
      const scrollArea = this.$refs.chatScroll;
      const scrollTarget = scrollArea.getScrollTarget();
      const duration = 0; // ms - use 0 to instant scroll
      scrollArea.setScrollPosition(scrollTarget.scrollHeight, duration);
    },
  },

  created() {
    this.loadMore();

    axios.get("http://10.10.10.38:8001/api/home").then((response) => {
      this.userid = response.data.id;
      this.profile = response.data.info.profile;
      console.log(response);
    });
  },
  mounted() {
    this.getreceiver();
    api =
      "http://10.10.10.38:8001/api/chat/conversation?token=" +
      this.$route.params.token;
    this.$options.sockets.request = (res) => {
      if (res.data.data.data[0]["conversation_id"] == this.token) {
        this.list.push(res.data.data.data[0]);
        
        if (res.data.data.data[0]["sender_id"] == this.userid) {
          if (res.data.data.data[0]["seen"] == 1) {
            this.seen = 1;
          } else {
            this.seen = 0;
          }
        }
      }
    };
    this.$options.sockets.typings = (res) => {
      if (res[0].token == this.token) {
        // this.list.push(res.data.data.data[0])
        if (res[0].sender !== this.userid) {
          if (res[0].value == 1) {
            this.type = 1;
          } else {
            this.type = 0;
          }
        }
      }
    };
    this.$options.sockets.seen = (res) => {
      if (res[0].tokenid == this.token) {
          if (res[0].sender !== this.userid) {
            if (res[0].seenid == 1) {
              if (res[0].receiver_id !== this.userid) {
                  this.seen = 1;
                }
                  } else {
                  this.seen = 0;
                }
           }
      }
  };
  this.scroll();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1,
h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
body {
  margin: 0 auto;
  max-width: 800px;
  padding: 0 20px;
}

.container {
  border: 2px solid #dedede;
  background-color: #f1f1f1;
  border-radius: 5px;
  padding: 10px;
  margin: 10px 0;
}

.darker {
  border-color: rgb(190, 168, 168);
  background-color: #eb34a1;
}

.container::after {
  content: "";
  clear: both;
  display: table;
}

.container img {
  float: left;
  max-width: 60px;
  width: 100%;
  margin-right: 20px;
  border-radius: 50%;
}

.container img.right {
  float: right;
  margin-left: 20px;
  margin-right: 0;
}

.time-right {
  float: right;
  color: #aaa;
}

.time-left {
  float: left;
  color: #999;
}
p {
  display: block; /* or inline-block, at least its a block element */
  width: auto; /* or width is certain by parent element */
  height: auto; /* height cannot be defined */
  word-break: break-all; /*  */
  word-wrap: break-word; /* if you want to cut the complete word */
  white-space: normal; /* be sure its not 'nowrap'! ! ! :/ */
}
</style>
