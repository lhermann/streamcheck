<template>
  <div class="flex flex-wrap -mx-3 mb-6">
    <!-- <div class="w-1/3 px-3">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="form-name">
        Name
      </label>
      <input
        id="form-name"
        type="text"
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        v-model="internalName"
      >
    </div> -->
    <div class="w-full px-3">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="form-pass">
        Password
      </label>
      <input
        id="form-pass"
        type="text"
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        v-model="internalPassword"
      >
    </div>
  </div>
</template>

<script>
export default {
  props: {
    name: { type: String, default: '' },
    password: { type: String, default: '' },
  },
  computed: {
    internalName: {
      get () {
        return this.name
      },
      set (value) {
        this.$emit('update:name', value)
      },
    },
    internalPassword: {
      get () {
        return this.password
      },
      set (value) {
        this.$emit('update:password', value)
      },
    },
  },
}
</script>
