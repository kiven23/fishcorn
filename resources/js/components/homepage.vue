<template>
  <div class="q-pa-md">
    <div class="row items-start example-container">
      <div class="example-cell col-12" tabindex="0">
        <div class="q-pa-md q-gutter-sm">
          <q-editor v-model="editor" min-height="5rem" />

          <q-card flat bordered>
            <q-card-section>
              <pre style="white-space: pre-line"></pre>
            </q-card-section>
          </q-card>

          <q-card flat bordered>
            <q-card-section v-html="editor" />
          </q-card>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      editor: "What you see is <b>what</b> you get.",
    };
  },
  mounted() {
    console.log("Component mounted.");
  },
};
</script>
