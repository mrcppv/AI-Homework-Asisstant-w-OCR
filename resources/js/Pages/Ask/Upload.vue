<template>

  <div id="accordion-collapse" data-accordion="collapse">
    <h2 id="accordion-collapse-heading-1">
      <button type="button"
        class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
        data-accordion-target="#accordion-collapse-body-1" aria-expanded="true"
        aria-controls="accordion-collapse-body-1">
        <span>Add Image or File</span>
        <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5 5 1 1 5" />
        </svg>
      </button>
    </h2>
    <div id="accordion-collapse-body-1" class="hidden" aria-labelledby="accordion-collapse-heading-1">
      <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">
        <p class="mb-2 text-gray-500 dark:text-gray-400">Flowbite is an open-source library of interactive components
          built on top of Tailwind CSS including buttons, dropdowns, modals, navbars, and more.</p>
        <file-input v-model="form.photo" :error="form.errors.photo" class="pb-8 pr-6 w-full lg:w-1/2" type="file"
          accept="image/*" label="Photo" />
      </div>
    </div>
    <h2 id="accordion-collapse-heading-2">
      <button type="button"
        class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
        data-accordion-target="#accordion-collapse-body-2" aria-expanded="false"
        aria-controls="accordion-collapse-body-2">
        <span>Show Advanced Options</span>
        <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5 5 1 1 5" />
        </svg>
      </button>
    </h2>
    <div id="accordion-collapse-body-2" class="hidden" aria-labelledby="accordion-collapse-heading-2">
      <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700">
        <p class="mb-2 text-gray-500 dark:text-gray-400">Flowbite is first conceptualized and designed using the Figma
          software so everything you see in the library has a design equivalent in our Figma file.</p>
        <p class="text-gray-500 dark:text-gray-400">Check out the <a href="https://flowbite.com/figma/"
            class="text-blue-600 dark:text-blue-500 hover:underline">Figma design system</a> based on the utility
          classes from Tailwind CSS and components from Flowbite.</p>
      </div>
    </div>

  </div>


</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { initFlowbite } from 'flowbite'
import FileInput from '@/Shared/FileInput.vue'



const photo = ref(null);
const form = ref({ errors: { photo: null } });

watch(photo, (newValue) => {
  // Emit the updated photo to the parent component
  emit('update:photo', newValue);
});
// initialize components based on data attribute selectors
onMounted(() => {
  initFlowbite();
})
</script>

<script>
import FileInput from '@/Shared/FileInput.vue'

export default {
  components: {
    FileInput
  },
  props: {
    photo: File
  },
  emits: ['update:photo'],


};
</script>

<style scoped>
/* Optional: Add scoped styles here */
</style>
