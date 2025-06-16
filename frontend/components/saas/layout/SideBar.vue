<script setup lang="ts">
import LinkSideBar from "~/components/ui/LinkSideBar.vue";

import type { DropdownMenuItem } from '@nuxt/ui'
import {useAuthStore} from "~/store/AuthStore";
import { useRouter } from 'nuxt/app'

const auth = useAuthStore()
const router = useRouter()

const items = ref<DropdownMenuItem[]>([
  {
    label: 'Profile',
    icon: 'i-lucide-user',
    onSelect() {
      router.push('/profile')
    }
  },

  {
    label: 'Try Chrome Extension',
    icon: 'i-lucide-chrome',
    link: 'https://chromewebstore.google.com/detail/arises-pomodoro-timer-dis/aheohjodpllofjdihniljfkppcacpeib'
  },
  {
    label: 'Logout',
    icon: 'i-lucide-log-out',
    onSelect() {
      auth.logout()
    }
  },
])
</script>

<template>
  <section class="min-h-screen max-h-screen bg-background flex flex-col">

    <!-- Logo / nom -->
    <div class="flex flex-col items-center py-4">
      <NuxtImg
          src="/logo_without_text.svg"
          alt="Logo"
          class="w-8 h-8"
      />
    </div>

    <!-- Partie centrale + footer -->
    <div class="flex flex-col justify-between flex-1 ">

      <!-- Liens -->
      <div class="flex flex-col items-center gap-2">
        <UTooltip
            :content="{
              align: 'center',
              side: 'right',
              sideOffset: 8
            }"
            text="Home"
        >
          <LinkSideBar title="Home" icon="i-lucide-house" link="/dashboard" :active="true" />
        </UTooltip>
<!--        <UTooltip-->
<!--            :content="{-->
<!--              align: 'center',-->
<!--              side: 'right',-->
<!--              sideOffset: 8-->
<!--            }"-->
<!--            text="Analytics"-->
<!--        >-->
<!--          <LinkSideBar title="Analytics" icon="i-lucide-signal-high" link="/" />-->
<!--        </UTooltip>-->

<!--        <UTooltip-->
<!--            :content="{-->
<!--              align: 'center',-->
<!--              side: 'right',-->
<!--              sideOffset: 8-->
<!--            }"-->
<!--            text="Learn"-->
<!--        >-->
<!--          <LinkSideBar title="Learn" icon="i-lucide-lightbulb" link="/" />-->
<!--        </UTooltip>-->


<!--        <UTooltip-->
<!--            :content="{-->
<!--              align: 'center',-->
<!--              side: 'right',-->
<!--              sideOffset: 8-->
<!--            }"-->
<!--            text="Roadmap"-->
<!--        >-->
<!--          <LinkSideBar title="Roadmap" icon="i-lucide-map" link="/" />-->
<!--        </UTooltip>-->
      </div>

      <!-- Footer en bas -->
      <div class="border-t border-t-[1px] border-grey-calendar py-5">
          <div class="flex items-center flex-row justify-center gap-2 text-grey inter">

          <div class="relative overflow-hidden">
            <UDropdownMenu
                :items="items"
                :content="{
              align: 'end',
              side: 'right',
              sideOffset: 10
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
