<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import AppBar from '@/components/AppBar.vue';
import { 
    Search, 
    Plus, 
    FileText, 
    ChevronRight, 
    Clock, 
    CheckCircle2, 
    XCircle,
    User,
    Calendar,
    ArrowRight,
    HardHat
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    devis: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

watch(search, (value) => {
    router.get('/devis', { search: value }, { preserveState: true, replace: true });
});

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'accepte': return { label: 'Accepté', icon: CheckCircle2, class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' };
        case 'envoye': return { label: 'Envoyé', icon: Clock, class: 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400' };
        case 'refuse': return { label: 'Refusé', icon: XCircle, class: 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400' };
        default: return { label: 'Brouillon', icon: FileText, class: 'bg-gray-100 text-gray-700 dark:bg-zinc-800 dark:text-zinc-400' };
    }
};
</script>

<template>
    <Head title="Gestion des Devis" />

    <Layout>
        <AppBar title="Devis" :actions="[{ icon: Plus, href: '/devis/create' }]" />

        <div class="space-y-4">
            <!-- Search -->
            <div class="relative group">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500 group-focus-within:text-primary transition-colors" />
                <input v-model="search"
                       type="text" 
                       placeholder="Rechercher par numéro ou client..."
                       class="w-full pl-12 pr-4 py-4 bg-white dark:bg-zinc-900 border-none rounded-3xl shadow-sm focus:ring-2 focus:ring-primary dark:text-white" />
            </div>
        </div>

        <!-- Filter Bubbles -->
        <div class="flex gap-2 overflow-x-auto pb-4 -mx-4 px-4 scrollbar-hide py-2">
            <button class="px-5 py-2 rounded-full bg-primary text-primary-foreground text-xs font-bold shadow-md shadow-primary/20">Tous</button>
            <button class="px-5 py-2 rounded-full bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 text-gray-500 text-xs font-bold">En attente</button>
            <button class="px-5 py-2 rounded-full bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 text-gray-500 text-xs font-bold">Accepté</button>
            <button class="px-5 py-2 rounded-full bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 text-gray-500 text-xs font-bold">Brouillon</button>
        </div>

        <!-- Devis Cards List -->
        <div class="space-y-4 pb-20">
            <div v-for="devi in devis.data" :key="devi.id"
                 class="group bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-xl transition-all">
                <Link :href="`/devis/${devi.id}`" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/20 flex items-center justify-center text-indigo-500">
                                <FileText class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ devi.numero }}</h3>
                                <div class="flex items-center gap-2">
                                    <Calendar class="w-3 h-3 text-gray-400" />
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ formatDate(devi.date) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest transition-all" :class="getStatusBadge(devi.statut).class">
                            <component :is="getStatusBadge(devi.statut).icon" class="w-3 h-3" />
                            {{ getStatusBadge(devi.statut).label }}
                        </div>
                    </div>

                    <div class="space-y-3 pb-4 border-b border-gray-50 dark:border-zinc-800">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-zinc-400">
                            <User class="w-4 h-4 text-gray-400" />
                            <span class="text-xs font-extrabold truncate">{{ devi.client?.nom_complet || 'Client Inconnu' }}</span>
                        </div>
                        <div v-if="devi.chantier" class="flex items-center gap-2 text-gray-500 dark:text-zinc-500">
                            <HardHat class="w-4 h-4 text-gray-400" />
                            <span class="text-[10px] font-bold uppercase tracking-tight truncate">{{ devi.chantier?.titre }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Total Devis</p>
                            <p class="text-xl font-black text-primary dark:text-white">{{ formatCurrency(devi.total_general).replace(',00', '') }}<span class="text-xs font-normal ml-1">FCFA</span></p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-50 dark:bg-zinc-800 flex items-center justify-center transition-transform group-hover:translate-x-1">
                            <ArrowRight class="w-5 h-5 text-gray-300" />
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-if="devis.data.length === 0" class="py-20 text-center bg-gray-200/20 dark:bg-zinc-900/40 rounded-[2.5rem] border border-dashed border-gray-100 dark:border-zinc-800 m-2">
                <div class="w-20 h-20 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100 dark:border-zinc-700">
                    <FileText class="w-10 h-10 text-gray-300" />
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">Aucun devis ici pour le moment</h3>
                <p class="text-xs text-gray-500 px-8 mt-2">Créez des devis professionnels et envoyez-les en un clin d'œil.</p>
                <Link href="/devis/create" class="inline-flex items-center gap-2 bg-primary text-white px-8 py-3 rounded-2xl mt-8 font-black text-xs uppercase shadow-xl active:scale-95">
                    Créer mon premier devis
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="devis.links?.length > 3" class="flex justify-center gap-1.5 pt-6 pb-20 overflow-x-auto scrollbar-hide">
            <template v-for="link in devis.links" :key="link.label">
                <Link v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      class="min-w-[40px] h-[40px] flex items-center justify-center rounded-xl text-xs font-bold"
                      :class="link.active ? 'bg-primary text-primary-foreground' : 'bg-white dark:bg-zinc-900 text-gray-500 shadow-sm border border-gray-100 dark:border-zinc-800'">
                </Link>
            </template>
        </div>
    </Layout>
</template>
