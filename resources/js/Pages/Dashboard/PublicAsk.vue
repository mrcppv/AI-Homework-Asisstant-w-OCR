<template>
  <div class=" bg-gradient-to-r from-indigo-500 to-indigo-600 min-h-screen">

    <Nav />


    <div>

      <Head title="Public Ask" />
      <div class="flex justify-start mb-8 max-w-3xl">


      </div>

      <div class="container mx-auto mt-20">

        <div class="flex flex-col lg:flex-row gap-4">
          <!-- Question Section -->
          <div class="flex-1 bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-8 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
              <h2 class="text-xl font-semibold">Question</h2>
            </div>
            <form @submit.prevent="update">
              <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                <!-- Title with Tooltip -->
                <div class="w-full pb-8 pr-6 relative">
                  <label for="title" class="block text-sm font-medium text-gray-700 flex items-center">
                    Title
                    <div class="relative inline-block ml-2">
                      <button type="button" @mouseover="showTooltip = true" @mouseleave="showTooltip = false"
                        class="text-gray-600 transition-colors duration-200 hover:text-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                      </button>
                      <p v-if="showTooltip"
                        class="absolute flex items-center justify-center w-36 p-2 text-gray-600 bg-white rounded-lg shadow-lg left-10 -top-6 dark:shadow-none shadow-gray-200 dark:bg-gray-800 dark:text-white text-xs">
                        <span>Enter the title of your question</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                          class="absolute w-4 h-4 text-white transform rotate-45 -translate-y-1/2 fill-current -left-2 top-1/2 dark:text-gray-800"
                          stroke="currentColor" viewBox="0 0 24 24">
                          <path d="M20 3H4a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1z"></path>
                        </svg>
                      </p>
                    </div>
                  </label>
                  <input v-model="form.title" id="title" type="text"
                    class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Enter the title here...(Optional)" />
                </div>

                <!-- Subject and Level Selection -->
                <div class="w-full lg:w-1/2 pb-8 pr-6">
                  <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                  <select v-model="form.subject" id="subject"
                    class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="" disabled selected>Auto-Detect</option>
                    <option v-for="subject in subjects" :key="subject" :value="subject">{{ subject }}</option>

                  </select>
                </div>
                <div class="w-full lg:w-1/2 pb-8 pr-6">
                  <label for="level" class="block text-sm font-medium text-gray-700">Level</label>
                  <select v-model="form.level" id="level"
                    class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="" selected>Any Level</option>
                    <option value="1-5th grade">1-5th grade</option>
                    <option value="6-8th grade">6-8th grade</option>
                    <option value="9-12th grade">9-12th grade</option>
                    <option value="University">University</option>
                    <option value="Masters">Masters</option>
                    <option value="Expert">Expert</option>
                  </select>
                </div>

                <!-- Text Area for Homework Question -->
                <div class="relative w-full pb-8 pr-6">
                  <div class="flex items-center justify-between mb-2">
                    <label for="homework_question" class="block text-sm font-medium text-gray-700">Your homework
                      question</label>
                    <button type="button" @click="toggleUploadSection"
                      class=" flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-400 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="upload-button w-5 h-5 ">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                      </svg>
                    </button>
                  </div>
                  <textarea v-model="form.question" id="question" rows="4"
                    class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Enter your homework question here..."></textarea>
                </div>

                <!-- Accordion for Upload and Advanced Options -->
                <div class="w-full pb-8 pr-6">
                  <!-- Upload Section -->
                  <div class="border border-gray-200 rounded-md mb-4">
                    <button type="button" @click.prevent="toggleUploadSection"
                      class="w-full px-4 py-2 text-left font-semibold text-indigo-600 hover:bg-indigo-50 focus:outline-none flex justify-between items-center">
                      <span>{{ showUploadSection ? 'Hide Upload' : 'Add Upload' }}</span>
                      <svg class="w-5 h-5 transform transition-transform duration-200"
                        :class="{ 'rotate-180': showUploadSection }" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd" />
                      </svg>
                    </button>
                    <div v-show="showUploadSection" class="px-4 py-2 bg-white">
                      <FileUpload v-model="form.photo" :error="form.errors.photo" name="photo"
                        label="Upload File or Image" required />
                    </div>
                  </div>

                  <!-- Advanced Options Section -->
                  <div class="border border-gray-200 rounded-md">
                    <button @click.prevent="toggleAdvancedOptions"
                      class="w-full px-4 py-2 text-left font-semibold text-indigo-600 hover:bg-indigo-50 focus:outline-none flex justify-between items-center">
                      <span>{{ showAdvancedOptions ? 'Hide Advanced Options' : 'Show Advanced Options' }}</span>
                      <svg class="w-5 h-5 transform transition-transform duration-200"
                        :class="{ 'rotate-180': showAdvancedOptions }" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd" />
                      </svg>
                    </button>
                    <div v-show="showAdvancedOptions" class="px-4 py-2 bg-white">
                      <div class="flex items-center mb-4">
                        <input v-model="form.explain" id="explanation" type="checkbox" class="mr-2" />
                        <label for="explain" class="text-sm font-medium text-gray-700">Explain why the answer is
                          correct</label>
                      </div>
                      <div class="flex items-center mb-8">
                        <input v-model="form.steps" id="steps" type="checkbox" class="mr-2" />
                        <label for="steps" class="text-sm font-medium text-gray-700">Show the work done to get to the
                          answer</label>
                      </div>

                      <div class="w-full lg:w-1/2 pb-8 pr-6">
                        <label for="temperature" class="block text-sm font-medium text-gray-700">Creativity (Higher =
                          More
                          Creative, Lower = Logical)</label>
                        <select v-model="form.temperature" id="temperature"
                          class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                          <option value="0.6" selected>0.6</option>
                          <option value="0.0">0.0</option>
                          <option value="0.2">0.2</option>
                          <option value="0.4">0.4</option>
                          <option value="0.8">0.8</option>
                          <option value="1.0">1.0</option>
                        </select>
                      </div>
                      <div class="w-full lg:w-1/2 pb-8 pr-6">
                        <label for="model" class="block text-sm font-medium text-gray-700">AI Model</label>
                        <select v-model="form.model" id="model"
                          class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                          <option value="gpt-4o-mini" selected>GPT-4o Mini</option>
                          <option value="gpt-4-turbo">GPT-4 Turbo</option>
                          <option value="gpt-4o">GPT-4o</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                <loading-button :loading="form.processing"
                  class="w-full btn-indigo1 transition-colors duration-200 hover:text-indigo-500 flex items-center justify-center space-x-2"
                  type="submit">
                  <span class="question-mark">?</span>
                  <span>Ask</span>
                  <span class="hidden md:inline">&nbsp;Question</span>
                </loading-button>
              </div>
            </form>
          </div>
          <!-- Response Settings Section -->
          <div class="flex-1 bg-white shadow-lg rounded-lg overflow-hidden ">
            <div class=" h-full">
              <div class="px-8 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
                <h2 class="text-xl font-semibold">Response</h2>
              </div>

              <div class="p-4">
                <!-- Loader Section -->
                <div id="answer-loader" v-if="form.processing" class="flex flex-col items-center  ">
                  <div class="loader mb-10 mt-10"></div>
                  <div class="text-center mt-4">
                    <h6 class="text-gray-600">One moment while I get the answer...</h6>
                    <div class="text-gray-500">
                      Did you know that the average human attention span is 8 seconds?
                    </div>
                  </div>
                </div>

                <!-- Result Section -->
                <div id="result-card">
                  <h5 class="text-lg font-semibold mt-4 mb-2" id="result-scroll-point">Answer</h5>
                  <div>
                    <label class="flex justify-between items-end mb-2">
                      <span class="text-gray-600">
                        <i class="fa-solid fa-comments mr-2"></i>
                        The answer to your question
                      </span>
                      <a :class="copyButtonClass('answer')" @click="copyToClipboard('answer')"
                        :aria-disabled="!response || clicked.answer" href="javascript:;">
                        Copy
                      </a>
                    </label>
                    <div class="form-textarea mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm"
                      rows="3">
                      {{ response || placeholderAnswer }}
                    </div>
                  </div>

                  <div class="mt-5">
                    <h5 class="text-lg font-semibold mb-2">Explanation</h5>
                    <div>
                      <label class="flex justify-between items-end mb-2">
                        <span class="text-gray-600">
                          <i class="fa-solid fa-comments mr-2"></i>
                          Explanation of the answer
                        </span>
                        <a :class="copyButtonClass('explanation')" @click="copyToClipboard('explanation')"
                          :aria-disabled="!explanation || clicked.explanation" href="javascript:;">
                          Copy
                        </a>
                      </label>
                      <div
                        class="form-textarea mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm"
                        rows="3">
                        {{ explainResponse || placeholderExplain }}
                      </div>
                    </div>
                  </div>

                  <div class="mt-5">
                    <h5 class="text-lg font-semibold mb-2">Steps</h5>
                    <div>
                      <label class="flex justify-between items-end mb-2">
                        <span class="text-gray-600">
                          <i class="fa-solid fa-comments mr-2"></i>
                          Steps to solve the problem
                        </span>
                        <a :class="copyButtonClass('steps')" @click="copyToClipboard('steps')"
                          :aria-disabled="!steps || clicked.steps" href="javascript:;">
                          Copy
                        </a>
                      </label>
                      <div
                        class="form-textarea mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm"
                        rows="3">
                        {{ stepsResponse || placeholderSteps }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="px-4 py-8 md:flex-1 md:p-12 md:overflow-y-auto" scroll-region>
            <flash-messages ref="FlashMessages"/>

            <slot />
    </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
<contact-us-vue/>
<Footer />
</template>

<script>
import { Head } from '@inertiajs/vue3'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import FileUpload from '../../Shared/FileUpload.vue'
import Nav from './PublicNav.vue'
import Footer from './Footer.vue'
import FlashMessages from '@/Shared/FlashMessages.vue'
import ContactUsVue from './ContactUs.vue'


export default {
  components: {
    Head,
    LoadingButton,
    FlashMessages,
    ContactUsVue,
    SelectInput,
    TextInput,
    FileUpload,
    Nav,
    Footer
  },

  props: {
    response: String,
    explainResponse: String,
    stepsResponse: String,
    remainingQuestions: Number,
    selectedSubject: {
      type: String,
      default: ''
    }
  },

  data() {
    return {
      form: this.$inertia.form({
        _method: 'post',
        title: '',
        subject: this.selectedSubject,
        level: '',
        question: '',
        explain: '',
        steps: '',
        tokensCost: 1000,
        temperature: 0.7,
        model: "gpt-4o-mini",
        photo: null,
      }),
      subjects: ['Biology', 'Chemistry', 'Computer Science', 'Economics', 'English', 'Geography', 'History', 'Mathematics', 'Physics', 'Science'],

      showTooltip: false,
      showUploadSection: false,
      showAdvancedOptions: false,
      clicked: {
        answer: false,
        explanation: false,
        steps: false,
      },
      placeholderAnswer: 'Ask away!',
      placeholderExplain: 'You can enable explanations in "Advanced Options" under the question box',
      placeholderSteps: 'You can enable steps in "Advanced Options" under the question box',
    }
  },
  watch: {
    selectedSubject(newValue) {
      this.form.subject = newValue;
    }
  },

  methods: {
    copyToClipboard(type) {
      let textToCopy = '';

      if (type === 'answer') {
        textToCopy = this.response || this.placeholderAnswer;
        this.clicked.answer = true;
      } else if (type === 'explanation') {
        textToCopy = this.explainResponse || this.placeholderExplain;
        this.clicked.explanation = true;
      } else if (type === 'steps') {
        textToCopy = this.stepsResponse || this.placeholderSteps;
        this.clicked.steps = true;
      }

      navigator.clipboard.writeText(textToCopy).then(() => {
        this.copyButtonClass(type);
        this.$forceUpdate();
      });
    },
    copyButtonClass(type) {
      return this.clicked[type] ? 'bg-gray-500 text-white text-xs px-3 py-1 rounded-lg cursor-not-allowed opacity-50' : 'bg-indigo-400 text-white text-xs px-3 py-1 rounded-lg';
    },
    update() {
      this.form.post(`/public-ask`, {
        onSuccess: () => {
          this.form.reset('');
          this.clicked.answer = false;
          this.clicked.explanation = false;
          this.clicked.steps = false;
        }
      })
    },
    toggleUploadSection() {
      this.showUploadSection = !this.showUploadSection
    },
    toggleAdvancedOptions() {
      this.showAdvancedOptions = !this.showAdvancedOptions
    },
  },
}
</script>



<style>
@keyframes pulse {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.7;
  }
}

