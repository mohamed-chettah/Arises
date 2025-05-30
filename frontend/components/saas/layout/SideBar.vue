<script setup lang="ts">
import LinkSideBar from "~/components/ui/LinkSideBar.vue";

import type { DropdownMenuItem } from '@nuxt/ui'
import {useAuthStore} from "~/store/AuthStore";

const auth = useAuthStore()

const items = ref<DropdownMenuItem[]>([
  {
    label: 'Profile',
    icon: 'i-lucide-user'
  },
  {
    label: 'Logout',
    icon: 'i-lucide-cog',
    onSelect() {
      const token = useCookie('token')
      token.value = null
      const user = useCookie('user')
      user.value = null
      location.reload()
    }
  },
  {
    label: 'Chrome Extension',
    icon: 'i-lucide-chrome',
    link: 'https://chromewebstore.google.com/detail/arises-pomodoro-timer-dis/aheohjodpllofjdihniljfkppcacpeib'
  }
])
</script>

<template>
  <section class="min-h-screen bg-background border-r border-r-[1px] border-grey-calendar flex flex-col">

    <!-- Logo / nom -->
    <div class="flex flex-col items-center py-4">
      <NuxtImg
          src="/logo_without_text.svg"
          alt="Logo"
          class="w-8 h-8"
      />
    </div>

    <!-- Partie centrale + footer -->
    <div class="flex flex-col justify-between flex-1 border-t border-t-[1px] border-grey-calendar pt-4">

      <!-- Liens -->
      <div class="flex flex-col items-center gap-2">
        <LinkSideBar title="Home" icon="i-lucide-house" link="/" :active="true" />
        <LinkSideBar title="Tasks" icon="i-lucide-gallery-horizontal-end" link="/" />
<!--        <LinkSideBar title="Integrations" icon="i-lucide-puzzle" link="/" />-->
        <LinkSideBar title="Usage" icon="i-lucide-signal-high" link="/" />
        <LinkSideBar title="Learn" icon="i-lucide-lightbulb" link="/" />
      </div>

      <!-- Footer en bas -->
      <div class="border-t border-t-[1px] border-grey-calendar py-5">
          <div class="flex items-center flex-row justify-center gap-2 text-grey inter">

          <div class="relative overflow-hidden">
            <UDropdownMenu
                :items="items"
                :content="{
              align: 'start',
              side: 'bottom',
              sideOffset: 8
              }"
                :ui="{
                content: 'w-48 cursor-pointer'
              }"
            >
              <UButton class="cursor-pointer hover:bg-purple/20 hover:text-primary w-9 h-8 py-2.5 bg-purple/20 rounded-lg inline-flex flex-col justify-center items-center gap-2.5 inter" >
                {{ auth.user?.name[0].toUpperCase() }}
              </UButton>
            </UDropdownMenu>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<style scoped>
</style>
