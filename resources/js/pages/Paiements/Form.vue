<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    ChevronLeft, 
    Save, 
    CreditCard, 
    Users, 
    Briefcase, 
    Calendar, 
    TrendingUp, 
    HardHat,
    ArrowLeft,
    Check,
    AlertCircle,
    ArrowDownCircle,
    ArrowUpCircle
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    paiement: { type: Object, default: null },
    clients: Array,
    prestataires: Array,
    chantiers: Array,
});

const page = usePage();
const queryParams = new URLSearchParams(window.location.search);
const defaultType = queryParams.get('type') || 'recu_client';
const defaultChantierId = queryParams.get('chantier_id') || '';
const defaultClientId = queryParams.get('client_id') || '';

const isEditing = !!props.paiement;

const form = useForm({
    type: props.paiement?.type ?? defaultType,
    client_id: props.paiement?.client_id ?? defaultClientId,
    prestataire_id: props.paiement?.prestataire_id ?? '',
    chantier_id: props.paiement?.chantier_id ?? defaultChantierId,
    montant: props.paiement?.montant ?? 0,
    date: props.paiement?.date?.split('T')[0] ?? new Date().toISOString().split('T')[0],
    mode_paiement: props.paiement?.mode_paiement ?? 'especes',
    reference_transaction: props.paiement?.reference_transaction ?? '',
    notes: props.paiement?.notes ?? '',
});

const submit = () => {
    if (isEditing) {
        form.put(`/paiements/${props.paiement.id}`);
    } else {
        form.post('/paiements');
    }
};

const MODE_PAIEMENTS = [
    { value: 'especes', label: 'Espèces' },
    { value: 'orange_money', label: 'Orange Money' },
    { value: 'mtn_momo', label: 'MTN MoMo' },
    { value: 'virement', label: 'Virement' },
    { value: 'cheque', label: 'Chèque' },
];
</script>

