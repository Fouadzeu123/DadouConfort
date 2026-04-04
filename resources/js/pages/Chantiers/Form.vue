<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { 
    Save, 
    HardHat, 
    Users, 
    MapPin, 
    Settings,
    FileText
} from 'lucide-vue-next';
import AppBar from '@/components/AppBar.vue';
import Layout from '@/layouts/MobileLayout.vue';

const props = defineProps<{
    chantier?: any;
    clients: any[];
}>();


const queryParams = new URLSearchParams(window.location.search);
const defaultClientId = queryParams.get('client_id');

const isEditing = !!props.chantier;

const form = useForm({
    client_id: props.chantier?.client_id ?? (defaultClientId ?? ''),
    titre: props.chantier?.titre ?? '',
    description: props.chantier?.description ?? '',
    localisation: props.chantier?.localisation ?? '',
    date_debut: props.chantier?.date_debut?.split('T')[0] ?? '',
    date_fin_prevue: props.chantier?.date_fin_prevue?.split('T')[0] ?? '',
    statut: props.chantier?.statut ?? 'en_attente',
    notes: props.chantier?.notes ?? '',
});

const submit = () => {
    if (isEditing) {
        form.put(`/chantiers/${props.chantier.id}`);
    } else {
        form.post('/chantiers');
    }
};

const STATUTS = [
    { value: 'en_attente', label: 'En Attente', color: 'bg-amber-500' },
    { value: 'en_cours', label: 'En Cours', color: 'bg-emerald-500' },
    { value: 'termine', label: 'Terminé', color: 'bg-indigo-500' },
    { value: 'suspendu', label: 'Suspendu', color: 'bg-rose-500' },
    { value: 'annule', label: 'Annulé', color: 'bg-gray-500' },
];
</script>

<template>
    <Head :title="isEditing ? 'Modifier Chantier' : 'Démarrer Chantier'" />

    <Layout hide-nav>
        <AppBar :title="isEditing ? 'Modifier Chantier' : 'Nouveau Chantier'" 
                show-back-button 
                :back-href="isEditing ? `/chantiers/${chantier.id}` : '/chantiers'" />

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Client Selection -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4">
                <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                    <Users class="w-4 h-4 text-primary" /> Client concerné
                </label>
                <select v-model="form.client_id" required 
                        class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm appearance-none transition-all focus:ring-2 focus:ring-primary shadow-inner">
                    <option value="">Sélectionner un client...</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.nom_complet }}</option>
                </select>
            </div>

            <!-- General Details -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                <!-- Titre -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                        <HardHat class="w-4 h-4 text-primary" /> Titre du chantier
                    </label>
                    <input v-model="form.titre" type="text" placeholder="Ex: Rénovation Cuisine Mbarga" required
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm focus:ring-2 focus:ring-primary transition-all" />
                </div>

                <!-- Localisation -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                        <MapPin class="w-4 h-4 text-primary" /> Localisation exacte
                    </label>
                    <input v-model="form.localisation" type="text" placeholder="Bastos, Rue 123..." 
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm focus:ring-2 focus:ring-primary" />
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-tighter">Début prévu</label>
                        <input v-model="form.date_debut" type="date" 
                               class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-xs focus:ring-2 focus:ring-primary appearance-none" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-tighter">Fin prévue</label>
                        <input v-model="form.date_fin_prevue" type="date" 
                               class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-xs focus:ring-2 focus:ring-primary appearance-none" />
                    </div>
                </div>
            </div>

            <!-- Status Section -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4">
                <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                    <Settings class="w-4 h-4 text-primary" /> État d'avancement
                </label>
                <div class="flex flex-wrap gap-2">
                    <button v-for="s in STATUTS" :key="s.value" type="button"
                            @click="form.statut = s.value"
                            class="px-4 py-2 rounded-full text-[10px] font-bold uppercase transition-all flex items-center gap-2 border border-transparent shadow-sm"
                            :class="form.statut === s.value ? `${s.color} text-white scale-105 shadow-lg` : 'bg-gray-50 dark:bg-zinc-800 text-gray-500 dark:text-zinc-500'">
                        <div class="w-1.5 h-1.5 rounded-full" :class="form.statut === s.value ? 'bg-white' : s.color"></div>
                        {{ s.label }}
                    </button>
                </div>
            </div>

            <!-- Description & Notes -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                        <FileText class="w-4 h-4 text-primary" /> Description des travaux
                    </label>
                    <textarea v-model="form.description" rows="4" placeholder="Détails techniques du projet..."
                              class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm focus:ring-2 focus:ring-primary transition-all"></textarea>
                </div>
                <div class="space-y-2 pb-2">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                        <FileText class="w-4 h-4 text-primary" /> Notes internes
                    </label>
                    <textarea v-model="form.notes" rows="4" placeholder="Commentaires personnels..."
                              class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm focus:ring-2 focus:ring-primary transition-all"></textarea>
                </div>
            </div>

            <p class="text-[10px] text-gray-400 uppercase text-center font-bold tracking-[0.2em] pb-32">DadouConfort - Pilotage Chantier</p>

            <!-- Bottom Action Button (Inline for better mobile experience during typing) -->
            <div class="px-4 py-8">
                <button type="submit" :disabled="form.processing"
                        class="w-full bg-primary text-white py-4 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/20 flex items-center justify-center gap-3 active:scale-95 transition-all disabled:opacity-50">
                    <Save v-if="!form.processing" class="w-5 h-5" />
                    <span v-if="form.processing" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    {{ isEditing ? 'Mettre à jour' : 'Enregistrer le chantier' }}
                </button>
            </div>
        </form>
    </Layout>
</template>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.5);
    cursor: pointer;
}
.dark input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
}
</style>
