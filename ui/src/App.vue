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
        @click="getStreams"
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
        v-for="(stream, i) in streams"
        :key="stream.id"
        :stream="stream"
        :auth="auth ? auth.find(item => item.id === stream.id) : null"
        class="mb-6"
        @toggle="toggle(i, $event)"
        @remove="remove(i, $event)"
      />
    </div>

    <h2 class="text-xl font-bold text-gray-400">Auth</h2>
    <CredentialsForm
      :password.sync="password"
    />
  </div>
</template>

<script>
import StreamCard from './components/StreamCard.vue'
import CredentialsForm from './components/CredentialsForm.vue'

export default {
  name: 'App',
  components: {
    StreamCard,
    CredentialsForm,
  },
  data () {
    return {
      loading: true,
      error: null,
      streams: [],
      status: null,
      auth: [],
      password: '',
      // basicAuth: {
      //   name: '',
      //   pass: '',
      // },
    }
  },
  computed: {
    live () {
      return this.status?.live || false
    },
    authHeader () {
      // const authStr = btoa(`${this.basicAuth.name}:${this.basicAuth.pass}`)
      // return new Headers({ Authorization: `Basic ${authStr}` })
      return new Headers({ 'App-Password': this.password })
    },
  },
  watch: {
    password (password) {
      localStorage.setItem('password', password)
    },
    // basicAuth: {
    //   deep: true,
    //   handler (obj) {
    //     localStorage.setItem('basicAuthName', obj.name)
    //     localStorage.setItem('basicAuthPass', obj.pass)
    //   },
    // },
  },
  mounted () {
    this.getStreams()
    this.password = localStorage.getItem('password') || ''
    // this.basicAuth = {
    //   name: localStorage.getItem('basicAuthName') || '',
    //   pass: localStorage.getItem('basicAuthPass') || '',
    // }
  },
  methods: {
    async getStreams () {
      this.loading = true
      this.error = null
      try {
        // const params = { redirect_url: window.location.href }
        const parallelCalls = await Promise.all([
          fetch(this.$apiRoot + 'streams'),
          fetch(this.$apiRoot + 'status'),
          fetch(this.$apiRoot + 'auth'),
        ])
        this.streams = await parallelCalls[0].json()
        this.status = await parallelCalls[1].json()
        this.auth = await parallelCalls[2].json()
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
    async toggle (index, id) {
      this.error = null
      try {
        const response = await fetch(
          this.$apiRoot + `status/toggle/${encodeURI(id)}`,
          { method: 'post', headers: this.authHeader },
        )
        if (response.ok) {
          const obj = await response.json()
          this.streams[index].live = obj.live
          this.streams[index].updated = obj.updated
        } else {
          // eslint-disable-next-line
          console.log(response)
          this.error = response.statusText || response.status
        }
      } catch (e) {
        this.error = e
      }
    },
    async remove (index, id) {
      this.error = null
      try {
        const response = await fetch(
          this.$apiRoot + `status/remove/${encodeURI(id)}`,
          { method: 'delete', headers: this.authHeader },
        )
        if (response.ok) {
          this.streams.splice(index, 1)
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
