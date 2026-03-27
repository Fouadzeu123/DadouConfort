<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    User, 
    Settings, 
    Bell, 
    Shield, 
    ArrowLeft,
    Phone,
    MapPin,
    LogOut,
    Sun,
    Moon,
    Laptop,
    ChevronRight,
    HelpCircle,
    Info
} from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const menuItems = [
    { label: 'Infos Personnelles', icon: User, color: 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' },
    { label: 'Sécurité', icon: Shield, color: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' },
    { label: 'Centre d\'aide', icon: HelpCircle, color: 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' },
    { label: 'À propos', icon: Info, color: 'bg-gray-100 text-gray-600 dark:bg-zinc-800 dark:text-zinc-400' },
];
</script>

<template>
    <Head title="Réglages & Profil" />

    <Layout>
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between mb-8">
            <Link href="/dashboard" class="p-2.5 bg-white dark:bg-zinc-900 rounded-xl shadow-sm hover:scale-105 active:scale-95 transition-transform">
                <ArrowLeft class="w-5 h-5 text-gray-500" />
            </Link>
            <h2 class="text-sm font-black dark:text-white uppercase tracking-[0.2em] leading-none">Profil & Réglages</h2>
            <div class="w-10"></div>
        </div>

        <!-- User Profile Card -->
        <div class="text-center py-6 mb-8">
            <div class="relative inline-block">
                <div class="w-24 h-24 bg-primary text-white rounded-full flex items-center justify-center text-3xl font-black mb-4 border-4 border-white dark:border-zinc-950 shadow-2xl relative z-10">
                    D
                </div>
                <div class="absolute -bottom-2 -right-2 bg-emerald-500 w-8 h-8 rounded-full border-4 border-white dark:border-zinc-950 flex items-center justify-center shadow-lg z-20">
                     <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                </div>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white mt-4">Dadou Confort</h1>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Gérant Propriétaire</p>
        </div>

        <!-- Appearance (Theme) Switcher -->
        <section class="mb-8">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 px-2">Apparence de l'application</h3>
            <div class="bg-gray-100 dark:bg-zinc-900/50 p-1.5 rounded-3xl grid grid-cols-3 gap-1">
                <button @click="updateAppearance('light')"
                        class="flex flex-col items-center gap-2 py-4 rounded-[1.5rem] transition-all"
                        :class="appearance === 'light' ? 'bg-white text-primary shadow-lg shadow-gray-200' : 'text-gray-400 dark:text-zinc-500'">
                    <Sun class="w-5 h-5" />
                    <span class="text-[9px] font-black uppercase tracking-widest">Clair</span>
                </button>
                <button @click="updateAppearance('dark')"
                        class="flex flex-col items-center gap-2 py-4 rounded-[1.5rem] transition-all"
                        :class="appearance === 'dark' ? 'bg-zinc-800 text-white shadow-lg shadow-black/20' : 'text-gray-400 dark:text-zinc-500'">
                    <Moon class="w-5 h-5" />
                    <span class="text-[9px] font-black uppercase tracking-widest">Ombre</span>
                </button>
                <button @click="updateAppearance('system')"
                        class="flex flex-col items-center gap-2 py-4 rounded-[1.5rem] transition-all"
                        :class="appearance === 'system' ? 'bg-gray-50 dark:bg-zinc-800 text-primary dark:text-white shadow-sm' : 'text-gray-400 dark:text-zinc-500'">
                    <Laptop class="w-5 h-5" />
                    <span class="text-[9px] font-black uppercase tracking-widest">Auto</span>
                </button>
            </div>
        </section>

        <!-- General Menu -->
        <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-4 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-1">
            <button v-for="item in menuItems" :key="item.label"
                    class="w-full p-4 flex items-center justify-between group hover:bg-gray-50 dark:hover:bg-white/5 rounded-2xl transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" :class="item.color">
                        <component :is="item.icon" class="w-5 h-5" />
                    </div>
                    <span class="text-sm font-bold text-gray-700 dark:text-zinc-300">{{ item.label }}</span>
                </div>
                <ChevronRight class="w-4 h-4 text-gray-300 dark:text-zinc-600 transform group-hover:translate-x-1" />
            </button>
        </div>

        <div class="py-8">
            <Link href="/logout" method="post" as="button" 
                  class="w-full p-5 bg-rose-50 dark:bg-rose-950/20 text-rose-500 rounded-3xl font-black text-xs uppercase tracking-[0.3em] flex items-center justify-center gap-3 active:scale-95 transition-all shadow-sm border border-rose-100 dark:border-rose-900/10">
                <LogOut class="w-5 h-5" />
                Déconnexion
            </Link>
        </div>

        <p class="text-[8px] font-black text-gray-300 uppercase text-center tracking-[0.4em] py-10 pb-20 select-none">DadouConfort v1.0 • Yaoundé, CMR</p>
    </Layout>
</template>