<template>
    <Head :title="isEditing ? 'Modifier Paiement' : 'Nouveau Paiement'" />

    <Layout>
        <!-- Top Sticky Toolbar -->
        <div class="flex items-center justify-between mb-4 sticky top-14 z-30 bg-gray-50/90 dark:bg-zinc-950/90 backdrop-blur-md py-4 -mx-4 px-4 shadow-sm border-b border-gray-100 dark:border-zinc-900 transition-all">
            <Link :href="isEditing ? `/paiements` : '/paiements'" class="p-2 bg-white dark:bg-zinc-900 rounded-full shadow-sm hover:scale-105 active:scale-95 transition-transform flex items-center justify-center">
                <ArrowLeft class="w-5 h-5 text-gray-500" />
            </Link>
            <h2 class="text-sm font-black dark:text-white uppercase tracking-[0.2em] leading-none">{{ isEditing ? 'Éditer Paiement' : 'Enregistrement' }}</h2>
            <button @click="submit" 
                    :disabled="form.processing"
                    class="bg-primary text-primary-foreground p-3 rounded-full shadow-lg shadow-primary/20 hover:scale-110 active:scale-95 disabled:opacity-50 transition-all flex items-center justify-center">
                <Check class="w-5 h-5" />
            </button>
        </div>

        <form @submit.prevent="submit" class="space-y-6 pb-20">
            <!-- Type Selector (Recette vs Dépense) -->
            <div class="bg-indigo-950 dark:bg-zinc-900 p-2 rounded-[2rem] grid grid-cols-2 gap-2 shadow-2xl relative overflow-hidden transition-all">
                <!-- Background visual -->
                <div class="absolute inset-0 opacity-5 pointer-events-none grayscale translate-x-1/2 translate-y-1/2">
                    <CreditCard class="w-40 h-40" />
                </div>
                <button type="button" @click="form.type = 'recu_client'"
                        class="relative z-10 py-5 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest flex flex-col items-center gap-2 transition-all shadow-inner"
                        :class="form.type === 'recu_client' ? 'bg-emerald-500 text-white animate-in zoom-in-95 scale-100' : 'bg-transparent text-gray-500 hover:text-white/60'">
                    <ArrowDownCircle class="w-6 h-6" />
                    Reçu Client
                </button>
                <button type="button" @click="form.type = 'paye_prestataire'"
                        class="relative z-10 py-5 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest flex flex-col items-center gap-2 transition-all shadow-inner"
                        :class="form.type === 'paye_prestataire' ? 'bg-indigo-500 text-white animate-in zoom-in-95 scale-100' : 'bg-transparent text-gray-400 hover:text-white/60'">
                    <ArrowUpCircle class="w-6 h-6" />
                    Payé Prestataire
                </button>
            </div>

            <!-- Subject (Client or Prestataire) -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4 animate-in slide-in-from-bottom-2 duration-300">
                <label v-if="form.type === 'recu_client'" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest px-1 flex items-center gap-2">
                    <Users class="w-3.5 h-3.5" /> Sélectionner le Client payeur
                </label>
                <label v-else class="text-[9px] font-black text-indigo-500 uppercase tracking-widest px-1 flex items-center gap-2">
                    <Briefcase class="w-3.5 h-3.5" /> Sélectionner le Prestataire payé
                </label>

                <div v-if="form.type === 'recu_client'">
                    <select v-model="form.client_id" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm dark:text-white font-bold appearance-none shadow-inner">
                        <option value="">-- Choisir un client --</option>
                        <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.nom_complet }}</option>
                    </select>
                </div>
                <div v-else>
                    <select v-model="form.prestataire_id" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm dark:text-white font-bold appearance-none shadow-inner">
                        <option value="">-- Choisir un prestataire --</option>
                        <option v-for="p in prestataires" :key="p.id" :value="p.id">{{ p.nom_complet }}</option>
                    </select>
                </div>

                <!-- Related Chantier -->
                <div class="pt-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1 flex items-center gap-2 cursor-pointer transition-opacity hover:opacity-100 opacity-60">
                        <HardHat class="w-3.5 h-3.5" /> Chantier associé (Facultatif)
                    </label>
                    <select v-model="form.chantier_id" class="w-full p-4 bg-gray-50/50 dark:bg-zinc-800/50 rounded-2xl border-none text-[11px] dark:text-zinc-500 font-bold appearance-none mt-2 shadow-inner">
                        <option value="">-- Aucun chantier --</option>
                        <option v-for="ch in chantiers" :key="ch.id" :value="ch.id">{{ ch.titre }}</option>
                    </select>
                </div>
            </div>

            <!-- Amount Section -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-8 shadow-sm border border-gray-100 dark:border-zinc-800 animate-in slide-in-from-bottom-5 duration-500">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block text-center mb-6">Montant du versement</label>
                <div class="relative group">
                    <input v-model="form.montant" type="number" 
                           class="w-full bg-gray-100 dark:bg-zinc-800/80 border-none rounded-[1.5rem] p-6 text-center text-4xl font-black dark:text-white transition-all focus:ring-4 focus:ring-primary/5 shadow-inner" />
                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black opacity-30 uppercase tracking-widest pointer-events-none group-focus-within:opacity-10 transition-opacity font-serif italic">XAF</span>
                </div>
                <p v-if="form.montant > 0" class="text-center text-[10px] text-gray-400 mt-4 uppercase font-bold transition-all animate-pulse">
                     Valider ce montant pour confirmation financière
                </p>
            </div>

            <!-- Date & Mode -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Date Valeur</label>
                        <input v-model="form.date" type="date" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-xs font-bold dark:text-white shadow-inner" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Moyen / Mode</label>
                        <select v-model="form.mode_paiement" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-[10px] font-black uppercase dark:text-white shadow-inner appearance-none tracking-tighter">
                            <option v-for="mode in MODE_PAIEMENTS" :key="mode.value" :value="mode.value">{{ mode.label }}</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Référence Transaction / Chèque</label>
                    <input v-model="form.reference_transaction" type="text" placeholder="Ex: TX-99283-882" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm font-medium dark:text-white shadow-inner" />
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white dark:bg-zinc-900 rounded-[1.75rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1 block mb-2">Notes & Précision</label>
                <textarea v-model="form.notes" rows="3" placeholder="Informations complémentaires..." 
                          class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-xs dark:text-white shadow-inner transition-all"></textarea>
            </div>

            <p class="text-[8px] font-bold text-gray-300 uppercase text-center tracking-[0.3em] py-10 opacity-60">Suivi des fonds - DadouConfort</p>
        </form>
    </Layout>
</template>
