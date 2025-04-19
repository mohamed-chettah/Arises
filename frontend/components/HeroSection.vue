<script setup lang="ts">
import FormWaitlist from "~/components/FormWaitlist.vue";
</script>

<template>
  <div class="flex items-center justify-center flex-col gap-12 mt-10 px-4">
    <div class="flex flex-col justify-center gap-3 items-center">
      <UBadge
          icon="i-heroicons-moon"
          size="sm"
          class="badge-animate border border-white/10 bg-white/5 backdrop-blur-sm text-white px-4 py-1 rounded-xl font-medium tracking-wide"
          color="primary"
          variant="solid"
      >
        Inspired by Solo Leveling
      </UBadge>

      <h1 class="bank-gothic text-white text-[28px] sm:text-[40px] 2xl:text-[48px] xl:leading-tight leading-wide text-center heading-animate">
        Your smart AI calendar<br> to <span class="bg-[#435EF2]/30 px-3 py-1 rounded-lg highlight-animate">Plan Better</span> and
        <br class="xl:hidden block">
        <span class="bg-[#A480F2]/50 px-3 py-1 rounded-lg highlight-animate">Level Up Faster.</span>
      </h1>
    </div>

    <div class="w-full xl:w-1/3 w-1/2 mt-2 fade-in-animate">
      <h2 class="inter text-white text-lg sm:text-xl text-center pulse-animate">
        Coming Soon
      </h2>

      <FormWaitlist />
    </div>

    <USeparator color="primary" class="w-full sm:w-1/2 text-[#A480F2] separator-animate" />

    <a target="_blank" href="https://chromewebstore.google.com/detail/arises-pomodoro-timer-dis/aheohjodpllofjdihniljfkppcacpeib" class="w-full xl:w-1/3 w-1/2">
      <UButton type="submit" class="bg-[#445EF2] hover:bg-[#445EF2]/80 cta w-full inter">
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="26" height="26" viewBox="0 0 48 48" style="margin-right: 5px">
          <path fill="#fff" d="M34,24c0,5.521-4.479,10-10,10s-10-4.479-10-10s4.479-10,10-10S34,18.479,34,24z"></path><linearGradient id="Pax8JcnMzivu8f~SZ~k1ya_ejub91zEY6Sl_gr1" x1="5.789" x2="31.324" y1="34.356" y2="20.779" gradientTransform="matrix(1 0 0 -1 0 50)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#4caf50"></stop><stop offset=".489" stop-color="#4aaf50"></stop><stop offset=".665" stop-color="#43ad50"></stop><stop offset=".79" stop-color="#38aa50"></stop><stop offset=".892" stop-color="#27a550"></stop><stop offset=".978" stop-color="#11a050"></stop><stop offset="1" stop-color="#0a9e50"></stop></linearGradient><path fill="url(#Pax8JcnMzivu8f~SZ~k1ya_ejub91zEY6Sl_gr1)" d="M31.33,29.21l-8.16,14.77C12.51,43.55,4,34.76,4,24C4,12.96,12.96,4,24,4v11 c-4.97,0-9,4.03-9,9s4.03,9,9,9C27.03,33,29.7,31.51,31.33,29.21z"></path><linearGradient id="Pax8JcnMzivu8f~SZ~k1yb_ejub91zEY6Sl_gr2" x1="33.58" x2="33.58" y1="6" y2="34.797" gradientTransform="matrix(1 0 0 -1 0 50)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#ffd747"></stop><stop offset=".482" stop-color="#ffd645"></stop><stop offset=".655" stop-color="#fed43e"></stop><stop offset=".779" stop-color="#fccf33"></stop><stop offset=".879" stop-color="#fac922"></stop><stop offset=".964" stop-color="#f7c10c"></stop><stop offset="1" stop-color="#f5bc00"></stop></linearGradient><path fill="url(#Pax8JcnMzivu8f~SZ~k1yb_ejub91zEY6Sl_gr2)" d="M44,24c0,11.05-8.95,20-20,20h-0.84l8.17-14.79C32.38,27.74,33,25.94,33,24 c0-4.97-4.03-9-9-9V4c7.81,0,14.55,4.48,17.85,11C43.21,17.71,44,20.76,44,24z"></path><linearGradient id="Pax8JcnMzivu8f~SZ~k1yc_ejub91zEY6Sl_gr3" x1="36.128" x2="11.574" y1="44.297" y2="28.954" gradientTransform="matrix(1 0 0 -1 0 50)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#f7572f"></stop><stop offset=".523" stop-color="#f7552d"></stop><stop offset=".712" stop-color="#f75026"></stop><stop offset=".846" stop-color="#f7461b"></stop><stop offset=".954" stop-color="#f7390a"></stop><stop offset="1" stop-color="#f73100"></stop></linearGradient><path fill="url(#Pax8JcnMzivu8f~SZ~k1yc_ejub91zEY6Sl_gr3)" d="M41.84,15H24c-4.97,0-9,4.03-9,9c0,1.49,0.36,2.89,1.01,4.13H16L7.16,13.26H7.14 C10.68,7.69,16.91,4,24,4C31.8,4,38.55,8.48,41.84,15z"></path><linearGradient id="Pax8JcnMzivu8f~SZ~k1yd_ejub91zEY6Sl_gr4" x1="19.05" x2="28.95" y1="30.95" y2="21.05" gradientTransform="matrix(1 0 0 -1 0 50)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#2aa4f4"></stop><stop offset="1" stop-color="#007ad9"></stop></linearGradient><path fill="url(#Pax8JcnMzivu8f~SZ~k1yd_ejub91zEY6Sl_gr4)" d="M31,24c0,3.867-3.133,7-7,7s-7-3.133-7-7s3.133-7,7-7S31,20.133,31,24z"></path>
        </svg>
        Install the Chrome Extension
      </UButton>
    </a>

    <div class="w-full border-6 bg-transparent border-[#A480F2]/30 rounded-xl focus:outline-none focus:border-[#A480F2] backdrop-blur-xl backdrop-blur-xl">
      <video controls autoplay muted loop class="w-full h-full rounded-lg">
        <source src="/video/arises-extension.mp4" type="video/mp4" />
      </video>
    </div>
  </div>

  <Footer />
</template>

<style scoped>
/* Fade-in animation for the heading */
.heading-animate {
  animation: fadeIn 1.2s ease-out;
}

/* Subtle highlight animation for the colored text */
.highlight-animate {
  animation: highlightPulse 3s infinite alternate;
  transition: transform 0.3s ease;
}

.highlight-animate:hover {
  transform: translateY(-2px);
}

/* Badge animation */
.badge-animate {
  animation: slideDown 0.8s ease-out;
}

/* Coming soon text animation */
.pulse-animate {
  animation: pulse 4s infinite;
}

.separator-animate {
  animation: expandWidth 1.5s ease-out;
}


/* Fade-in animation for form */
.fade-in-animate {
  animation: fadeIn 1.5s ease-out;
}

/* Animation keyframes */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes highlightPulse {
  0% {
    box-shadow: 0 0 0 rgba(164, 128, 242, 0);
  }
  50% {
    box-shadow: 0 0 10px rgba(164, 128, 242, 0.3);
  }
  100% {
    box-shadow: 0 0 0 rgba(164, 128, 242, 0);
  }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

@keyframes expandWidth {
  from {
    width: 0;
    opacity: 0;
  }
  to {
    width: 50%;
    opacity: 1;
  }
}
</style>