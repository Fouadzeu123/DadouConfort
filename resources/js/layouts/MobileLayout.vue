<script setup lang="ts">
import { Capacitor } from '@capacitor/core';
import { Keyboard } from '@capacitor/keyboard';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Briefcase,
    CreditCard,
    FileText,
    HardHat,
    LayoutDashboard,
    Package,
    Settings,
    TrendingDown,
    Users
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const page = usePage();
const currentRoute = computed(() => page.url);

const navItems = [
    { label: 'Accueil', icon: LayoutDashboard, href: '/dashboard', active: currentRoute.value === '/dashboard' },
    { label: 'Clients', icon: Users, href: '/clients', active: currentRoute.value.startsWith('/clients') },
    { label: 'Chantiers', icon: HardHat, href: '/chantiers', active: currentRoute.value.startsWith('/chantiers') },
    { label: 'Devis', icon: FileText, href: '/devis', active: currentRoute.value.startsWith('/devis') },
    { label: 'Plus', icon: Settings, href: '#', active: false }, // Pour un menu secondaire
];

const secondaryMenuItems = [
    { label: 'Prestataires', icon: Briefcase, href: '/prestataires', active: currentRoute.value.startsWith('/prestataires') },
    { label: 'Services', icon: Package, href: '/services', active: currentRoute.value.startsWith('/services') },
    { label: 'Contrats', icon: FileText, href: '/contrats', active: currentRoute.value.startsWith('/contrats') },
    { label: 'Paiements', icon: CreditCard, href: '/paiements', active: currentRoute.value.startsWith('/paiements') },
    { label: 'Dépenses', icon: TrendingDown, href: '/depenses', active: currentRoute.value.startsWith('/depenses') },
];

const showSecondaryMenu = ref(false);
const isKeyboardVisible = ref(false);

const toggleSecondaryMenu = () => {
    showSecondaryMenu.value = !showSecondaryMenu.value;
};

onMounted(() => {
    // Native Keyboard detection
    if (Capacitor.isNativePlatform()) {
        Keyboard.addListener('keyboardWillShow', () => {
            isKeyboardVisible.value = true;
            if (showSecondaryMenu.value) {
                showSecondaryMenu.value = false;
            }
        });
        Keyboard.addListener('keyboardWillHide', () => {
            isKeyboardVisible.value = false;
        });
    }

    // Web-based keyboard detection (height change)
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', () => {
            if (window.visualViewport) {
                const isSmall = window.visualViewport.height < window.innerHeight * 0.75;
                isKeyboardVisible.value = isSmall;
            }
        });
    }
});

onUnmounted(() => {
    if (Capacitor.isNativePlatform()) {
        Keyboard.removeAllListeners();
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-950 pb-32 flex flex-col font-sans">
        <!-- Main Content -->
        <main class="flex-1 w-full max-w-lg mx-auto p-4 space-y-6">
            <slot />
        </main>

        <!-- Secondary Menu Overlay -->
        <div v-if="showSecondaryMenu" 
             @click="toggleSecondaryMenu"
             class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm transition-opacity">
        </div>
        
        <div v-if="showSecondaryMenu"
             class="fixed bottom-24 left-4 right-4 z-50 bg-white dark:bg-zinc-900 rounded-[2.5rem] p-6 shadow-2xl border border-primary/10 animate-in slide-in-from-bottom-10 duration-300">
            <div class="flex items-center justify-between mb-6 px-2">
                <h3 class="text-[10px] font-black text-gray-400 dark:text-zinc-500 uppercase tracking-[0.2em]">Pilotage de l'entreprise</h3>
                <div class="w-8 h-1 bg-gray-100 dark:bg-zinc-800 rounded-full"></div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <Link v-for="item in secondaryMenuItems" 
                      :key="item.href"
                      :href="item.href"
                      @click="toggleSecondaryMenu"
                      class="flex flex-col items-center gap-3 p-4 rounded-3xl transition-all active:scale-90"
                      :class="item.active ? 'bg-primary/10 border border-primary/20' : 'bg-gray-50 dark:bg-zinc-800/50 border border-transparent'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="item.active ? 'bg-primary text-white' : 'bg-white dark:bg-zinc-900 shadow-sm'">
                        <component :is="item.icon" class="w-5 h-5" />
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-wider text-center" :class="item.active ? 'text-primary' : 'text-gray-500 dark:text-zinc-400'">{{ item.label }}</span>
                </Link>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <nav v-show="!isKeyboardVisible" class="fixed bottom-6 left-6 right-6 z-50 bg-zinc-900/90 dark:bg-zinc-900/95 backdrop-blur-xl rounded-[2.5rem] border border-white/10 flex items-center justify-around px-4 py-3 shadow-2xl transition-all">
            <template v-for="item in navItems" :key="item.label">
                <Link v-if="item.href !== '#'" 
                      :href="item.href"
                      class="flex flex-col items-center justify-center min-w-[50px] transition-all duration-300"
                      :class="item.active ? 'text-primary' : 'text-zinc-500'">
                    <component :is="item.icon" class="w-5 h-5 mb-1" :class="{ 'scale-110 drop-shadow-[0_0_8px_rgba(212,175,55,0.4)]': item.active }" />
                    <span class="text-[8px] font-black uppercase tracking-tighter">{{ item.label }}</span>
                    <div v-if="item.active" class="w-1 h-1 bg-primary rounded-full mt-1"></div>
                </Link>
                <button v-else 
                        @click="toggleSecondaryMenu"
                        class="flex flex-col items-center justify-center min-w-[50px] transition-all duration-300"
                        :class="showSecondaryMenu ? 'text-primary' : 'text-zinc-500'">
                    <div class="w-12 h-12 bg-primary dark:bg-primary rounded-full flex items-center justify-center -mt-8 shadow-xl shadow-primary/20 border-4 border-zinc-950">
                        <component :is="item.icon" class="w-6 h-6 text-zinc-900" :class="{ 'rotate-90': showSecondaryMenu }" />
                    </div>
                    <span class="text-[8px] font-black uppercase tracking-tighter mt-1">{{ item.label }}</span>
                </button>
            </template>
        </nav>
    </div>
</template>

<style scoped>
.font-sans {
    font-family: var(--font-sans);
}
</style>
