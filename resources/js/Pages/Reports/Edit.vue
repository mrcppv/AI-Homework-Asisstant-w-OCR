<template>
  <div>

    <Head :title="`${form.first_name} ${form.last_name}`" />
    <div class="flex justify-start mb-8 max-w-3xl">
      <h1 class="text-3xl font-bold">
        <Link class="text-indigo-400 hover:text-indigo-600" href="/reports">Reports</Link>
        <span class="text-indigo-400 font-medium"> /</span>
        {{ form.first_name }} {{ form.last_name }}
      </h1>
      <img v-if="contact.photo" class="block ml-4 w-8 h-8 rounded-full" :src="contact.photo" />

     <img>
    </div>
    <trashed-message v-if="contact.deleted_at" class="mb-6" @restore="restore"> This contact has been deleted.
    </trashed-message>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.first_name" :error="form.errors.first_name" class="pb-8 pr-6 w-full lg:w-1/2"
            label="First name" />
          <text-input v-model="form.last_name" :error="form.errors.last_name" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Last name" />
            <text-input v-model="form.id" :error="form.errors.id" class="pb-8 pr-6 w-full lg:w-1/2"
            label="ID #" disabled/>
          <select-input v-model="form.organization_id" :error="form.errors.organization_id"
            class="pb-8 pr-6 w-full lg:w-1/2" label="Organization">
            <option :value="null" />
            <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{
              organization.name }}</option>
          </select-input>
          <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.phone" :error="form.errors.phone" class="pb-8 pr-6 w-full lg:w-1/2" label="Phone" />
          <text-input v-model="form.address" :error="form.errors.address" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Address" />
          <text-input v-model="form.city" :error="form.errors.city" class="pb-8 pr-6 w-full lg:w-1/2" label="City" />
          <text-input v-model="form.region" :error="form.errors.region" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Province/State" />
          <select-input v-model="form.country" :error="form.errors.country" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Country">
            <option :value="null" />
            <option value="CA">Canada</option>
            <option value="US">United States</option>
          </select-input>
          <text-input v-model="form.postal_code" :error="form.errors.postal_code" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Postal code" />
          <text-input v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full lg:w-100%"
            label="Description" />
          <text-input v-model="form.strengthsText" :error="form.errors.strengths" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Strengths" />
          <text-input v-model="form.soft_skillsText" :error="form.errors.soft_skills" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Soft Skills" />
          <file-input v-model="form.photo" :error="form.errors.photo" class="pb-8 pr-6 w-full lg:w-1/2" type="file"
            accept="image/*" label="Photo" />

          <text-input v-if="contact.hired_by" v-model="form.hired_by" :value="contact.hired_by"
            class="pb-8 pr-6 w-full lg:w-1/2" label="Hired By USER ID#" disabled />
          <text-input v-if="contact.hired_on" v-model="form.hired_on" :value="contact.hired_on"
            class="pb-8 pr-6 w-full lg:w-1/2" label="Hired On" disabled />
        </div>

        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!contact.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button"
            @click="destroy">Delete Contact</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Update
            Contact</loading-button>


        </div>
      </form>

      <!-- Email Contact button outside form -->
      <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
        <button v-if="!contact.hired_on" class="btn-indigo ml-4" @click="hireContact">Hire Contact</button>
        <button v-else class="btn-indigo ml-4" @click="fireContact">Fire Contact</button>
        <button class="btn-indigo ml-auto" @click="showEmailForm">Email Contact</button>

      </div>
    </div>
    <!-- EmailForm component -->
    <email-form v-if="showEmail" @emailSent="handleEmailSent" @close="closeEmailForm" :contacte="contact" />

  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import FileInput from '@/Shared/FileInput.vue'

import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TrashedMessage from '@/Shared/TrashedMessage.vue'
import EmailForm from '../../Shared/EmailForm.vue'


export default {
  components: {
    FileInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    EmailForm,
  },
  layout: Layout,
  props: {
    contact: Object,
    organizations: Array,
  },
  mounted() {
    this.initializeFormFields();
  },
  watch: {
    'form.strengths': function (newVal) {
      this.form.strengthsText = newVal ? JSON.parse(newVal).join(", ") : "";
    },
    'form.soft_skills': function (newVal) {
      this.form.soft_skillsText = newVal ? JSON.parse(newVal).join(", ") : "";
    },
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        first_name: this.contact.first_name,
        last_name: this.contact.last_name,
        organization_id: this.contact.organization_id,
        email: this.contact.email,
        phone: this.contact.phone,
        address: this.contact.address,
        city: this.contact.city,
        region: this.contact.region,
        country: this.contact.country,
        postal_code: this.contact.postal_code,
        description: this.contact.description,
        strengths: this.contact.strengths,
        soft_skills: this.contact.soft_skills,
        strengthsText: "",
        soft_skillsText: "",
        hired_by: this.contact.hired_by,
        hired_on: this.contact.hired_on,
        photo: null,
        id: this.contact.id,

      }),
      showEmail: false, // State variable to control EmailForm visibility
      contacte: this.contact,


    }
  },
  methods: {
    initializeFormFields() {
      this.form.strengthsText = this.form.strengths ? JSON.parse(this.form.strengths).join(", ") : "";
      this.form.soft_skillsText = this.form.soft_skills ? JSON.parse(this.form.soft_skills).join(", ") : "";
    },
    update() {
      // Convert strengths and soft_skills text back to JSON strings
      this.form.strengths = JSON.stringify(this.form.strengthsText.split(',').map(str => str.trim()));
      this.form.soft_skills = JSON.stringify(this.form.soft_skillsText.split(',').map(str => str.trim()));

      this.form.post(`/reports/${this.contact.id}`, {
        onSuccess: () => this.form.reset('photo'),
      }).then(this.initializeFormFields());


    },
    destroy() {
      if (confirm('Are you sure you want to delete this contact?')) {
        this.$inertia.delete(`/contacts/${this.contact.id}`)
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this contact?')) {
        this.$inertia.put(`/contacts/${this.contact.id}/restore`)
      }
    },

    // Method to show EmailForm
    showEmailForm() {
      this.showEmail = true;
    },

    // Method to close EmailForm
    closeEmailForm() {
      this.showEmail = false;
    },

    handleEmailSent(data) {
      if (confirm('Are you sure you want to send an email to this contact?')) {
        this.$inertia.post(`/reports/${this.contact.id}/send-email`, data)
      }
    },

    hireContact() {
      if (confirm('Are you sure you want to hire this contact?')) {
        this.$inertia.post(`/reports/${this.contact.id}/hire`)
      }
    },
    fireContact() {
      if (confirm('Are you sure you want to fire this contact?')) {
        this.$inertia.post(`/reports/${this.contact.id}/fire`).then(() => {
          // Update form data after firing
          this.form.hired_on = null;
        });
      }
    },
  }
}
</script>
