<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Action {
  icon: any;
  onClick?: () => void;
  href?: string;
  class?: string;
}

const props = defineProps<{
  title: string;
  showBackButton?: boolean;
  backHref?: string;
  actions?: Action[];
}>();

const goBack = () => {
  if (props.backHref) {
    router.visit(props.backHref);
  } else {
    window.history.back();
  }
};
</script>

<template>
  <header class="sticky top-0 z-50 w-full bg-white/80 dark:bg-zinc-950/80 backdrop-blur-lg border-b border-gray-100 dark:border-zinc-900 h-16 flex items-center px-4 transition-all">
    <!-- Left Section: Back Button or Empty -->
    <div class="w-12 flex items-center justify-start">
      <Link v-if="showBackButton && backHref" :href="backHref" class="p-2 -ml-2 text-zinc-500 dark:text-zinc-400 active:scale-90 transition-transform">
        <ArrowLeft class="w-6 h-6" />
      </Link>
      <button v-else-if="showBackButton" @click="goBack" class="p-2 -ml-2 text-zinc-500 dark:text-zinc-400 active:scale-90 transition-transform">
        <ArrowLeft class="w-6 h-6" />
      </button>
    </div>

    <!-- Center Section: Centered Title -->
    <div class="flex-1 flex justify-center overflow-hidden px-2">
      <h1 class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-[0.15em] truncate text-center leading-none">
        {{ title }}
      </h1>
    </div>

    <!-- Right Section: Actions (Max 2) -->
    <div class="w-12 flex items-center justify-end gap-1">
      <template v-for="(action, index) in actions?.slice(0, 2)" :key="index">
        <Link v-if="action.href" :href="action.href" :class="['p-2 text-zinc-500 dark:text-zinc-400 active:scale-90 transition-transform', action.class]">
          <component :is="action.icon" class="w-5 h-5" />
        </Link>
        <button v-else @click="action.onClick" :class="['p-2 text-zinc-500 dark:text-zinc-400 active:scale-90 transition-transform', action.class]">
          <component :is="action.icon" class="w-5 h-5" />
        </button>
      </template>
      <div v-if="!actions || actions.length === 0" class="w-6"></div>
    </div>
  </header>
</template>

<style scoped>
/* Specific mobile touch optimizations */
button, a {
  -webkit-tap-highlight-color: transparent;
}
</style>
