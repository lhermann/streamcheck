<template>
  <div id="app" class="max-w-screen-lg mx-auto px-4 my-12">
    <h1 class="text-center text-3xl font-bold text-gray-700 mb-12">
      Streamcheck
    </h1>

    <div class="text-center mb-6">
      <span
        class="inline-block bg-white font-bold py-2 px-4 rounded mr-4"
        :class="{ 'text-green-500': live, 'text-gray-500': !live }"
      >
        &bull; {{ live ? 'LIVE' : 'OFFLINE' }}
      </span>
      <button
        class="text-white font-bold py-2 px-4 rounded"
        :class="{
          'bg-green-500 hover:bg-green-700': !loading,
          'bg-gray-500 cursor-not-allowed': loading,
        }"
        :disabled="loading"
        @click="check"
      >
        Check
      </button>
    </div>

    <div v-if="loading" class="text-center">
      <div class="spinner"></div>
    </div>

    <div
      v-else-if="error"
      class="bg-white shadow-md rounded border-l-4 border-red-500 p-4"
      role="alert"
    >
      <strong>Error:</strong> {{ error }}
    </div>

    <div v-else>
      <StreamCard
        v-for="stream in streams"
        :key="stream.name"
        :stream="stream"
        :auth="auth.find(item => item.name === stream.name)"
        class="mb-6"
      />
    </div>
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
  data () {
    return {
      loading: true,
      error: null,
      status: null,
      auth: null,
    }
  },
  computed: {
    live () {
      return this.status ? this.status.live : false
    },
    streams () {
      return this.config.map((item, key) => {
        return {
          ...item,
          ...this.status.streams[key],
        }
      })
    },
  },
  async mounted () {
    this.loading = true
    try {
      const params = { redirect_url: window.location.href }
      const parallelCalls = await Promise.all([
        this.$http.get('status'),
        this.$http.get('auth', { params }),
      ])
      console.log({ parallelCalls })
      this.status = parallelCalls[0].body
      this.auth = parallelCalls[1].body
    } catch (e) {
      this.error = e
    }
    this.loading = false
  },
  methods: {
    async check () {
      this.loading = true
      try {
        const { body } = await this.$http.get('check')
        this.status = body
      } catch (e) {
        this.error = e
      }
      this.loading = false
    },
  },
}
</script>
