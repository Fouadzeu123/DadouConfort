<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import AppBar from '@/components/AppBar.vue';
import { 
    Save, 
    FileText, 
    Users, 
    Briefcase,
    HardHat, 
    Calendar,
    RefreshCw,
    ShieldCheck,
    CreditCard
} from 'lucide-vue-next';
import { computed, watch } from 'vue';

const props = defineProps({
    contrat: Object,
    clients: Array,
    prestataires: Array,
    chantiers: Array,
    devis: Array,
    nextNumero: String,
    initialType: String,
    initialChantierId: [String, Number],
});

const isEditing = computed(() => !!props.contrat);

const form = useForm({
    type: props.contrat?.type || props.initialType || 'client',
    client_id: props.contrat?.client_id || (props.initialType === 'client' ? props.initialChantierId : '') || '',
    prestataire_id: props.contrat?.prestataire_id || '',
    chantier_id: props.contrat?.chantier_id || props.initialChantierId || '',
    devis_id: props.contrat?.devis_id || '',
    objet: props.contrat?.objet || '',
    description_travaux: props.contrat?.description_travaux || '',
    montant_convenu: props.contrat?.montant_convenu || 0,
    acompte: props.contrat?.acompte || 0,
    date_signature: props.contrat?.date_signature || new Date().toISOString().substr(0, 10),
    date_debut: props.contrat?.date_debut || '',
    date_fin_estimee: props.contrat?.date_fin_estimee || '',
    conditions_paiement: props.contrat?.conditions_paiement || '',
    clauses: props.contrat?.clauses || '',
    statut: props.contrat?.statut || 'brouillon',
});

// Watch type to refresh number
watch(() => form.type, (newType) => {
    if (!isEditing.value) {
        router.reload({ 
            data: { type: newType },
            only: ['nextNumero'],
            onSuccess: (page) => {
               // Update is handled by props
            }
        });
    }
});

const currentNumero = computed(() => isEditing.value ? props.contrat.numero : props.nextNumero);

const submit = () => {
    if (isEditing.value) {
        form.put(`/contrats/${props.contrat.id}`);
    } else {
        form.post('/contrats');
    }
};

const labels = computed(() => {
    return form.type === 'client' 
        ? { party: 'Client (Maître d\'ouvrage)', party_id: 'client_id', list: props.clients }
        : { party: 'Prestataire (Sous-traitant)', party_id: 'prestataire_id', list: props.prestataires };
});
</script>

