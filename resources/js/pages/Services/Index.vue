<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    Plus, 
    Search, 
    Package, 
    ChevronRight, 
    Trash2, 
    Edit2, 
    Filter,
    ArrowLeft,
    Check,
    Briefcase
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    services: Object,
    filters: Object,
    categories: Array,
});

const search = ref(props.filters.search || '');
const showingForm = ref(false);
const editingService = ref(null);

watch(search, (value) => {
    router.get('/services', { search: value }, { preserveState: true, replace: true });
});

const form = useForm({
    nom: '',
    description: '',
    unite: 'unité',
    prix_indicatif: 0,
    categorie: '',
});

const editService = (service) => {
    editingService.value = service;
    form.nom = service.nom;
    form.description = service.description;
    form.unite = service.unite;
    form.prix_indicatif = service.prix_indicatif;
    form.categorie = service.categorie;
    showingForm.value = true;
};

const resetForm = () => {
    editingService.value = null;
    form.reset();
    showingForm.value = false;
};

const submit = () => {
    if (editingService.value) {
        form.put(`/services/${editingService.value.id}`, {
            onSuccess: () => resetForm(),
        });
    } else {
        form.post('/services', {
            onSuccess: () => resetForm(),
        });
    }
};

const deleteService = (id) => {
    if (confirm('Supprimer ce service ?')) {
        router.delete(`/services/${id}`);
    }
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};
</script>

<template>
    <Head title="Catalogue des Services" />

    <Layout>
        <div v-if="!showingForm" class="space-y-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold dark:text-white">Services</h2>
                <button @click="showingForm = true" 
                        class="flex items-center gap-1 bg-primary text-primary-foreground px-4 py-2 rounded-2xl text-sm font-bold shadow-lg shadow-primary/20 transition-transform active:scale-95">
                    <Plus class="w-4 h-4" />
                    Ajouter
                </button>
            </div>

            <!-- Search -->
            <div class="relative group">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500 group-focus-within:text-primary transition-colors" />
                <input v-model="search"
                       type="text" 
                       placeholder="Rechercher un service..."
                       class="w-full pl-12 pr-4 py-4 bg-white dark:bg-zinc-900 border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-primary dark:text-white transition-all" />
            </div>

            <!-- Categories -->
            <div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide">
                <button class="px-4 py-2 rounded-full bg-primary text-primary-foreground text-xs font-bold whitespace-nowrap">Tous</button>
                <button v-for="cat in categories" :key="cat"
                        class="px-4 py-2 rounded-full bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 text-gray-500 text-xs font-bold whitespace-nowrap">{{ cat }}</button>
            </div>

            <!-- List -->
            <div class="space-y-3 pb-20">
                <div v-for="service in services.data" :key="service.id"
                     class="group bg-white dark:bg-zinc-900 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-indigo-500">
                            <Package class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold dark:text-white">{{ service.nom }}</h3>
                            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">{{ service.categorie || 'Menuiserie' }}</p>
                            <p class="text-xs font-bold text-gray-600 dark:text-zinc-400 mt-1">{{ formatCurrency(service.prix_indicatif).replace(',00', '') }}<span class="text-[10px] text-gray-400 font-normal"> / {{ service.unite }}</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click="editService(service)" class="p-2 text-gray-400 hover:text-primary transition-colors">
                            <Edit2 class="w-4 h-4" />
                        </button>
                        <button @click="deleteService(service.id)" class="p-2 text-gray-400 hover:text-rose-500 transition-colors">
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                
                <div v-if="services.data.length === 0" class="py-20 text-center text-gray-400 space-y-4">
                    <Package class="w-12 h-12 mx-auto opacity-20" />
                    <p class="text-xs uppercase font-bold tracking-widest">Aucun service catalogue</p>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal/Form Layer -->
        <div v-if="showingForm" class="space-y-6 animate-in slide-in-from-bottom-2 duration-300">
            <div class="flex items-center justify-between">
                <button @click="resetForm" class="flex items-center gap-2 text-gray-400">
                    <ArrowLeft class="w-5 h-5" />
                    <span class="font-bold text-sm">Retour</span>
                </button>
                <h2 class="text-xl font-bold dark:text-white">{{ editingService ? 'Modifier Service' : 'Nouveau Service' }}</h2>
                <div class="w-12"></div>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-zinc-800 space-y-6">
                <!-- Nom -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Désignation du service</label>
                    <input v-model="form.nom" type="text" placeholder="Ex: Fabrication Porte" required
                           class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm" />
                </div>
                
                <!-- Categorie & Unite Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Catégorie</label>
                        <input v-model="form.categorie" type="text" placeholder="Menuiserie..." 
                               class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Unité</label>
                        <select v-model="form.unite" class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm appearance-none">
                            <option value="unité">Unité</option>
                            <option value="m²">m²</option>
                            <option value="forfait">Forfait</option>
                            <option value="lot">Lot</option>
                            <option value="jour">Jour</option>
                        </select>
                    </div>
                </div>

                <!-- Prix -->
                <div class="space-y-2 group">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Prix indicatif (FCFA)</label>
                    <div class="relative">
                        <input v-model="form.prix_indicatif" type="number" 
                               class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm" />
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold font-sans">FCFA</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Description (optionnelle)</label>
                    <textarea v-model="form.description" rows="3"
                              class="w-full bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 dark:text-white text-sm transition-all"></textarea>
                </div>

                <button type="submit" 
                        :disabled="form.processing"
                        class="w-full bg-primary text-primary-foreground py-4 rounded-2xl flex items-center justify-center gap-2 font-bold transition-all shadow-lg active:scale-95 disabled:opacity-50">
                    <Check class="w-5 h-5 text-emerald-400" />
                    {{ editingService ? 'Mettre à jour' : 'Enregistrer le service' }}
                </button>
            </form>
        </div>
    </Layout>
</template>
