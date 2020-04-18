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
        :auth="auth ? auth.find(item => item.name === stream.name) : null"
        class="mb-6"
        @toggle="toggle"
        @remove="remove"
      />
    </div>

    <h2 class="text-xl font-bold text-gray-400">Basic Auth</h2>
    <CredentialsForm
      :name.sync="basicAuth.name"
      :password.sync="basicAuth.pass"
    />
  </div>
</template>

<script>
import StreamCard from './components/StreamCard.vue'
import CredentialsForm from './components/CredentialsForm.vue'

export default {
  name: 'Streamcheck',
  components: {
    StreamCard,
    CredentialsForm,
  },
  inject: ['config'],
  data () {
    return {
      loading: true,
      error: null,
      status: null,
      auth: null,
      basicAuth: {
        name: '',
        pass: '',
      },
    }
  },
  computed: {
    live () {
      return this.status?.live || false
    },
    streams () {
      return this.status?.streams || []
    },
    authHeader () {
      const authStr = btoa(`${this.basicAuth.name}:${this.basicAuth.pass}`)
      return new Headers({ Authorization: `Basic ${authStr}` })
    },
  },
  watch: {
    basicAuth: {
      deep: true,
      handler (obj) {
        localStorage.setItem('basicAuthName', obj.name)
        localStorage.setItem('basicAuthPass', obj.pass)
      },
    },
  },
  mounted () {
    this.getStatus()
    this.basicAuth = {
      name: localStorage.getItem('basicAuthName') || '',
      pass: localStorage.getItem('basicAuthPass') || '',
    }
  },
  methods: {
    async getStatus () {
      this.loading = true
      this.error = null
      try {
        // const params = { redirect_url: window.location.href }
        const parallelCalls = await Promise.all([
          fetch(this.$apiRoot + 'status'),
          fetch(this.$apiRoot + 'auth'),
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
      this.error = null
      try {
        const response = await fetch(this.$apiRoot + 'status/check')
        this.status = await response.json()
      } catch (e) {
        this.error = e
      }
      this.loading = false
    },
    async toggle (name) {
      this.error = null
      try {
        const response = await fetch(
          this.$apiRoot + `status/toggle?name=${encodeURI(name)}`,
          { method: 'post', headers: this.authHeader },
        )
        if (response.ok) {
          this.status = await response.json()
        } else {
          this.error = response.statusText
        }
      } catch (e) {
        this.error = e
      }
    },
    async remove (name) {
      this.error = null
      try {
        const response = await fetch(
          this.$apiRoot + `status/remove?name=${encodeURI(name)}`,
          { method: 'delete', headers: this.authHeader },
        )
        if (response.ok) {
          this.status = await response.json()
        } else {
          this.error = response.statusText
        }
      } catch (e) {
        this.error = e
      }
    },
  },
}
</script>
