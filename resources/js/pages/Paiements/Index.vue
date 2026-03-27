<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import AppBar from '@/components/AppBar.vue';
import { 
    Plus, 
    Search, 
    CreditCard, 
    ArrowUpCircle, 
    ArrowDownCircle, 
    Calendar, 
    ChevronRight,
    Filter,
    ArrowRight
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

// ... (reste du script)
</script>

<template>
    <Head title="Journal des Paiements" />

    <Layout>
        <AppBar title="Paiements" :actions="[{ icon: Plus, href: '/paiements/create' }]" />

        <div class="space-y-4">
            <div class="relative group">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors" />
                <input v-model="search"
                       type="text" 
                       placeholder="Rechercher par client ou référence..."
                       class="w-full pl-12 pr-4 py-4 bg-white dark:bg-zinc-900 border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-primary dark:text-white" />
            </div>
        </div>

        <!-- Rapid Filter -->
        <div class="flex gap-2 overflow-x-auto pb-4 -mx-4 px-4 scrollbar-hide py-2">
            <button class="px-5 py-2 rounded-full bg-primary text-primary-foreground text-xs font-bold whitespace-nowrap shadow-md">Historique</button>
            <button class="px-5 py-2 rounded-full bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 text-gray-400 text-xs font-bold whitespace-nowrap">Recettes</button>
            <button class="px-5 py-2 rounded-full bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 text-gray-400 text-xs font-bold whitespace-nowrap">Sorties</button>
        </div>

        <!-- Paiements List -->
        <div class="space-y-3 pb-20">
            <div v-for="paiement in paiements.data" :key="paiement.id"
                 class="group bg-white dark:bg-zinc-900 rounded-[2rem] p-5 shadow-sm border border-gray-100 dark:border-zinc-800 flex items-center justify-between transition-all hover:shadow-xl hover:translate-x-1">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-[1.25rem] flex items-center justify-center transition-all group-hover:scale-110" :class="getPaiementType(paiement.type).bg">
                        <component :is="getPaiementType(paiement.type).icon" class="w-6 h-6" :class="getPaiementType(paiement.type).class" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                             <h3 class="text-sm font-black dark:text-white tracking-tight">{{ formatCurrency(paiement.montant).replace(',00', '') }}<span class="text-[9px] font-normal opacity-50 ml-1 italic font-sans uppercase">FCFA</span></h3>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest flex items-center gap-1">
                             {{ getPaiementType(paiement.type).label }} • {{ formatDate(paiement.date) }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                     <p class="text-[9px] font-black text-gray-300 dark:text-zinc-600 uppercase tracking-widest mb-1">{{ paiement.numero }}</p>
                     <div class="flex items-center gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                         <span class="text-[10px] font-bold dark:text-gray-400 truncate max-w-[120px]">{{ paiement.client?.nom_complet || paiement.prestataire?.nom_complet }}</span>
                         <ArrowRight class="w-3 h-3 text-gray-300 transform group-hover:translate-x-1" />
                     </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="paiements.data.length === 0" class="py-20 text-center grayscale opacity-30 bg-gray-50/50 dark:bg-zinc-900/50 rounded-[2.5rem] border-2 border-dashed border-gray-100 dark:border-zinc-800">
                <CreditCard class="w-16 h-16 mx-auto mb-4" />
                <p class="text-xs uppercase font-bold tracking-[0.2em]">Zéro transaction enregistrée</p>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="paiements.links?.length > 3" class="flex justify-center gap-1 pt-6 pb-20 overflow-x-auto scrollbar-hide">
            <template v-for="link in paiements.links" :key="link.label">
                <Link v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      class="px-4 py-2 rounded-xl text-xs font-bold transition-all"
                      :class="link.active ? 'bg-primary text-primary-foreground transform scale-110' : 'bg-white dark:bg-zinc-800 text-gray-400 border border-gray-50 dark:border-zinc-700/30'">
                </Link>
            </template>
        </div>
    </Layout>
</template>
