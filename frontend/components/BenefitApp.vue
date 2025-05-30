<script setup lang="ts">
const cards = [
  {
    title: 'Chat AI',
    description: 'Think, organize, and turn thoughts into clear action plans.',
    image: '/images/chat-ai.svg'
  },
  {
    title: 'Calendar',
    description: 'Arises AI add goals, tasks, and sessions directly to your calendar.',
    image: '/images/calendar.svg'
  },
  {
    title: 'Achievement',
    description: 'No noise, just momentum — progress designed with intent.',
    image: '/images/achievement.svg'
  }
]
</script>

<template>

  <section class="sm:mt-[200px] mt-[150px]">
    <h3 class="space-grotesk sm:text-4xl text-3xl text-center text-white">From idea to achievement</h3>

    <div class="mt-16 sm:mx-0 mx-5 flex lg:flex-row flex-col items-center justify-center gap-10">
      <div v-for="card in cards">
        <CardBenefit :title="card.title" :description="card.description" :image="card.image" />
      </div>
    </div>
  </section>


</template>

<style scoped>

</style>