.upload-button {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* General form styling */
.container {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 2rem;
  border-radius: 1rem;
}

/* Card styling */
.bg-white {
  background-color: rgba(255, 255, 255, 0.95);
  /* Increased opacity */
  border-radius: 1rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.bg-white:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Input styling */
input[type="text"],
textarea,
select {
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.75rem 1rem;
  transition: all 0.3s ease;
}

input[type="text"]:focus,
textarea:focus,
select:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
  outline: none;
}

/* Button styling */
.btn-indigo1 {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  border-radius: 0.5rem;
  color: white;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  transition: all 0.3s ease;
}

.btn-indigo1:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Accordion styling */
.border {
  border-radius: 0.5rem;
  overflow: hidden;
  transition: all 0.3s ease;
}

.border:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Checkbox styling */
input[type="checkbox"] {
  appearance: none;
  width: 1.5rem;
  height: 1.5rem;
  border: 2px solid #4f46e5;
  border-radius: 0.25rem;
  transition: all 0.3s ease;
}

input[type="checkbox"]:checked {
  background-color: #4f46e5;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3E%3C/svg%3E");
  background-size: 100% 100%;
  background-position: center;
  background-repeat: no-repeat;
}

/* Tooltip styling */
.tooltip {
  background-color: #1f2937;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

* {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Animated Question Mark */
.question-mark {
  display: inline-block;
  font-size: 1.5rem;
  margin-right: 0.5rem;
  animation: flip-spin 5s ease-in-out infinite;
}

@keyframes flip-spin {
  0% {
    transform: rotate(0deg) scaleY(1);
  }

  25% {
    transform: rotate(90deg) scaleY(-1);
  }

  50% {
    transform: rotate(180deg) scaleY(1);
  }

  75% {
    transform: rotate(270deg) scaleY(-1);
  }

  100% {
    transform: rotate(360deg) scaleY(1);
  }
}

/* Tooltip styles */
.relative .tooltip {
  display: none;
}

.relative:hover .tooltip {
  display: block;
}

.tooltip {
  position: absolute;
  top: 100%;
  left: 100%;
  transform: translateX(10px);
  background-color: #333;
  color: #fff;
  padding: 2px 4px;
  /* Smaller padding */
  border-radius: 4px;
  white-space: nowrap;
  font-size: 0.625rem;
  /* Smaller font size */
  z-index: 10;
}


/* Fade transition for the expandable section */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to

/* .fade-leave-active in <2.1.8 */
  {
  opacity: 0;
}





.form-textarea {
  white-space: pre-wrap;
  /* Ensures whitespace and line breaks are preserved */
  overflow: auto;
  resize: none;
  min-height: 4em;
  /* Adjust the height as needed */
  padding: 0.5em;
}


/* HTML: <div class="loader"></div> */
.loader {
  height: 60px;
  aspect-ratio: 1;
  position: relative;
}

.loader::before,
.loader::after {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: 50%;
  transform-origin: bottom;
}

.loader::after {
  background:
    radial-gradient(at 75% 15%, #fffb, #0000 35%),
    radial-gradient(at 80% 40%, #0000, #0008),
    radial-gradient(circle 5px, #fff 94%, #0000),
    radial-gradient(circle 10px, #000 94%, #0000),
    linear-gradient(#F93318 0 0) top /100% calc(50% - 5px),
    linear-gradient(#fff 0 0) bottom/100% calc(50% - 5px) #000;
  background-repeat: no-repeat;
  animation: l20 1s infinite cubic-bezier(0.5, 120, 0.5, -120);
}

.loader::before {
  background: #ddd;
  filter: blur(8px);
  transform: scaleY(0.4) translate(-13px, 0px);
}

@keyframes l20 {

  30%,
  70% {
    transform: rotate(0deg)
  }

  49.99% {
    transform: rotate(0.2deg)
  }

  50% {
    transform: rotate(-0.2deg)
  }
}



/* Smooth transition for accordion content */
.accordion-content-enter-active,
.accordion-content-leave-active {
  transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
  max-height: 1000px;
  /* Adjust this value based on your content's maximum height */
  opacity: 1;
}

.accordion-content-enter-from,
.accordion-content-leave-to {
  max-height: 0;
  opacity: 0;
}
</style>
