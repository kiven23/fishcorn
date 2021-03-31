<template>
  <div class="row">
    <div class="example-cell" tabindex="0">
      <div class="text-weight-bold">Profile</div>
      <q-card dark bordered class="bg-blue-3 my-card">
        <div class="q-gutter-md row items-start">
          <q-img
            v-for="urls in url"
            :key="urls.profile_picture"
            :src="urls.profile_picture"
            style="width: 150px"
            ratio="1"
            spinner-color="white"
            class="rounded-borders"
            v-if="urls.profile_picture"
          >
            <div class="absolute-bottom text-center text-italic text-body2">
              None
            </div>
          </q-img>
        </div>
      </q-card>
    </div>

    <div class="example-cell" tabindex="0">
      <div class="text-weight-bold">Gallery</div>
      <q-card dark bordered class="bg-blue-3 my-card">
        <div class="q-gutter-md row items-start">
          <!-- notice "basic" prop (which disables default animation) -->
          <q-img
            v-for="urls in url"
            :key="urls.gallery"
            :src="urls.gallery"
            style="width: 150px"
            ratio="1"
            spinner-color="white"
            class="rounded-borders"
            v-if="urls.gallery"
          >
            <div class="absolute-bottom text-center text-italic text-body2">
              None
            </div>
          </q-img>
        </div>
      </q-card>
    </div>
  </div>
</template>

<script>
const axios = require("axios");
export default {
  data() {
    return {
      url: null,
    };
  },
  mounted() {
    axios.get("http://10.10.10.38:8001/api/gallery").then((response) => {
      this.url = response.data;
      
    });
  },
};
</script>