<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import AppBar from '@/components/AppBar.vue';
import { 
    Plus, 
    FileText, 
    Search, 
    ChevronRight, 
    HardHat, 
    Users, 
    Calendar,
    ArrowRight,
    Filter,
    Briefcase
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    contrats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get('/contrats', { search: value }, { preserveState: true, replace: true });
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'actif': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400';
        case 'termine': return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400';
        case 'resilie': return 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400';
        default: return 'bg-gray-100 text-gray-700';
    }
};
</script>

<template>
    <Head title="Suivi des Contrats" />

    <Layout>
        <AppBar title="Contrats" :actions="[{ icon: Plus, href: '/contrats/create' }]" />

        <div class="space-y-4">

            <div class="relative group">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500 group-focus-within:text-primary transition-colors" />
                <input v-model="search"
                       type="text" 
                       placeholder="Rechercher par numéro ou client..."
                       class="w-full pl-12 pr-4 py-4 bg-white dark:bg-zinc-900 border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-primary dark:text-white" />
            </div>
        </div>

        <div class="flex gap-2 overflow-x-auto pb-4 -mx-4 px-4 scrollbar-hide py-2">
            <button class="px-5 py-2 rounded-full bg-primary text-primary-foreground text-xs font-bold whitespace-nowrap">Tous</button>
            <button class="px-5 py-2 rounded-full bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 text-gray-400 text-xs font-bold whitespace-nowrap">Actifs</button>
            <button class="px-5 py-2 rounded-full bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 text-gray-400 text-xs font-bold whitespace-nowrap">Archivés</button>
        </div>

        <div class="space-y-4 pb-20">
            <div v-for="contrat in contrats.data" :key="contrat.id"
                 class="group bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 transition-all hover:border-primary/50">
                <Link :href="`/contrats/${contrat.id}`" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900/20 flex items-center justify-center text-violet-600">
                                <FileText class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-lg tracking-tight">{{ contrat.numero }}</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ formatDate(contrat.date) }}</p>
                            </div>
                        </div>
                        <div class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm" :class="getStatusBadge(contrat.statut)">
                             {{ contrat.statut }}
                        </div>
                    </div>

                    <div class="space-y-3 pb-4 border-b border-gray-50 dark:border-zinc-800">
                        <div v-if="contrat.type === 'client'" class="flex items-center gap-2 text-gray-600 dark:text-zinc-400 font-bold">
                            <Users class="w-4 h-4 text-gray-400" />
                            <span class="text-xs truncate">{{ contrat.client?.nom_complet }}</span>
                        </div>
                        <div v-else class="flex items-center gap-2 text-gray-600 dark:text-zinc-400 font-bold">
                            <Briefcase class="w-4 h-4 text-gray-400" />
                            <span class="text-xs truncate">{{ contrat.prestataire?.nom_complet }}</span>
                        </div>
                        <div v-if="contrat.chantier" class="flex items-center gap-2 text-gray-500 dark:text-zinc-500 italic">
                            <HardHat class="w-4 h-4 text-gray-400" />
                            <span class="text-[10px] font-medium tracking-tight truncate">{{ contrat.chantier?.titre }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Montant Total</p>
                            <p class="text-xl font-black text-primary dark:text-white">{{ formatCurrency(contrat.montant_convenu).replace(',00', '') }}<span class="text-xs font-normal ml-1">FCFA</span></p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-50 dark:bg-zinc-800 flex items-center justify-center transition-transform group-hover:translate-x-1">
                            <ArrowRight class="w-5 h-5 text-gray-300" />
                        </div>
                    </div>
                </Link>
            </div>

            <div v-if="contrats.data.length === 0" class="py-20 text-center grayscale opacity-40">
                <FileText class="w-16 h-16 mx-auto mb-4" />
                <p class="text-xs uppercase font-bold tracking-widest">Aucun contrat enregistré</p>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="contrats.links?.length > 3" class="flex justify-center gap-1 pt-6 pb-20 overflow-x-auto scrollbar-hide">
            <template v-for="link in contrats.links" :key="link.label">
                <Link v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      class="px-4 py-2 rounded-xl text-xs font-bold"
                      :class="link.active ? 'bg-primary text-primary-foreground' : 'bg-white dark:bg-zinc-900 text-gray-600 shadow-sm border border-gray-100 dark:border-zinc-800'">
                </Link>
            </template>
        </div>
    </Layout>
</template>
