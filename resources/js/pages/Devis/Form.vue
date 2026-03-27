<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    ChevronLeft, 
    Save, 
    Plus, 
    Trash2, 
    FileText, 
    Users, 
    HardHat, 
    Settings,
    ArrowLeft,
    Check,
    MinusCircle
} from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    devis: { type: Object, default: null },
    clients: Array,
    chantiers: Array,
    services: Array,
});

const isEditing = !!props.devis;

const form = useForm({
    client_id: props.devis?.client_id ?? '',
    chantier_id: props.devis?.chantier_id ?? '',
    date: props.devis?.date?.split('T')[0] ?? new Date().toISOString().split('T')[0],
    statut: props.devis?.statut ?? 'brouillon',
    remise: props.devis?.remise ?? 0,
    frais_transport: props.devis?.frais_transport ?? 0,
    main_oeuvre: props.devis?.main_oeuvre ?? 0,
    notes: props.devis?.notes ?? '',
    lignes: props.devis?.lignes ?? [],
});

// Calculate totals reactively
const totalHT = computed(() => {
    return form.lignes.reduce((sum, ligne) => sum + (ligne.quantite * ligne.prix_unitaire), 0);
});

const totalGeneral = computed(() => {
    return (totalHT.value - (Number(form.remise) || 0)) + (Number(form.frais_transport) || 0) + (Number(form.main_oeuvre) || 0);
});

const addLigne = () => {
    form.lignes.push({
        designation: '',
        quantite: 1,
        unite: 'unité',
        prix_unitaire: 0,
    });
};

const removeLigne = (index) => {
    form.lignes.splice(index, 1);
};

const selectService = (index, service) => {
    form.lignes[index].designation = service.nom;
    form.lignes[index].unite = service.unite;
    form.lignes[index].prix_unitaire = service.prix_indicatif;
};

const submit = () => {
    if (isEditing) {
        form.put(`/devis/${props.devis.id}`);
    } else {
        form.post('/devis');
    }
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR').format(val);
};

// Autocomplete logic for services
const showingServicePicker = ref(null);
</script>

