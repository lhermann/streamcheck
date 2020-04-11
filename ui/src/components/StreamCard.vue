<template>
  <div class="bg-white shadow-md rounded border-l-4 border-green-500 p-4" role="alert">
    <h2 class="text-xl font-bold text-gray-700">{{ config.name }}</h2>
    <p class="text-sm text-gray-500">
      Status: <span class="text-green-500 font-bold">LIVE</span>
      &middot; Checked: 2020-03-23 6:20 AM
    </p>
  </div>
</template>

<script>
export default {
  props: {
    config: { type: Object, required: true },
  },
}
</script>
