<template>
  <div class="chart full-width">
    <apexchart
      type="line"
      height="400"
      :options="options"
      :series="series"
    ></apexchart>
  </div>
</template>
<script setup>
const props = defineProps({
  options: {
    type: [Array, Object],
  },
  series: {
    type: [Array, Object],
  },
});
</script>
