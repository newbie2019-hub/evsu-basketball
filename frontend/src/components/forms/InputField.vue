<template>
  <div class="relative">
    <mdicon v-if="icon" :size="22" :name="icon" class="absolute left-3 top-3.5"/> 
    <input
      :id="id"
      :type="type"
      :value="$attrs.modelValue"
      @input="emit('update:modelValue', $event.target.value)"
      placeholder=" "
      class="block text-sm placeholder-transparent w-full peer p-3 border-2 border-gray-300 rounded-md mt-3 focus:outline-none focus:border-blue-600"
      :class="{'pl-10' : icon }"
    />
    <label
      :for="id"
      class="absolute text-gray-400 bg-white top-0 px-1 text-xs duration-300 transform -translate-y-2 origin-[0] peer-focus:top-0 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-placeholder-shown:text-sm peer-focus:text-blue-600 peer-focus:text-xs"
      :class="[{'left-2.5' : !icon}, {'left-2.5 peer-placeholder-shown:left-9 peer-focus:left-2.5' : icon}]"
      >{{ label }}</label
    >
  </div>
  <p v-if="error" :id="`error-${id}`" class="text-xs ml-1 text-red-600">Error message goes here.</p>
</template>
<script setup>
const props = defineProps({
  type: {
    type: String,
    default: "text",
  },
  label: {
    type: String,
    default: "Input Placeholder",
  },
  id: {
    type: String,
    required: true,
  },
  error: {
    type: Boolean,
    default: false
  },
  icon: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue'])
</script>
<style>
span.mdi svg {
  fill: rgb(99, 99, 99);
}
</style>