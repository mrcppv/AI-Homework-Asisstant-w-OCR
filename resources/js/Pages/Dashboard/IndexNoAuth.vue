<template>

  <div class="bg-gradient-to-b from-indigo-900 to-indigo-700 min-h-screen">



    <Hero :isAuthenticated="$page.props.auth.user !== null" />
    <Nav />


    <feature-section />
    <black-bar :isAuthenticated="$page.props.auth.user !== null" />
    <steps></steps>


    <pricing />


    <Footer />





  </div>
</template>


<script>
import { Head, Link } from '@inertiajs/vue3'

import OCR from '@/Pages/Announcements/OCR.vue'

import Icon from '@/Shared/Icon.vue' // Import the Icon component

import FeatureSection from './FeatureSection.vue'; // Adjust the path as necessary
import BlackBar from './BlackBar.vue'
import Nav from './PublicNav.vue'
import Hero from './Hero.vue'
import Steps from './Steps.vue'
import DarkDivider from './DarkDivider.vue'
import Pricing from './Pricing.vue'
import Footer from './Footer.vue'

import { ref } from 'vue'





export default {


  props: {

  },



  components: {
    Head,
    OCR,
    Icon,
    FeatureSection,
    BlackBar,
    Nav,
    Hero,
    Steps,
    DarkDivider,
    Pricing,
    Footer,
    Link
  },

  setup() {
    const mobileMenuOpen = ref(false)
    const toggleMobileMenu = () => {
      mobileMenuOpen.value = !mobileMenuOpen.value
    }

    return {
      mobileMenuOpen,
      toggleMobileMenu
    }
  }





}
</script>

<style scoped></style>