<template>
    <Head :title="isEditing ? 'Modifier Devis' : 'Nouveau Devis'" />

    <Layout>
        <!-- Top Toolbar Sticky -->
        <div class="flex items-center justify-between mb-4 sticky top-14 z-30 bg-gray-50 dark:bg-zinc-950 py-3 -mx-4 px-4 shadow-sm border-b border-gray-100 dark:border-zinc-900 transition-all">
            <Link :href="isEditing ? `/devis/${devis.id}` : '/devis'" class="flex items-center gap-2 text-gray-500 hover:text-primary transition-colors">
                <div class="p-2 bg-white dark:bg-zinc-900 rounded-full shadow-sm">
                    <ArrowLeft class="w-5 h-5" />
                </div>
                <span class="font-black text-xs uppercase tracking-widest">Annuler</span>
            </Link>
            <h2 class="text-lg font-black dark:text-white uppercase tracking-tighter">{{ isEditing ? 'Édition Devis' : 'Nouveau Devis' }}</h2>
            <button @click="submit" 
                    :disabled="form.processing"
                    class="flex items-center gap-2 bg-primary text-primary-foreground px-6 py-2.5 rounded-full text-sm font-black shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 disabled:opacity-50 transition-all">
                <Save class="w-4 h-4" />
                Sauver
            </button>
        </div>

        <form @submit.prevent="submit" class="space-y-6 pb-40">
            <!-- Client & Global Details -->
            <section class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                <!-- Client -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1 flex items-center gap-2">
                         <Users class="w-4 h-4 text-primary" /> Client Destinataire
                    </label>
                    <select v-model="form.client_id" required 
                            class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm font-bold shadow-inner appearance-none transition-all focus:ring-2 focus:ring-primary">
                        <option value="">Sélectionner un client...</option>
                        <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.nom_complet }}</option>
                    </select>
                </div>

                <!-- Chantier (Optionnel) -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1 flex items-center gap-2">
                         <HardHat class="w-4 h-4 text-primary" /> Chantier associé (Facultatif)
                    </label>
                    <select v-model="form.chantier_id" 
                            class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm appearance-none shadow-inner transition-all hover:bg-gray-100">
                        <option value="">Aucun chantier spécifique...</option>
                        <option v-for="ch in chantiers" :key="ch.id" :value="ch.id">{{ ch.titre }}</option>
                    </select>
                </div>

                <!-- Date & Statut Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Date</label>
                        <input v-model="form.date" type="date" 
                               class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-xs font-bold shadow-sm" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">État</label>
                        <select v-model="form.statut" class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-xs font-bold appearance-none uppercase tracking-widest">
                            <option value="brouillon">Brouillon</option>
                            <option value="envoye">Envoyé</option>
                            <option value="accepte">Accepté</option>
                            <option value="refuse">Refusé</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Line Items Header -->
            <div class="flex items-center justify-between px-2">
                <h3 class="font-black text-lg dark:text-white flex items-center gap-2 grayscale"><FileText class="w-5 h-5" /> Lignes du devis</h3>
                <button type="button" @click="addLigne" 
                        class="px-4 py-2 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-500 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 shadow-sm active:scale-95 transition-all">
                    <Plus class="w-4 h-4" /> Ajouter ligne
                </button>
            </div>

            <!-- Individual Line Items -->
            <div class="space-y-4">
                <div v-for="(ligne, index) in form.lignes" :key="index" 
                     class="bg-white dark:bg-zinc-900 rounded-[1.5rem] p-5 shadow-sm border border-gray-100 dark:border-zinc-800 relative group animate-in slide-in-from-left-2 duration-300">
                    
                    <!-- Remove Button -->
                    <button type="button" @click="removeLigne(index)" 
                            class="absolute -top-2 -right-2 w-8 h-8 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg active:scale-125 transition-all hover:rotate-12">
                        <Trash2 class="w-4 h-4" />
                    </button>

                    <div class="space-y-4">
                        <!-- Designation -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Description de l'ouvrage</label>
                            <div class="relative">
                                <textarea v-model="ligne.designation" rows="2" 
                                          class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm font-medium pr-12 focus:ring-1 focus:ring-primary shadow-sm"
                                          placeholder="Ex: Fabrication d'un placard de cuisine..."></textarea>
                                <button type="button" @click="showingServicePicker = index" 
                                        class="absolute right-3 bottom-3 p-2 bg-white dark:bg-zinc-700 rounded-xl text-gray-400 hover:text-primary shadow-sm transition-colors active:scale-90">
                                    <Settings class="w-4 h-4" />
                                </button>
                            </div>

                            <!-- Integrated Service Picker Popover (Simplified) -->
                            <div v-if="showingServicePicker === index" 
                                 class="mt-2 p-4 bg-gray-100 dark:bg-zinc-800 rounded-2xl space-y-2 border border-primary/20 animate-in slide-in-from-top-2">
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Choisir depuis le catalogue</p>
                                <div class="max-h-40 overflow-y-auto space-y-1 pr-1 scrollbar-hide">
                                    <button v-for="s in services" :key="s.id" type="button"
                                            @click="selectService(index, s); showingServicePicker = null"
                                            class="w-full text-left p-3 hover:bg-white dark:hover:bg-zinc-700 rounded-xl text-xs font-bold transition-all flex items-center justify-between group">
                                        <span class="dark:text-white group-hover:text-primary">{{ s.nom }}</span>
                                        <span class="text-gray-400 text-[10px] font-normal">{{ formatCurrency(s.prix_indicatif).replace(',00', '') }}</span>
                                    </button>
                                </div>
                                <button type="button" @click="showingServicePicker = null" class="w-full py-2 text-[10px] font-black text-rose-500 uppercase tracking-widest">Fermer</button>
                            </div>
                        </div>

                        <!-- Qte & Prix -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="space-y-1">
                                <label class="text-[9px] font-bold text-gray-400 uppercase text-center block">Qte</label>
                                <input v-model="ligne.quantite" type="number" 
                                       class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-center text-sm font-black shadow-inner" />
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-bold text-gray-400 uppercase text-center block">Unité</label>
                                <select v-model="ligne.unite" class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-center text-xs font-bold appearance-none uppercase transition-all">
                                    <option value="unité">U</option>
                                    <option value="m²">M²</option>
                                    <option value="forfait">F</option>
                                </select>
                            </div>
                            <div class="space-y-1 col-span-1">
                                <label class="text-[9px] font-bold text-gray-400 uppercase text-center block">Unit (FCFA)</label>
                                <input v-model="ligne.prix_unitaire" type="number" 
                                       class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-center text-sm font-black shadow-inner" />
                            </div>
                        </div>

                        <!-- Subtotal Line -->
                        <div class="pt-2 flex justify-end">
                            <span class="text-xs font-black text-gray-400 uppercase tracking-widest mr-2">Sous-total :</span>
                            <span class="text-xs font-black text-primary dark:text-white">{{ formatCurrency(ligne.quantite * ligne.prix_unitaire).replace(',00', '') }} FCFA</span>
                        </div>
                    </div>
                </div>

                <div v-if="form.lignes.length === 0" class="py-12 text-center text-gray-400/50 bg-gray-100 dark:bg-zinc-900 rounded-3xl border border-dashed border-gray-200 dark:border-zinc-800 shadow-inner">
                    <p class="text-xs font-bold uppercase tracking-widest">Le devis est vide</p>
                    <button type="button" @click="addLigne" class="text-primary mt-4 font-black text-[10px] uppercase underline tracking-[0.2em] flex items-center justify-center gap-2 mx-auto">
                        <Plus class="w-4 h-4" /> Ajouter un ouvrage
                    </button>
                </div>
            </div>

            <!-- Footer Calculations & Adjustments -->
            <section class="bg-indigo-950 text-white rounded-[2.5rem] p-8 space-y-6 shadow-2xl relative overflow-hidden transition-all duration-500">
                <div class="flex justify-between items-center text-xs opacity-60 font-bold uppercase tracking-widest">
                    <span>Récapitulatif Financier</span>
                    <TrendingUp class="w-4 h-4 transition-transform hover:scale-125" />
                </div>
                
                <div class="space-y-4 py-4 border-y border-white/5">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold opacity-70">Calculé au m² / unité</span>
                        <span class="font-black text-sm">{{ formatCurrency(totalHT).replace(',00', '') }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold opacity-50 uppercase tracking-widest block">Main d'œuvre</label>
                            <div class="relative">
                                <input v-model="form.main_oeuvre" type="number" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-black focus:bg-white/10 transition-colors shadow-inner" />
                                <Plus class="absolute right-4 top-1/2 -translate-y-1/2 w-3 h-3 text-emerald-400" />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold opacity-50 uppercase tracking-widest block">Transport</label>
                            <div class="relative">
                                <input v-model="form.frais_transport" type="number" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-black focus:bg-white/10 transition-colors shadow-inner" />
                                <Plus class="absolute right-4 top-1/2 -translate-y-1/2 w-3 h-3 text-emerald-400" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1 pt-2">
                        <label class="text-[10px] font-bold opacity-50 uppercase tracking-widest block px-2">Remise Exceptionnelle</label>
                        <div class="relative group">
                            <input v-model="form.remise" type="number" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-black text-rose-400 focus:bg-white/10 transition-colors shadow-inner" />
                            <MinusCircle class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-rose-500 animate-pulse group-focus-within:animate-none" />
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-between items-end">
                    <div>
                        <p class="text-[10px] font-black opacity-60 uppercase tracking-[0.3em] mb-1">TOTAL TTC ESTIMÉ</p>
                        <p class="text-4xl font-black text-white tracking-tighter">{{ formatCurrency(totalGeneral).replace(',00', '') }}<span class="text-lg font-normal opacity-50 ml-2 italic">FCFA</span></p>
                    </div>
                    <div class="w-16 h-16 rounded-[1.5rem] bg-white/10 flex items-center justify-center backdrop-blur-md border border-white/20">
                         <Check class="w-8 h-8 text-emerald-400 shadow-glow" />
                    </div>
                </div>
            </section>

            <!-- Internal Notes -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Notes Internes / Conditions spécifiques</h3>
                <textarea v-model="form.notes" rows="3" class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm focus:ring-1 focus:ring-primary"></textarea>
            </div>

            <p class="text-[10px] text-gray-400 font-bold uppercase text-center tracking-[0.2em] pt-12">Gestion Documentaire DadouConfort</p>
        </form>
    </Layout>
</template>

<style scoped>
.shadow-glow {
    filter: drop-shadow(0 0 8px rgba(52, 211, 153, 0.5));
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
