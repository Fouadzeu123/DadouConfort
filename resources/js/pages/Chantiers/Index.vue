<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import AppBar from '@/components/AppBar.vue';
import { 
    Search, 
    Plus, 
    HardHat, 
    MapPin, 
    Calendar, 
    Users, 
    ChevronRight,
    TrendingUp,
    Filter
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    chantiers: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get('/chantiers', { search: value }, { preserveState: true, replace: true });
});

const getStatusBadge = (status) => {
    switch (status) {
        case 'en_cours': return { label: 'En Cours', class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' };
        case 'en_attente': return { label: 'En Attente', class: 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400' };
        case 'termine': return { label: 'Terminé', class: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400' };
        case 'suspendu': return { label: 'Suspendu', class: 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400' };
        default: return { label: status, class: 'bg-gray-100 text-gray-700' };
    }
};

const formatDate = (date) => {
    if (!date) return 'Date libre';
    return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
};
</script>

<template>
    <Head title="Gestion des Chantiers" />

    <Layout>
        <AppBar title="Chantiers" :actions="[{ icon: Plus, href: '/chantiers/create' }]" />

        <div class="space-y-4">
            <!-- Search -->
            <div class="relative group">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500 group-focus-within:text-primary transition-colors" />
                <input v-model="search"
                       type="text" 
                       placeholder="Rechercher par titre ou client..."
                       class="w-full pl-12 pr-4 py-4 bg-white dark:bg-zinc-900 border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-primary dark:text-white transition-all" />
            </div>
        </div>

        <!-- Filter Bubbles -->
        <div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide">
            <button class="px-4 py-2 rounded-full bg-primary text-primary-foreground text-xs font-bold whitespace-nowrap">Tous</button>
            <button class="px-4 py-2 rounded-full bg-white dark:bg-zinc-900 text-gray-500 border border-gray-100 dark:border-zinc-800 text-xs font-bold whitespace-nowrap">En Cours</button>
            <button class="px-4 py-2 rounded-full bg-white dark:bg-zinc-900 text-gray-500 border border-gray-100 dark:border-zinc-800 text-xs font-bold whitespace-nowrap">En Attente</button>
            <button class="px-4 py-2 rounded-full bg-white dark:bg-zinc-900 text-gray-500 border border-gray-100 dark:border-zinc-800 text-xs font-bold whitespace-nowrap">Terminés</button>
        </div>

        <!-- Chantiers List -->
        <div class="space-y-4 pb-20">
            <div v-for="chantier in chantiers.data" :key="chantier.id" 
                 class="group bg-white dark:bg-zinc-900 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800 hover:border-primary/50 transition-colors">
                <Link :href="`/chantiers/${chantier.id}`" class="block">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/20 flex items-center justify-center font-bold text-amber-500">
                                <HardHat class="w-6 h-6" />
                            </div>
                            <div class="max-w-[180px]">
                                <h3 class="font-bold text-lg dark:text-white truncate group-hover:text-primary transition-colors">{{ chantier.titre }}</h3>
                                <div class="flex items-center gap-1 text-gray-400 dark:text-zinc-500 font-bold uppercase text-[10px] tracking-widest">
                                    {{ chantier.numero }}
                                </div>
                            </div>
                        </div>
                        <span class="text-[10px] py-0.5 px-3 rounded-full font-bold uppercase transition-all shadow-sm" :class="getStatusBadge(chantier.statut).class">
                            {{ getStatusBadge(chantier.statut).label }}
                        </span>
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-zinc-400">
                            <div class="w-6 h-6 rounded-full bg-gray-50 dark:bg-zinc-800 flex items-center justify-center">
                                <Users class="w-3.5 h-3.5 text-gray-400" />
                            </div>
                            <span class="text-sm font-medium">{{ chantier.client?.nom_complet || 'Aucun client' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500 dark:text-zinc-500">
                            <div class="w-6 h-6 rounded-full bg-gray-50 dark:bg-zinc-800 flex items-center justify-center">
                                <MapPin class="w-3.5 h-3.5 text-gray-400" />
                            </div>
                            <span class="text-xs truncate max-w-[200px]">{{ chantier.localisation || 'Lieu inconnu' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-50 dark:border-zinc-800">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-gray-400 dark:text-zinc-600 text-xs font-bold uppercase tracking-widest">
                                <Calendar class="w-3.5 h-3.5" />
                                <span>{{ formatDate(chantier.date_debut) }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-primary dark:text-primary/70 text-xs font-bold uppercase tracking-widest">
                                <Users class="w-3.5 h-3.5" />
                                <span>{{ chantier.prestataires_count || 0 }} Prestataires</span>
                            </div>
                        </div>
                        <ChevronRight class="w-5 h-5 text-gray-300 dark:text-zinc-600 group-hover:translate-x-1 transition-all" />
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-if="chantiers.data.length === 0" class="py-20 text-center space-y-4">
                <div class="w-20 h-20 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto opacity-30 shadow-inner">
                    <HardHat class="w-10 h-10 text-gray-500" />
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white capitalize">Aucun chantier à l'horizon</h3>
                <p class="text-gray-500 text-sm">Organisez vos travaux et gérez vos chantiers ici.</p>
                <Link href="/chantiers/create" class="inline-flex items-center gap-2 text-primary font-bold uppercase text-xs tracking-widest transform transition active:scale-95">
                    Démarrer un chantier <Plus class="w-4 h-4" />
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="chantiers.links?.length > 3" class="flex justify-center gap-1 pt-6 pb-20 overflow-x-auto scrollbar-hide">
            <template v-for="link in chantiers.links" :key="link.label">
                <Link v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      class="px-4 py-2 rounded-xl text-xs font-bold shadow-sm"
                      :class="link.active ? 'bg-primary text-primary-foreground transform scale-110' : 'bg-white dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 hover:bg-gray-50'">
                </Link>
            </template>
        </div>
    </Layout>
</template>
