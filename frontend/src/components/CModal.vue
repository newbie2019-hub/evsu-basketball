<template>
  <Teleport to="body">
    <div class="inset-0 fixed overflow-y-auto flex justify-center items-center z-50" v-bind="$attrs">
      <div @click.prevent="emitClose" class="fixed inset-0 w-full flex min-h-screen justify-center items-center bg-gray-700/50 z-40"></div>
      <div class="flex w-full px-4 sm:px-0">
        <Transition>
          <div class="mx-auto min-w-full sm:min-w-[368px] md:max-w-[428px] bg-white box-shadow-lg rounded-lg min-w-2xl z-40 inset-0">
            <div class="px-5 pt-5 pb-3">
              <slot name="title" />
            </div>
            <div class="px-5 pb-2">
              <slot name="body" />
            </div>
            <div class="flex justify-end px-5 py-4">
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
  const emit = defineEmits(['close']);

  const props = defineProps({
    persistent: {
      type: Boolean,
      value: false,
    },
  });

  const emitClose = () => {
    if (props.persistent) return;
    emit('close');
  };
</script>


<style>
  .v-enter-active,
  .v-leave-active {
    transition: opacity 0.5s ease;
  }

  .v-enter-from,
  .v-leave-to {
    opacity: 0;
  }
</style>
