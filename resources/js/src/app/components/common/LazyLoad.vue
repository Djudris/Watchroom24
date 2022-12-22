<template>
  <div v-if="enabled && isLoaded">
    <div v-if="isLoaded"><slot></slot></div>
    <div v-else class="component-loading"></div>
  </div>
</template>

<script>
export default {
  name: "lazy-load",

  props: {
    component: String
  },

  computed: {
    isLoaded() {
      return !!this.$store.state.lazyComponent.components[this.component];
    }
  },

  data() {
    return {
      enabled: false
    }
  },

  mounted() {
    this.enabled = true;
  }
}
</script>
