<template>
  <div>
    <vue-cal
      :time="false"
      active-view="month"
      class="vuecal--blue-theme"
      :events="events"
      :disable-views="['years', 'year']"
      :on-event-click="onEventClick"
    />
  </div>
</template>
<script setup>
import VueCal from "vue-cal";
import "vue-cal/dist/vuecal.css";
import moment from "moment";
import { computed } from "vue";

const emits = defineEmits(["emitEvent"]);

const props = defineProps({
  data: {
    type: [Object, Array],
    value: [],
  },
});

const onEventClick = (event, e) => {
  emits("emitEvent", event);
  e.stopPropagation();
};

const events = computed(() => {
  let sched = [];

  props.data.map((event) => {
    const start = moment(event.schedule).format("YYYY-MM-DD HH:mm");
    sched.push({
      title: event.name,
      content: `<p style="font-size: .8rem">${event.description}<p>`,
      start: start,
      end: start,
    });
  });

  return sched;
});

</script>
