<template>
  <div id="app" class="max-w-screen-lg mx-auto px-4 my-12">
    <h1 class="text-center text-3xl font-bold text-gray-700 mb-12">
      Streamcheck
    </h1>

    <StreamCard
      v-for="stream in config"
      :key="stream.name"
      :config="stream"
      class="mb-6"
    />
  </div>
</template>

<script>
import StreamCard from './components/StreamCard.vue'

export default {
  name: 'Streamcheck',
  components: {
    StreamCard,
  },
  inject: ['config'],
}
</script>
