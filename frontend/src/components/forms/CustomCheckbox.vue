<template>
  <div class="flex items-center mt-2 gap-x-1">
    <input
      :id="id"
      type="checkbox"
      class=" appearance-none rounded-md checked:bg-blue-600 checked:border-blue-600 border-gray-300 border-2 w-4 h-4 focus:ring-1 focus:ring-offset-1 focus:ring-blue-600"
    />
    <label :for="id" class="text-sm cursor-pointer">{{ label }}</label>
  </div>
</template>
<script setup>
defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: "Im a checkbox",
  },
});
</script>
