<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import AppBar from '@/components/AppBar.vue';
import { 
    Plus, 
    FileText, 
    Users, 
    HardHat, 
    CreditCard, 
    TrendingUp, 
    AlertCircle,
    CheckCircle2,
    Clock,
    UserPlus,
    FilePlus,
    Hammer,
    ArrowRight,
    Settings
} from 'lucide-vue-next';

const props = defineProps({
    devisStats: Object,
    chantiersStats: Object,
    totalARecevoir: Number,
    totalAPayerPrestataires: Number,
    depensesRecentes: Array,
    contratsActifs: Number,
    totalClients: Number,
    totalPrestataires: Number,
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};

const STATS_CARDS = [
  { label: 'À recevoir', value: props.totalARecevoir, icon: TrendingUp, color: 'text-emerald-600 dark:text-emerald-400', bg: 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100/50' },
  { label: 'À payer', value: props.totalAPayerPrestataires, icon: AlertCircle, color: 'text-amber-600 dark:text-amber-400', bg: 'bg-amber-50 dark:bg-amber-950/20 border-amber-100/50' },
  { label: 'Contrats actifs', value: props.contratsActifs, icon: FileText, color: 'text-primary dark:text-primary', bg: 'bg-primary/5 dark:bg-primary/10 border-primary/10' },
  { label: 'En cours', value: props.chantiersStats?.en_cours || 0, icon: HardHat, color: 'text-sky-600 dark:text-sky-400', bg: 'bg-sky-50 dark:bg-sky-950/20 border-sky-100/50' },
];

const QUICK_ACTIONS = [
    { label: 'Client', icon: UserPlus, href: '/clients/create', color: 'bg-zinc-800 dark:bg-zinc-800 border-zinc-700/50' },
    { label: 'Devis', icon: FilePlus, href: '/devis/create', color: 'bg-primary border-primary/20 shadow-primary/20' },
    { label: 'Contrat', icon: FilePlus, href: '/contrats/create', color: 'bg-zinc-800 dark:bg-zinc-800 border-zinc-700/50' },
    { label: 'Chantier', icon: Hammer, href: '/chantiers/create', color: 'bg-zinc-800 dark:bg-zinc-800 border-zinc-700/50' },
    { label: 'Paiement', icon: CreditCard, href: '/paiements/create', color: 'bg-primary border-primary/20 shadow-primary/20' },
];
</script>

<template>
    <Head title="Tableau de Bord" />

    <Layout>
        <AppBar title="Accueil" :actions="[{ icon: Settings, href: '/settings/profile' }]" />

        <!-- Finances Overview Scroll -->
        <div class="flex gap-4 overflow-x-auto pb-4 -mx-4 px-4 scrollbar-hide">
            <div v-for="card in STATS_CARDS" 
                 :key="card.label"
                 class="min-w-[160px] flex-1 rounded-3xl p-5 border shadow-sm transition-transform active:scale-95 cursor-pointer"
                 :class="card.bg">
                <div class="flex justify-between items-start mb-4">
                    <component :is="card.icon" :class="['w-6 h-6', card.color]" />
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">{{ card.label }}</p>
                    <p class="text-xl font-bold dark:text-white">
                        {{ typeof card.value === 'number' && card.value > 1000 ? formatCurrency(card.value) : card.value }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Actions Grid -->
        <section>
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-gray-900 dark:text-white">Nouveau...</h3>
            </div>
            <div class="grid grid-cols-5 gap-3">
                <Link v-for="action in QUICK_ACTIONS" 
                      :key="action.label"
                      :href="action.href"
                      class="flex flex-col items-center gap-2">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center border shadow-lg transform transition active:scale-90" :class="action.color">
                        <component :is="action.icon" class="w-6 h-6 text-white" />
                    </div>
                    <span class="text-[10px] font-bold text-gray-700 dark:text-zinc-300 uppercase">{{ action.label }}</span>
                </Link>
            </div>
        </section>

        <!-- Status Summary -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Devis Summary Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                <div class="flex items-center gap-2 mb-4">
                    <FileText class="w-5 h-5 text-indigo-500" />
                    <h3 class="font-bold dark:text-white">Devis</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-zinc-400">Acceptés</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ devisStats.accepte }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-zinc-400">En attente</span>
                        <span class="font-bold text-amber-600 dark:text-amber-400">{{ devisStats.envoye }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-zinc-400 text-xs">Brouillons</span>
                        <span class="font-bold text-gray-400">{{ devisStats.brouillon }}</span>
                    </div>
                </div>
            </div>

            <!-- Chantiers Summary Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800">
                <div class="flex items-center gap-2 mb-4">
                    <HardHat class="w-5 h-5 text-amber-500" />
                    <h3 class="font-bold dark:text-white">Chantiers</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-zinc-400">Actifs</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ chantiersStats.en_cours }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-zinc-400">Terminés</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ chantiersStats.termine }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-zinc-400">Suspendus</span>
                        <span class="font-bold text-rose-500">{{ chantiersStats.suspendu }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <section>
            <div class="flex items-center justify-between mb-3 px-1">
                <h3 class="font-bold text-gray-900 dark:text-white">Dépenses récentes</h3>
                <Link href="/depenses" class="text-xs font-bold text-primary flex items-center gap-1 uppercase tracking-wider">
                    Voir tout <ArrowRight class="w-3 h-3" />
                </Link>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-gray-100 dark:border-zinc-800 overflow-hidden">
                <div v-for="(depense, index) in depensesRecentes" 
                     :key="depense.id"
                     class="flex items-center justify-between p-4"
                     :class="{ 'border-t border-gray-50 dark:border-zinc-800': index !== 0 }">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/30 flex items-center justify-center">
                            <TrendingUp class="w-5 h-5 text-rose-500 rotate-180" />
                        </div>
                        <div>
                            <p class="text-sm font-bold dark:text-white">{{ depense.titre }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-zinc-500">{{ depense.chantier?.titre || 'Général' }}</p>
                        </div>
                    </div>
                    <span class="font-bold text-rose-600 dark:text-rose-400">-{{ formatCurrency(depense.montant) }}</span>
                </div>
                <div v-if="depensesRecentes.length === 0" class="p-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-zinc-500">Aucune dépense récente.</p>
                </div>
            </div>
        </section>

    </Layout>
</template>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
