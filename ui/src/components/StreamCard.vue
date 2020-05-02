<template>
  <div
    class="bg-white shadow-md rounded border-l-4 p-4 flex justify-between"
    :class="{ 'border-green-500': live, 'border-gray-400': !live }"
    role="alert"
  >
    <div>
      <h2 class="text-xl font-bold text-gray-700">{{ stream.name }}</h2>
      <p class="text-sm text-gray-500">
        Status:
        <strong v-if="live" class="text-green-500">LIVE</strong>
        <strong v-else>OFFLINE</strong>
        &middot; Checked: {{ updated }}
      </p>
      <div v-if="auth" class="mt-2">
        <a
          class="bg-transparent hover:bg-green-500 text-green-600 hover:text-white py-1 px-2 border border-gray-400 hover:border-transparent rounded"
          :href="auth.auth_url"
        >
          <span v-if="auth.authenticated">✓ Authenticated</span>
          <strong v-else>Authenticate</strong>
        </a>
      </div>
    </div>
    <div>
      <button
        class="bg-transparent hover:bg-green-500 text-green-600 hover:text-white py-1 px-2 border border-gray-400 hover:border-transparent rounded mr-2"
        @click="$emit('toggle', stream.id)"
      >
        Toggle
      </button>
      <button
        class="bg-transparent hover:bg-red-500 text-gray-500 hover:text-white py-1 px-3 border border-gray-400 hover:border-transparent rounded"
        @click="$emit('remove', stream.id)"
      >×</button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    stream: { type: Object, required: true },
    auth: { type: Object, default: null },
  },
  computed: {
    live () {
      return this.stream.live
    },
    updated () {
      return new Date(this.stream.updated)
    },
  },
}
</script>
