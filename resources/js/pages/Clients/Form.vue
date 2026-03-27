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
    ArrowLeft
} from 'lucide-vue-next';

const props = defineProps({
    client: {
        type: Object,
        default: null,
    },
});

const isEditing = !!props.client;

const form = useForm({
    nom_complet: props.client?.nom_complet ?? '',
    telephone: props.client?.telephone ?? '',
    adresse: props.client?.adresse ?? '',
    localisation: props.client?.localisation ?? '',
    notes: props.client?.notes ?? '',
});

const submit = () => {
    if (isEditing) {
        form.put(`/clients/${props.client.id}`);
    } else {
        form.post('/clients');
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Modifier Client' : 'Nouveau Client'" />

    <Layout>
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between mb-6">
            <Link :href="isEditing ? `/clients/${client.id}` : '/clients'" 
                  class="flex items-center gap-2 text-gray-500 dark:text-zinc-500 hover:text-primary transition-colors">
                <div class="p-2 bg-white dark:bg-zinc-900 rounded-full shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
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

        <!-- Form Section -->
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
                           placeholder="Ex: Jean Dupont"
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white transition-all"
                           required />
                    <span v-if="form.errors.nom_complet" class="text-rose-500 text-xs px-2">{{ form.errors.nom_complet }}</span>
                </div>

                <!-- Téléphone -->
                <div class="space-y-2 group">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <Phone class="w-4 h-4 text-primary" />
                        Numéro de Téléphone
                    </label>
                    <input v-model="form.telephone"
                           type="tel" 
                           placeholder="Ex: 677 000 000"
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white transition-all" />
                    <span v-if="form.errors.telephone" class="text-rose-500 text-xs px-2">{{ form.errors.telephone }}</span>
                </div>

                <!-- Localisation Rapide -->
                <div class="space-y-2 group">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <MapPin class="w-4 h-4 text-primary" />
                        Localisation (Quartier/Ville)
                    </label>
                    <input v-model="form.localisation"
                           type="text" 
                           placeholder="Ex: Bastos, Yaoundé"
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white transition-all" />
                    <span v-if="form.errors.localisation" class="text-rose-500 text-xs px-2">{{ form.errors.localisation }}</span>
                </div>

                <!-- Adresse Détaillée -->
                <div class="space-y-2 group">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <MapPin class="w-4 h-4 text-primary" />
                        Adresse Complète
                    </label>
                    <textarea v-model="form.adresse"
                              rows="2"
                              placeholder="Adresse de livraison ou chantier..."
                              class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white transition-all"></textarea>
                    <span v-if="form.errors.adresse" class="text-rose-500 text-xs px-2">{{ form.errors.adresse }}</span>
                </div>

                <!-- Notes -->
                <div class="space-y-2 group pb-2">
                    <label class="text-xs font-bold text-gray-400 dark:text-zinc-500 flex items-center gap-2 uppercase tracking-widest px-1">
                        <FileText class="w-4 h-4 text-primary" />
                        Notes Internes
                    </label>
                    <textarea v-model="form.notes"
                              rows="4"
                              placeholder="Infos utiles, exigences particulières..."
                              class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary dark:text-white transition-all"></textarea>
                    <span v-if="form.errors.notes" class="text-rose-500 text-xs px-2">{{ form.errors.notes }}</span>
                </div>
            </div>

            <p class="text-[10px] text-center text-gray-400 uppercase tracking-widest pb-10">DadouConfort - Gestion Interne</p>
        </form>
    </Layout>
</template>