<template>
    <Head :title="isEditing ? 'Modifier Contrat' : 'Nouveau Contrat'" />

    <Layout>
        <AppBar :title="isEditing ? 'Modifier Contrat' : 'Nouveau Contrat'" 
                show-back-button 
                back-href="/contrats" 
                :actions="[{ icon: Save, onClick: submit, class: 'bg-primary text-primary-foreground rounded-full' }]" />

        <form @submit.prevent="submit" class="space-y-6 pb-32">
            <!-- Type & Number info -->
            <div class="bg-zinc-900 text-white rounded-[2rem] p-6 shadow-xl relative overflow-hidden">
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <p class="text-[9px] font-black text-primary uppercase tracking-widest mb-1">Numéro de référence</p>
                        <p class="text-2xl font-black tracking-tighter">{{ currentNumero }}</p>
                    </div>
                    <div class="p-4 bg-white/10 rounded-2xl backdrop-blur-md border border-white/10">
                        <FileText class="w-6 h-6 text-primary" />
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 opacity-10">
                    <ShieldCheck class="w-40 h-40" />
                </div>
            </div>

            <!-- Type Selector -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-2 shadow-sm border border-gray-100 dark:border-zinc-800 flex gap-2">
                <button type="button" 
                        @click="form.type = 'client'; form.prestataire_id = ''"
                        class="flex-1 py-4 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2"
                        :class="form.type === 'client' ? 'bg-primary text-white shadow-lg' : 'text-gray-400'">
                    <Users class="w-4 h-4" /> Client
                </button>
                <button type="button" 
                        @click="form.type = 'prestataire'; form.client_id = ''"
                        class="flex-1 py-4 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2"
                        :class="form.type === 'prestataire' ? 'bg-amber-500 text-white shadow-lg' : 'text-gray-400'">
                    <Briefcase class="w-4 h-4" /> Prestataire
                </button>
            </div>

            <!-- Main Parties -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">{{ labels.party }}</label>
                    <div class="relative group">
                        <component :is="form.type === 'client' ? Users : Briefcase" class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors" />
                        <select v-model="form[labels.party_id]" 
                                class="w-full pl-14 pr-4 py-5 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary dark:text-white appearance-none transition-all">
                            <option value="">Sélectionner une partie...</option>
                            <option v-for="item in labels.list" :key="item.id" :value="item.id">{{ item.nom_complet }}</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Chantier Concerné</label>
                    <div class="relative group">
                        <HardHat class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors" />
                        <select v-model="form.chantier_id" 
                                class="w-full pl-14 pr-4 py-5 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary dark:text-white appearance-none transition-all">
                            <option value="">Associer un projet (optionnel)</option>
                            <option v-for="chantier in chantiers" :key="chantier.id" :value="chantier.id">{{ chantier.numero }} - {{ chantier.titre }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Financial Details -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-500">
                        <CreditCard class="w-5 h-5" />
                    </div>
                    <h3 class="font-black text-gray-900 dark:text-white uppercase tracking-tighter">Conditions Financières</h3>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Montant Global (XAF)</label>
                        <input v-model="form.montant_convenu" 
                               type="number" 
                               placeholder="0"
                               class="w-full px-6 py-5 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl text-2xl font-black focus:ring-2 focus:ring-primary dark:text-white text-right transition-all" />
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Acompte à la signature</label>
                        <input v-model="form.acompte" 
                               type="number" 
                               placeholder="0"
                               class="w-full px-6 py-5 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl text-lg font-black focus:ring-2 focus:ring-amber-500/20 text-amber-600 dark:text-amber-400 text-right transition-all" />
                    </div>
                </div>
            </div>

            <!-- Object of Contract -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Libellé des Travaux</label>
                    <input v-model="form.objet" 
                           type="text" 
                           placeholder="Ex: Fabrication de portes massives..."
                           class="w-full px-6 py-5 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary dark:text-white transition-all" />
                </div>
            </div>

            <!-- Planning -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-zinc-800 grid grid-cols-1 gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 mb-1">
                        <Calendar class="w-4 h-4 text-primary" />
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date de Début</label>
                    </div>
                    <input v-model="form.date_debut" type="date" class="w-full p-5 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-primary/20" />
                </div>
                <div class="space-y-2">
                    <div class="flex items-center gap-2 mb-1">
                        <Calendar class="w-4 h-4 text-amber-500" />
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date de Fin estimée</label>
                    </div>
                    <input v-model="form.date_fin_estimee" type="date" class="w-full p-5 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-primary/20" />
                </div>
            </div>

            <!-- Action Button -->
            <div class="fixed bottom-28 left-6 right-6 z-50">
                <button type="submit" 
                        :disabled="form.processing"
                        class="w-full py-6 bg-primary text-white rounded-[2rem] font-black text-xs uppercase tracking-[0.4em] flex items-center justify-center gap-4 active:scale-95 transition-all shadow-2xl shadow-primary/40 group">
                    <Save v-if="!form.processing" class="w-5 h-5 group-hover:rotate-12 transition-transform" />
                    <RefreshCw v-else class="w-5 h-5 animate-spin" />
                    {{ isEditing ? 'Appliquer les changements' : 'Générer le contrat Ohada' }}
                </button>
            </div>
        </form>
    </Layout>
</template>

<style scoped>
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.25rem center;
    background-size: 1rem;
}
</style>
