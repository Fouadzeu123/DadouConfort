<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    ChevronLeft, 
    Save, 
    User, 
    Phone, 
    MapPin, 
    FileText,
    Briefcase,
    Clock,
    DollarSign,
    ArrowLeft
} from 'lucide-vue-next';

const props = defineProps({
    prestataire: {
        type: Object,
        default: null,
    },
});

const isEditing = !!props.prestataire;

const form = useForm({
    nom_complet: props.prestataire?.nom_complet ?? '',
    telephone: props.prestataire?.telephone ?? '',
    specialite: props.prestataire?.specialite ?? '',
    adresse: props.prestataire?.adresse ?? '',
    disponibilite: props.prestataire?.disponibilite ?? 'disponible',
    tarif_indicatif: props.prestataire?.tarif_indicatif ?? '',
    notes: props.prestataire?.notes ?? '',
});

const submit = () => {
    if (isEditing) {
        form.put(`/prestataires/${props.prestataire.id}`);
    } else {
        form.post('/prestataires');
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Modifier Prestataire' : 'Nouveau Prestataire'" />

    <Layout>
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between mb-6">
            <Link :href="isEditing ? `/prestataires/${prestataire.id}` : '/prestataires'" 
                  class="flex items-center gap-2 text-gray-500">
                <div class="p-2 bg-white dark:bg-zinc-900 rounded-full shadow-sm">
                    <ArrowLeft class="w-5 h-5" />
                </div>
                <span class="font-bold text-sm">Annuler</span>
            </Link>
            <h2 class="text-xl font-bold dark:text-white">{{ isEditing ? 'Modifier' : 'Ajouter' }}</h2>
            <button @click="submit" 
                    :disabled="form.processing"
                    class="flex items-center gap-2 bg-primary text-primary-foreground px-6 py-2 rounded-full text-sm font-bold shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 disabled:opacity-50 transition-all">
                <Save class="w-4 h-4" />
                Enregistrer
            </button>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                <!-- Nom Complet -->
                <div class="space-y-2 group">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <User class="w-4 h-4 text-primary" />
                        Nom Complet
                    </label>
                    <input v-model="form.nom_complet"
                           type="text" 
                           placeholder="Ex: Paul Mvondo"
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white transition-all"
                           required />
                    <span v-if="form.errors.nom_complet" class="text-rose-500 text-xs px-2">{{ form.errors.nom_complet }}</span>
                </div>

                <!-- Spécialité -->
                <div class="space-y-2 group">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <Briefcase class="w-4 h-4 text-primary" />
                        Spécialité
                    </label>
                    <select v-model="form.specialite"
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white appearance-none transition-all">
                        <option value="">Sélectionner une spécialité...</option>
                        <option value="Charpentier">Charpentier</option>
                        <option value="Soudeur">Soudeur</option>
                        <option value="Peintre">Peintre</option>
                        <option value="Maçon">Maçon</option>
                        <option value="Vitrier">Vitrier</option>
                        <option value="Transporteur">Transporteur</option>
                        <option value="Plombier">Plombier</option>
                        <option value="Électricien">Électricien</option>
                        <option value="Manœuvre">Manœuvre</option>
                    </select>
                    <span v-if="form.errors.specialite" class="text-rose-500 text-xs px-2">{{ form.errors.specialite }}</span>
                </div>

                <!-- Téléphone -->
                <div class="space-y-2 group">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <Phone class="w-4 h-4 text-primary" />
                        Téléphone
                    </label>
                    <input v-model="form.telephone" type="tel" 
                           placeholder="Ex: 699 000 000"
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white" />
                </div>

                <!-- Disponibilité -->
                <div class="space-y-2 group">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <Clock class="w-4 h-4 text-primary" />
                        Disponibilité Actuelle
                    </label>
                    <div class="grid grid-cols-3 gap-2 p-1 bg-gray-50 dark:bg-zinc-800 rounded-3xl">
                        <button type="button" @click="form.disponibilite = 'disponible'"
                                :class="form.disponibilite === 'disponible' ? 'bg-emerald-500 text-white shadow-lg' : 'text-gray-500'"
                                class="py-3 rounded-2xl text-[10px] font-bold uppercase transition-all">Disponible</button>
                        <button type="button" @click="form.disponibilite = 'en_mission'"
                                :class="form.disponibilite === 'en_mission' ? 'bg-amber-500 text-white shadow-lg' : 'text-gray-500'"
                                class="py-3 rounded-2xl text-[10px] font-bold uppercase transition-all">En Mission</button>
                        <button type="button" @click="form.disponibilite = 'indisponible'"
                                :class="form.disponibilite === 'indisponible' ? 'bg-rose-500 text-white shadow-lg' : 'text-gray-500'"
                                class="py-3 rounded-2xl text-[10px] font-bold uppercase transition-all">Indisp.</button>
                    </div>
                </div>

                <!-- Tarif Indicatif -->
                <div class="space-y-2 group">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <DollarSign class="w-4 h-4 text-primary" />
                        Tarif Indicatif (Journalier ou autre)
                    </label>
                    <input v-model="form.tarif_indicatif" type="text" 
                           placeholder="Ex: 15 000 FCFA / jour"
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white" />
                </div>

                <!-- Adresse -->
                <div class="space-y-2 group">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <MapPin class="w-4 h-4 text-primary" />
                        Zone / Adresse
                    </label>
                    <input v-model="form.adresse" type="text"
                           placeholder="Ex: Mendong, Yaoundé"
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white" />
                </div>

                <!-- Notes -->
                <div class="space-y-2 group pb-2">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <FileText class="w-4 h-4 text-primary" />
                        Notes Internes
                    </label>
                    <textarea v-model="form.notes" rows="3"
                              placeholder="Expérience, outils, commentaires..."
                              class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white transition-all"></textarea>
                </div>
            </div>

            <p class="text-[10px] text-center text-gray-400 uppercase tracking-widest pb-10">Gestion Prestataires - DadouConfort</p>
        </form>
    </Layout>
</template>
