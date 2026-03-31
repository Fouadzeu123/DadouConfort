<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    Palette, 
    ArrowLeft,
    Sun,
    Moon,
    Laptop,
    Check
} from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const themes = [
    { id: 'light', label: 'Mode Clair', icon: Sun, color: 'text-amber-500 bg-amber-50 dark:bg-amber-950/20' },
    { id: 'dark', label: 'Mode Ombre', icon: Moon, color: 'text-indigo-500 bg-indigo-50 dark:bg-indigo-950/20' },
    { id: 'system', label: 'Automatique', icon: Laptop, color: 'text-gray-500 bg-gray-50 dark:bg-zinc-800' },
];
</script>

<template>
    <Head title="Apparence" />

    <Layout>
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between mb-8">
            <Link href="/settings/profile" class="p-2.5 bg-white dark:bg-zinc-900 rounded-xl shadow-sm hover:scale-105 active:scale-95 transition-transform">
                <ArrowLeft class="w-5 h-5 text-gray-500" />
            </Link>
            <h2 class="text-sm font-black dark:text-white uppercase tracking-[0.2em] leading-none">Apparence</h2>
            <div class="w-10"></div>
        </div>

        <!-- Header Icon Section -->
        <div class="flex flex-col items-center py-6 mb-8">
            <div class="w-20 h-20 rounded-[2rem] bg-orange-50 dark:bg-orange-950/30 text-orange-500 flex items-center justify-center shadow-xl border border-orange-100 dark:border-orange-900/30 mb-4 transition-transform hover:rotate-6">
                <Palette class="w-10 h-10" />
            </div>
            <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest leading-none">Personnalisation</p>
        </div>

        <!-- Themes List -->
        <div class="space-y-4">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 px-4">Choisir un thème</h3>
            
            <div class="grid gap-3">
                <button v-for="theme in themes" :key="theme.id"
                        @click="updateAppearance(theme.id as any)"
                        class="relative flex items-center justify-between p-5 rounded-[2rem] border-2 transition-all active:scale-95 overflow-hidden group"
                        :class="appearance === theme.id 
                            ? 'bg-white dark:bg-zinc-900 border-primary shadow-xl shadow-primary/10' 
                            : 'bg-gray-50/50 dark:bg-zinc-900/30 border-transparent text-gray-500'">
                    
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110" :class="theme.color">
                            <component :is="theme.icon" class="w-6 h-6" />
                        </div>
                        <div class="text-left">
                            <span class="block text-sm font-black" :class="appearance === theme.id ? 'text-gray-900 dark:text-white' : ''">
                                {{ theme.label }}
                            </span>
                            <span class="text-[10px] opacity-60 font-medium">
                                {{ theme.id === 'system' ? 'S\'adapte selon votre système' : 'Thème ' + theme.id }}
                            </span>
                        </div>
                    </div>

                    <div v-if="appearance === theme.id" class="relative z-10 bg-primary text-white p-1.5 rounded-full shadow-lg">
                        <Check class="w-4 h-4" />
                    </div>

                    <!-- Decorative background for active state -->
                    <div v-if="appearance === theme.id" class="absolute right-0 top-0 w-32 h-32 bg-primary/5 rounded-full -mr-10 -mt-10 blur-2xl"></div>
                </button>
            </div>
        </div>

        <div class="mt-12 p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-gray-100 dark:border-zinc-800 text-center space-y-4 shadow-sm">
            <h4 class="text-sm font-black dark:text-white uppercase tracking-widest">Aperçu Visuel</h4>
            <div class="flex justify-center gap-2">
                <div class="w-12 h-4 bg-gray-100 dark:bg-zinc-800 rounded-full animate-pulse"></div>
                <div class="w-8 h-4 bg-primary/20 rounded-full animate-pulse"></div>
                <div class="w-16 h-4 bg-gray-50 dark:bg-zinc-800/50 rounded-full animate-pulse"></div>
            </div>
            <p class="text-[10px] text-gray-400 font-medium italic">
                Les changements sont appliqués instantanément sur tout l'appareil.
            </p>
        </div>

        <p class="text-[8px] font-black text-gray-300 uppercase text-center tracking-[0.4em] pt-12 pb-20 select-none">DadouConfort Style Engine</p>
    </Layout>
</template>
