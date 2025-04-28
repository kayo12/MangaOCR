<script setup>
import { ref } from 'vue'

defineProps({
  name: String
})

const Link1 = ref(null)

</script>

<template>
  <div>
    <h1>Hello </h1> 
      <button type="button" @click="scan"> SCANNER </button>
      <p>
        Edit
        <code>components/HelloWorld.vue</code> to test HMR
      </p>
    <p>
      Check out
      <a  ref="Link1" href="https://vuejs.org/guide/quick-start.html#local" target="_blank"
        >create-vue</a
      >, the official Vue + Vite starter
    </p>
    <p>
      Learn more about IDE Support for Vue in th </p>
  </div>
</template>

<style scoped>
  .read-the-docs {
    color: #888;
  }
</style>
