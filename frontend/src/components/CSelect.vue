<script setup>
  const emit = defineEmits(['update:modelValue']);

  const updateValue = (event) => {
    emit('update:modelValue', event.target.value);
  };
</script>
<template>
  <select
    :value="$attrs.modelValue"
    @input="updateValue"
    class="peer ring-0 outline-none border-2 p-3 placeholder-transparent rounded-md text-sm w-full border-gray-400 dark:border-gray-400 text-gray-900 bg-transparent focus:border-blue-500"
    placeholder="Input Field"
  >
    <slot />
  </select>
</template>
