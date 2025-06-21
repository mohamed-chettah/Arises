<script setup lang="ts">
import type { DropdownMenuItem } from '@nuxt/ui'

const items = ref<DropdownMenuItem[]>([
  {
    label: 'Profile',
    icon: 'i-lucide-user'
  },
  {
    label: 'Logout',
    icon: 'i-lucide-cog',
    onSelect() {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      navigateTo('/login')
    }
  }
])
</script>

<template>
  <div class="py-2">
    <p class="text-center inter text-purple text-sm font-semibold"> Arises Beta</p>
  </div>
</template>

<style scoped>

</style>