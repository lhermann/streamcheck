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
        class="text-white font-bold py-2 px-4 rounded mr-4"
        :class="{
          'bg-green-500 hover:bg-green-700': !loading,
          'bg-gray-500 cursor-not-allowed': loading,
        }"
        :disabled="loading"
        @click="check"
      >
        Check
      </button>
      <button
        class="text-white font-bold py-2 px-4 rounded"
        :class="{
          'bg-green-500 hover:bg-green-700': !loading,
          'bg-gray-500 cursor-not-allowed': loading,
        }"
        :disabled="loading"
        @click="getStatus"
      >
        Refresh
      </button>
    </div>

    <div
      v-if="error"
      class="bg-white shadow-md rounded border-l-4 border-red-500 p-4 mb-6"
      role="alert"
    >
      <strong>Error:</strong> {{ error }}
    </div>

    <div v-if="loading" class="text-center">
      <div class="spinner"></div>
    </div>

    <div v-else>
      <StreamCard
        v-for="stream in streams"
        :key="stream.name"
        :stream="stream"
        :auth="auth?.find(item => item.name === stream.name)"
        class="mb-6"
        @toggleManual="toggleManual"
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
      return this.status?.live : false
    },
    streams () {
      return this.config.streams.map((item, key) => {
        return {
          ...item,
          ...this.status?.streams[key],
        }
      })
    },
  },
  mounted () {
    this.getStatus()
  },
  methods: {
    async getStatus () {
      this.loading = true
      try {
        // const params = { redirect_url: window.location.href }
        const parallelCalls = await Promise.all([
          fetch('http://localhost:8080/api/v1/status'),
          fetch('http://localhost:8080/api/v1/auth'),
        ])
        this.status = await parallelCalls[0].json()
        this.auth = await parallelCalls[1].json()
      } catch (e) {
        this.error = e
      }
      this.loading = false
    },
    async check () {
      this.loading = true
      try {
        const { body } = await this.$http.get('status/check')
        this.status = body
      } catch (e) {
        this.error = e
      }
      this.loading = false
    },
    async toggleManual () {
      try {
        // const { body } = await this.$http.get('status/toggle-manual')
        // const authStr = btoa('admin:TNUjqCn7hfuJR4PHvW7Fyh7n')
        const { body } = await this.$http.post(
          'status/toggle-manual',
          // { headers: { 'X-TEST': `Basic ${authStr}` } },
          // { headers: { hello: 'world' }, credentials: true },
        )
        this.status = body
      } catch (e) {
        this.error = e
      }
    },
  },
}
</script>
