<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    ChevronLeft, 
    Save, 
    TrendingDown, 
    HardHat, 
    Calendar, 
    Trash2,
    ArrowLeft,
    Check,
    Briefcase,
    ShoppingBag,
    FileText
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    depense: { type: Object, default: null },
    chantiers: Array,
});

const page = usePage();
const queryParams = new URLSearchParams(window.location.search);
const defaultChantierId = queryParams.get('chantier_id') || '';

const isEditing = !!props.depense;

const form = useForm({
    chantier_id: props.depense?.chantier_id ?? defaultChantierId,
    titre: props.depense?.titre ?? '',
    montant: props.depense?.montant ?? 0,
    date: props.depense?.date?.split('T')[0] ?? new Date().toISOString().split('T')[0],
    categorie: props.depense?.categorie ?? 'Matériaux',
    notes: props.depense?.notes ?? '',
});

const submit = () => {
    if (isEditing) {
        form.put(`/depenses/${props.depense.id}`);
    } else {
        form.post('/depenses');
    }
};

const CATEGORIES = [
    'Matériaux',
    'Transport',
    'Outillage',
    'Main d\'œuvre externe',
    'Logistique',
    'Administratif',
    'Autre',
];
</script>

<template>
    <Head :title="isEditing ? 'Modifier Dépense' : 'Nouvelle Dépense'" />

    <Layout>
        <!-- Sticky Toolbar -->
        <div class="flex items-center justify-between mb-4 sticky top-14 z-30 bg-gray-50/90 dark:bg-zinc-950/90 backdrop-blur-md py-4 -mx-4 px-4 shadow-sm border-b border-gray-100 dark:border-zinc-900 transition-all font-sans">
            <Link href="/depenses" class="p-2.5 bg-white dark:bg-zinc-900 rounded-xl shadow-sm hover:scale-105 active:scale-95 transition-transform">
                <ArrowLeft class="w-5 h-5 text-gray-500" />
            </Link>
            <h2 class="text-xs font-black dark:text-white uppercase tracking-[0.2em] leading-none">{{ isEditing ? 'Éditer Sortie' : 'Nouvelle Dépense' }}</h2>
            <button @click="submit" 
                    :disabled="form.processing"
                    class="bg-rose-500 text-white p-3 rounded-full shadow-lg shadow-rose-500/30 hover:scale-110 active:scale-95 disabled:opacity-50 transition-all">
                <Check class="w-5 h-5" />
            </button>
        </div>

        <form @submit.prevent="submit" class="space-y-6 pb-20 animate-in fade-in slide-in-from-bottom-5 duration-700">
            <!-- Header Icon Section -->
            <div class="flex flex-col items-center py-6">
                <div class="w-20 h-20 rounded-[2rem] bg-rose-50 dark:bg-rose-950/30 text-rose-500 flex items-center justify-center shadow-xl border border-rose-100 dark:border-rose-900/30 mb-4 transition-transform hover:rotate-12">
                    <ShoppingBag class="w-10 h-10" />
                </div>
                <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest leading-none">Sortie de fonds</p>
            </div>

            <!-- Subject & Category -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                <!-- Titre -->
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Désignation / Objet</label>
                    <input v-model="form.titre" type="text" placeholder="Ex: Achat bois Samba..." required
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-sm font-bold dark:text-white shadow-inner focus:ring-1 focus:ring-rose-500/20" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Catégorie -->
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Catégorie</label>
                        <select v-model="form.categorie" class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-[10px] font-black uppercase dark:text-white shadow-inner appearance-none transition-all">
                            <option v-for="cat in CATEGORIES" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                    </div>
                    <!-- Date -->
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Date d'achat</label>
                        <input v-model="form.date" type="date" 
                               class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-xs font-bold dark:text-white shadow-inner" />
                    </div>
                </div>
            </div>

            <!-- Amount Section -->
            <div class="bg-rose-600 text-white rounded-[2rem] p-8 shadow-2xl relative overflow-hidden transition-all duration-500">
                <div class="absolute -right-5 -bottom-5 opacity-10 pointer-events-none">
                    <TrendingDown class="w-40 h-40" />
                </div>
                <div class="relative z-10 text-center space-y-4">
                    <label class="text-[10px] font-black opacity-60 uppercase tracking-[0.3em]">MONTANT DÉBOURSÉ</label>
                    <div class="relative inline-block w-full max-w-[240px] group">
                        <input v-model="form.montant" type="number" 
                               class="w-full bg-white/10 border-none rounded-3xl p-6 text-center text-4xl font-black text-white focus:bg-white/20 transition-all shadow-inner outline-none placeholder:text-white/20" />
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black opacity-30 uppercase tracking-widest font-sans italic">XAF</span>
                    </div>
                </div>
            </div>

            <!-- Context Section (Chantier) -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1 flex items-center gap-2">
                    <HardHat class="w-3.5 h-3.5 text-rose-400" /> Affecter au Chantier (Recommandé)
                </label>
                <select v-model="form.chantier_id" 
                        class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-sm font-bold dark:text-white shadow-inner appearance-none transition-all focus:ring-1 focus:ring-rose-500/10">
                    <option value="">-- Dépense globale --</option>
                    <option v-for="ch in chantiers" :key="ch.id" :value="ch.id">{{ ch.titre }}</option>
                </select>
                <p class="text-[9px] text-gray-400 font-medium px-2 italic">Aidera au calcul de la marge de rentabilité du projet.</p>
            </div>

            <!-- Notes -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1 flex items-center gap-2">
                    <FileText class="w-3.5 h-3.5 text-gray-400" /> Précisions techniques / Fournisseur
                </label>
                <textarea v-model="form.notes" rows="4" placeholder="Où a été fait l'achat ? Détails spécifiques..." 
                          class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-xs dark:text-white shadow-inner transition-all"></textarea>
            </div>

            <p class="text-[8px] font-black text-gray-400 uppercase text-center tracking-[0.3em] pt-10 pb-12 opacity-40">Registre des Sorties DadouConfort</p>
        </form>
    </Layout>
</template>
