<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    Plus, 
    Search, 
    TrendingDown, 
    HardHat, 
    Calendar, 
    ChevronRight, 
    Trash2,
    Filter,
    ArrowRight,
    ShoppingBag
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    depenses: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get('/depenses', { search: value }, { preserveState: true, replace: true });
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
};

const deleteDepense = (id) => {
    if (confirm('Supprimer cette dépense ?')) {
        router.delete(`/depenses/${id}`);
    }
};
</script>

<template>
    <Head title="Suivi des Dépenses Matériaux" />

    <Layout>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black dark:text-white">Dépenses</h2>
                <Link href="/depenses/create" 
                      class="flex items-center gap-2 bg-rose-500 text-white px-5 py-2.5 rounded-2xl text-sm font-bold shadow-lg shadow-rose-500/20 active:scale-95 transition-all">
                    <Plus class="w-4 h-4" />
                    Enregistrer
                </Link>
            </div>

            <div class="relative group">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300 group-focus-within:text-rose-400 transition-colors" />
                <input v-model="search"
                       type="text" 
                       placeholder="Rechercher une dépense..."
                       class="w-full pl-12 pr-4 py-4 bg-white dark:bg-zinc-900 border-none rounded-3xl shadow-sm focus:ring-2 focus:ring-rose-500/30 dark:text-white" />
            </div>
        </div>

        <!-- Expense Stream -->
        <div class="space-y-4 pb-20 pt-4">
            <div v-for="depense in depenses.data" :key="depense.id"
                 class="group bg-white dark:bg-zinc-900 rounded-[2rem] p-5 shadow-sm border border-gray-100 dark:border-zinc-800 flex items-center justify-between transition-all hover:-translate-y-1 hover:shadow-xl">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-[1.25rem] bg-rose-50 dark:bg-rose-950/20 flex items-center justify-center text-rose-500 shadow-sm border border-rose-100/50 dark:border-rose-900/10">
                        <ShoppingBag class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-sm font-black dark:text-white tracking-tight leading-none mb-1">{{ depense.titre }}</h3>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                             {{ depense.categorie }} • {{ formatDate(depense.date) }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-black text-rose-600 dark:text-rose-400">-{{ formatCurrency(depense.montant).replace(',00', '') }}</p>
                    <div v-if="depense.chantier" class="flex items-center justify-end gap-1 opacity-50 text-gray-500">
                         <span class="text-[8px] font-bold uppercase tracking-tight truncate max-w-[100px]">{{ depense.chantier.titre }}</span>
                         <ArrowRight class="w-3 h-3" />
                    </div>
                    <button @click="deleteDepense(depense.id)" class="p-1 text-gray-200 hover:text-rose-500 transition-colors hidden group-hover:inline-block">
                         <Trash2 class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="depenses.data.length === 0" class="py-20 text-center grayscale opacity-30">
                <TrendingDown class="w-16 h-16 mx-auto opacity-20" />
                <p class="text-xs uppercase font-bold tracking-widest mt-4">Aucune dépense enregistrée</p>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="depenses.links?.length > 3" class="flex justify-center gap-1.5 pt-6 pb-20 overflow-x-auto scrollbar-hide">
            <template v-for="link in depenses.links" :key="link.label">
                <Link v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      class="px-4 py-2 rounded-xl text-xs font-bold transition-all"
                      :class="link.active ? 'bg-rose-500 text-white shadow-lg' : 'bg-white dark:bg-zinc-800 text-gray-400 shadow-sm border border-gray-100 dark:border-zinc-800'">
                </Link>
            </template>
        </div>
    </Layout>
</template>
