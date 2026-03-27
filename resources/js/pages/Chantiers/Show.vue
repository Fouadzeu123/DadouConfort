<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    Edit,
    FileText,
    TrendingUp,
    Briefcase,
    Calendar,
    Users,
    Trash2,
    Check,
    Phone,
    MapPin,
    ChevronRight,
    Plus
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    chantier: Object,
    prestatairesDisponibles: Array,
    margeEstimee: Number,
    totalRecettes: Number,
    totalDepenses: Number,
    totalPrestataires: Number,
});

const activeTab = ref('suivi'); // suivi, prestataires, finance

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'en_cours': return { label: 'En Cours', class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' };
        case 'en_attente': return { label: 'En Attente', class: 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400' };
        case 'termine': return { label: 'Terminé', class: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400' };
        case 'suspendu': return { label: 'Suspendu', class: 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400' };
        default: return { label: status, class: 'bg-gray-100 text-gray-700' };
    }
};

// Formulaire d'affectation de prestataire
const showingAffectationForm = ref(false);
const affectationForm = useForm({
    prestataire_id: '',
    role: '',
    montant_convenu: 0,
    montant_paye: 0,
    notes: '',
});

const submitAffectation = () => {
    affectationForm.post(`/chantiers/${props.chantier.id}/prestataires`, {
        onSuccess: () => {
            showingAffectationForm.value = false;
            affectationForm.reset();
        }
    });
};

const retirerPrestataire = (prestataireId) => {
    if (confirm('Retirer ce prestataire du chantier ?')) {
        router.delete(`/chantiers/${props.chantier.id}/prestataires/retirer`, {
            data: { prestataire_id: prestataireId }
        });
    }
};

// Formulaire d'édition de prestataire affecté (pivot)
const editingPivot = ref(null);
const editPivotForm = useForm({
    role: '',
    montant_convenu: 0,
    montant_paye: 0,
    notes: '',
});

const startEditPivot = (p) => {
    editingPivot.value = p.pivot.id;
    editPivotForm.role = p.pivot.role;
    editPivotForm.montant_convenu = p.pivot.montant_convenu;
    editPivotForm.montant_paye = p.pivot.montant_paye;
    editPivotForm.notes = p.pivot.notes;
};

const submitEditPivot = () => {
    editPivotForm.put(`/chantiers/${props.chantier.id}/prestataires/${editingPivot.value}`, {
        onSuccess: () => editingPivot.value = null
    });
};
</script>

<template>
    <Head :title="chantier.titre" />

    <Layout>
        <AppBar :title="chantier.titre" show-back-button back-href="/chantiers" :actions="[{ icon: Edit, href: `/chantiers/${chantier.id}/edit` }]" />

        <!-- Chantier Overview Header -->
        <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4 mb-4">
            <div class="flex justify-between items-start">
                <span class="text-[10px] py-1 px-4 rounded-full font-bold uppercase shadow-sm" :class="getStatusBadge(chantier.statut).class">
                    {{ getStatusBadge(chantier.statut).label }}
                </span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ chantier.numero }}</span>
            </div>
            
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">{{ chantier.titre }}</h1>
                <Link :href="`/clients/${chantier.client?.id}`" class="flex items-center gap-1.5 mt-2 text-primary font-bold text-sm">
                    <Users class="w-4 h-4" />
                    {{ chantier.client?.nom_complet }}
                    <ChevronRight class="w-3 h-3" />
                </Link>
            </div>

            <div class="flex flex-wrap gap-4 pt-2">
                <div class="flex items-center gap-2 text-gray-500 dark:text-zinc-500">
                    <MapPin class="w-4 h-4 text-primary" />
                    <span class="text-xs font-medium">{{ chantier.localisation || 'Non localisé' }}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-500 dark:text-zinc-500">
                    <Calendar class="w-4 h-4 text-primary" />
                    <span class="text-xs font-medium">{{ chantier.date_debut ? new Date(chantier.date_debut).toLocaleDateString('fr-FR') : 'Date libre' }}</span>
                </div>
            </div>
        </div>

        <!-- Tab Navigation (Custom Segmented Control) -->
        <div class="bg-gray-200/50 dark:bg-zinc-900/50 p-1.5 rounded-3xl grid grid-cols-3 mb-6 font-sans">
            <button v-for="tab in ['suivi', 'prestataires', 'finance']" :key="tab"
                    @click="activeTab = tab"
                    class="py-3 rounded-[1.25rem] text-[10px] font-bold uppercase tracking-widest transition-all"
                    :class="activeTab === tab ? 'bg-white dark:bg-zinc-800 text-primary shadow-sm dark:text-white' : 'text-gray-500 dark:text-zinc-500'">
                {{ tab }}
            </button>
        </div>

        <!-- TAB CONTENT: SUIVI (Documents & Notes) -->
        <div v-if="activeTab === 'suivi'" class="space-y-6 animate-in fade-in slide-in-from-right-2 duration-300">
            <!-- Related Documents Section -->
            <section class="space-y-3">
                <div class="flex items-center justify-between px-1">
                    <h3 class="font-bold dark:text-white flex items-center gap-2"><FileText class="w-4 h-4 text-indigo-500" /> Documents liés</h3>
                </div>
                <!-- Contrât link -->
                <div v-if="chantier.contrat" class="bg-indigo-50 dark:bg-indigo-950/20 rounded-2xl p-4 flex items-center justify-between border border-indigo-100 dark:border-indigo-900/30">
                    <div class="flex items-center gap-3">
                        <FileText class="w-6 h-6 text-indigo-500" />
                        <div>
                            <p class="text-[10px] font-bold text-indigo-400 uppercase">Contrat Principal</p>
                            <p class="text-sm font-bold text-indigo-900 dark:text-indigo-200">{{ chantier.contrat.numero }}</p>
                        </div>
                    </div>
                    <Link :href="`/contrats/${chantier.contrat.id}`" class="p-2 bg-white dark:bg-zinc-800 rounded-xl shadow-sm">
                        <ChevronRight class="w-4 h-4 text-indigo-500" />
                    </Link>
                </div>
                <div v-else class="grid grid-cols-2 gap-3 pb-2">
                    <Link :href="`/contrats/create?chantier_id=${chantier.id}&type=client`" 
                          class="flex flex-col items-center gap-2 p-4 bg-primary/5 dark:bg-primary/10 border border-dashed border-primary/30 rounded-2xl transition-all active:scale-95">
                        <Users class="w-5 h-5 text-primary" />
                        <span class="text-[9px] font-black uppercase text-primary tracking-widest text-center leading-tight">Contrat Client</span>
                    </Link>
                    <Link :href="`/contrats/create?chantier_id=${chantier.id}&type=prestataire`" 
                          class="flex flex-col items-center gap-2 p-4 bg-amber-500/5 dark:bg-amber-500/10 border border-dashed border-amber-500/30 rounded-2xl transition-all active:scale-95">
                        <Briefcase class="w-5 h-5 text-amber-500" />
                        <span class="text-[9px] font-black uppercase text-amber-600 dark:text-amber-500 tracking-widest text-center leading-tight">Sous-traitant</span>
                    </Link>
                </div>

                <!-- Devis List -->
                <div v-for="devi in chantier.devis" :key="devi.id"
                     class="bg-white dark:bg-zinc-900 rounded-2xl p-4 flex items-center justify-between border border-gray-100 dark:border-zinc-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <FileText class="w-6 h-6 text-gray-400" />
                        <div>
                            <p class="text-sm font-bold dark:text-white">{{ devi.numero }}</p>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">{{ devi.statut }}</p>
                        </div>
                    </div>
                    <Link :href="`/devis/${devi.id}`" class="p-2">
                        <ChevronRight class="w-4 h-4 text-gray-300" />
                    </Link>
                </div>
            </section>

            <!-- Notes & Description Section -->
            <section class="space-y-4">
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 dark:border-zinc-800 pb-2 flex justify-between">
                        Description des travaux
                    </h3>
                    <p class="text-sm dark:text-zinc-300 whitespace-pre-wrap leading-relaxed">{{ chantier.description || 'Pas de description.' }}</p>
                </div>
                <div v-if="chantier.notes" class="bg-amber-50 dark:bg-amber-950/20 rounded-3xl p-6 shadow-sm border border-amber-100 dark:border-amber-900/30 space-y-4">
                    <h3 class="text-xs font-bold text-amber-500 uppercase tracking-widest border-b border-amber-100/30 dark:border-amber-900/10 pb-2">
                        Notes internes
                    </h3>
                    <p class="text-sm text-amber-800 dark:text-amber-300 italic leading-relaxed">{{ chantier.notes }}</p>
                </div>
            </section>

            <p class="text-[10px] text-gray-400 uppercase text-center font-bold tracking-[0.2em] pt-12 pb-20">DadouConfort - Pilotage Chantier</p>
        </div>

        <!-- TAB CONTENT: PRESTATAIRES (Affectation et Rôles) -->
        <div v-if="activeTab === 'prestataires'" class="space-y-6 animate-in fade-in slide-in-from-right-2 duration-300 pb-20">
            <div class="flex items-center justify-between px-1">
                <h2 class="font-bold dark:text-white flex items-center gap-2"><Briefcase class="w-5 h-5 text-indigo-500" /> Intervenants</h2>
                <button @click="showingAffectationForm = true" class="p-2 bg-primary text-primary-foreground rounded-2xl shadow-lg active:scale-95 transition-all">
                    <Plus class="w-5 h-5" />
                </button>
            </div>

            <!-- Affectation Form Modal (Simple layer) -->
            <div v-if="showingAffectationForm" class="bg-white dark:bg-zinc-900 p-6 rounded-3xl shadow-2xl border border-primary/20 space-y-6 animate-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold dark:text-white">Affecter un prestataire</h3>
                    <button @click="showingAffectationForm = false" class="text-gray-400 p-1"><MinusCircle class="w-6 h-6" /></button>
                </div>
                <div class="space-y-4">
                    <select v-model="affectationForm.prestataire_id" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm dark:text-white font-bold">
                        <option value="">Choisir un prestataire...</option>
                        <option v-for="p in prestatairesDisponibles" :key="p.id" :value="p.id">{{ p.nom_complet }} ({{ p.specialite }})</option>
                    </select>
                    <input v-model="affectationForm.role" type="text" placeholder="Rôle (Ex: Finition vernis)" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm dark:text-white" />
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase block px-2">Montant convenu</span>
                            <input v-model="affectationForm.montant_convenu" type="number" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm dark:text-white" />
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase block px-2">Montant payé</span>
                            <input v-model="affectationForm.montant_paye" type="number" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm dark:text-white" />
                        </div>
                    </div>
                    <button @click="submitAffectation" :disabled="affectationForm.processing" class="w-full py-4 bg-primary text-primary-foreground rounded-2xl font-bold shadow-lg shadow-primary/20 transition-all active:scale-95 disabled:opacity-50">
                        Confirmer l'affectation
                    </button>
                </div>
            </div>

            <!-- List of Affectated Providers -->
            <div class="space-y-4">
                <div v-for="p in chantier.prestataires" :key="p.id" 
                     class="bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-zinc-800 transition-all"
                     :class="{ 'ring-2 ring-primary ring-inset': editingPivot === p.pivot.id }">
                    <!-- Provider Header -->
                    <div class="p-5 flex items-center justify-between bg-gray-50/50 dark:bg-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-black">
                                {{ p.nom_complet.substring(0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold dark:text-white">{{ p.nom_complet }}</h4>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ p.specialite }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                             <a :href="`tel:${p.telephone}`" class="p-2 text-primary hover:bg-white dark:hover:bg-zinc-800 rounded-xl transition-colors">
                                <Phone class="w-4 h-4" />
                             </a>
                             <button @click="editingPivot ? editingPivot = null : startEditPivot(p)" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                <Edit class="w-4 h-4" />
                             </button>
                             <button @click="retirerPrestataire(p.id)" class="p-2 text-gray-300 hover:text-rose-500 transition-colors">
                                <Trash2 class="w-4 h-4" />
                             </button>
                        </div>
                    </div>

                    <!-- View Mode -->
                    <div v-if="editingPivot !== p.pivot.id" class="p-5 space-y-4">
                        <div class="flex justify-between items-center text-xs">
                             <span class="text-gray-400 uppercase font-bold tracking-tighter">Mission</span>
                             <span class="font-bold dark:text-indigo-400">{{ p.pivot.role || 'Général' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-2">
                             <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Montant Convenu</p>
                                <p class="text-sm font-black dark:text-white">{{ formatCurrency(p.pivot.montant_convenu).replace(',00', '') }}</p>
                             </div>
                             <div class="space-y-1 text-right">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Reste à payer</p>
                                <p class="text-sm font-black transition-colors" :class="p.pivot.montant_convenu - p.pivot.montant_paye > 0 ? 'text-rose-500 animate-pulse' : 'text-emerald-500'">
                                    {{ formatCurrency(p.pivot.montant_convenu - p.pivot.montant_paye).replace(',00', '') }}
                                </p>
                             </div>
                        </div>
                        <!-- Simple Progress Bar -->
                        <div class="h-1.5 w-full bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 transition-all duration-500" :style="`width: ${Math.min(100, (p.pivot.montant_paye / p.pivot.montant_convenu) * 100)}%`"></div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div v-else class="p-5 space-y-4 animate-in slide-in-from-top-2 duration-200">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-indigo-500 uppercase px-2">Mission</label>
                            <input v-model="editPivotForm.role" type="text" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm dark:text-white" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-400 uppercase px-2">Convenu</label>
                                <input v-model="editPivotForm.montant_convenu" type="number" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm dark:text-white" />
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-400 uppercase px-2">Payé</label>
                                <input v-model="editPivotForm.montant_paye" type="number" class="w-full p-4 bg-gray-50 dark:bg-zinc-800 rounded-2xl border-none text-sm dark:text-white" />
                            </div>
                        </div>
                        <div class="flex gap-2">
                             <button @click="submitEditPivot" :disabled="editPivotForm.processing" class="flex-1 py-4 bg-primary text-primary-foreground rounded-2xl font-bold flex items-center justify-center gap-2">
                                <Check class="w-5 h-5 text-emerald-400" /> Confirmer
                             </button>
                             <button @click="editingPivot = null" class="px-6 py-4 bg-gray-100 dark:bg-zinc-800 text-gray-500 rounded-2xl font-bold">Annuler</button>
                        </div>
                    </div>
                </div>

                <div v-if="chantier.prestataires?.length === 0" class="py-20 text-center space-y-4 grayscale opacity-40">
                    <Users class="w-16 h-16 mx-auto text-gray-300" />
                    <p class="text-xs uppercase font-bold tracking-widest">Aucun prestataire affecté</p>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: FINANCE (Recettes, Dépenses et Marge) -->
        <div v-if="activeTab === 'finance'" class="space-y-6 animate-in fade-in slide-in-from-right-2 duration-300 pb-20">
            <!-- Global Profit Card -->
            <div class="bg-gradient-to-br from-zinc-900 to-black dark:from-zinc-100 dark:to-white p-8 rounded-[2.5rem] text-white dark:text-zinc-900 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10">
                    <TrendingUp class="w-48 h-48" />
                </div>
                <div class="relative z-10 space-y-6">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold uppercase tracking-widest opacity-60">Marge estimée</span>
                        <div class="px-3 py-1 bg-emerald-500/20 rounded-full border border-emerald-500/30 text-emerald-400 text-[10px] font-bold uppercase">Rentable</div>
                    </div>
                    <div>
                        <p class="text-4xl font-black mb-1">{{ formatCurrency(margeEstimee).replace(',00', '') }}<span class="text-xl font-normal opacity-60 ml-2">FCFA</span></p>
                        <p class="text-[10px] font-bold opacity-60 uppercase tracking-widest">Basée sur les recettes et dépenses actuelles</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/10 dark:border-zinc-200">
                        <div class="space-y-1">
                            <p class="text-[9px] font-bold opacity-50 uppercase">Total Recettes</p>
                            <p class="text-sm font-bold text-emerald-400 animate-pulse ">+{{ formatCurrency(totalRecettes).replace(',00', '') }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[9px] font-bold opacity-50 uppercase">Total Sorties</p>
                            <p class="text-sm font-bold text-rose-400">{{ formatCurrency(totalDepenses + totalPrestataires).replace(',00', '') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Finance breakdowns -->
            <div class="space-y-4">
                <!-- Expenses Section -->
                <div class="flex items-center justify-between px-1">
                    <h3 class="font-bold dark:text-white flex items-center gap-2"><CreditCard class="w-5 h-5 text-rose-500" /> Dépenses matériaux</h3>
                    <Link :href="`/depenses/create?chantier_id=${chantier.id}`" class="text-primary text-xs font-bold uppercase underline">Ajouter</Link>
                </div>
                <div class="bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden border border-gray-100 dark:border-zinc-800">
                    <div v-for="d in chantier.depenses" :key="d.id" class="p-4 flex justify-between items-center border-b border-gray-50 last:border-0 dark:border-zinc-800/50">
                        <div>
                            <p class="text-sm font-bold dark:text-white">{{ d.titre }}</p>
                            <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest">{{ d.categorie }}</p>
                        </div>
                        <span class="text-sm font-black text-rose-500">-{{ formatCurrency(d.montant).replace(',00', '') }}</span>
                    </div>
                    <div v-if="!chantier.depenses?.length" class="p-8 text-center text-gray-400 text-xs italic">Aucune dépense enregistrée.</div>
                </div>

                <!-- Recettes (Payments from client) -->
                <div class="flex items-center justify-between px-1 pt-4">
                    <h3 class="font-bold dark:text-white flex items-center gap-2"><CreditCard class="w-5 h-5 text-emerald-500" /> Recettes clients</h3>
                    <Link :href="`/paiements/create?chantier_id=${chantier.id}&type=recu_client&client_id=${chantier.client_id}`" class="text-primary text-xs font-bold uppercase underline">Ajouter</Link>
                </div>
                <div class="bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden border border-gray-100 dark:border-zinc-800">
                    <div v-for="p in chantier.paiements.filter(pay => pay.type === 'recu_client')" :key="p.id" class="p-4 flex justify-between items-center border-b border-gray-50 last:border-0 dark:border-zinc-800/50">
                        <div>
                            <p class="text-sm font-bold dark:text-white">Paiement Reçu</p>
                            <p class="text-[9px] text-gray-400 uppercase font-bold">{{ new Date(p.date).toLocaleDateString('fr-FR') }}</p>
                        </div>
                        <span class="text-sm font-black text-emerald-500">+{{ formatCurrency(p.montant).replace(',00', '') }}</span>
                    </div>
                    <div v-if="!chantier.paiements.filter(pay => pay.type === 'recu_client').length" class="p-8 text-center text-gray-400 text-xs italic">Aucun paiement reçu.</div>
                </div>
            </div>
        </div>
    </Layout>
</template>
