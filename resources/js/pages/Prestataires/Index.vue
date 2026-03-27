<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import AppBar from '@/components/AppBar.vue';
import { 
    Search, 
    Plus, 
    Phone, 
    MapPin, 
    ChevronRight,
    Briefcase,
    BadgeCheck,
    Clock,
    UserX,
    Filter
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

// ... (reste du script)
</script>

<template>
    <Head title="Prestataires Externes" />

    <Layout>
        <AppBar title="Prestataires" :actions="[{ icon: Plus, href: '/prestataires/create' }]" />

        <div class="space-y-4">
            <div class="relative group">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors" />
                <input v-model="search"
                       type="text" 
                       placeholder="Rechercher par nom ou spécialité..."
                       class="w-full pl-12 pr-4 py-4 bg-white dark:bg-zinc-900 border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-primary dark:text-white transition-all" />
            </div>
        </div>

        <!-- Specialties horizontal list -->
        <div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide">
            <button class="px-4 py-2 rounded-full bg-primary text-primary-foreground text-xs font-bold whitespace-nowrap">Tous</button>
            <button class="px-4 py-2 rounded-full bg-white dark:bg-zinc-900 text-gray-500 border border-gray-100 dark:border-zinc-800 text-xs font-bold whitespace-nowrap">Charpente</button>
            <button class="px-4 py-2 rounded-full bg-white dark:bg-zinc-900 text-gray-500 border border-gray-100 dark:border-zinc-800 text-xs font-bold whitespace-nowrap">Peinture</button>
            <button class="px-4 py-2 rounded-full bg-white dark:bg-zinc-900 text-gray-500 border border-gray-100 dark:border-zinc-800 text-xs font-bold whitespace-nowrap">Soudure</button>
        </div>

        <!-- Prestataires List -->
        <div class="space-y-4">
            <div v-for="prestataire in prestataires.data" :key="prestataire.id" 
                 class="bg-white dark:bg-zinc-900 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800 hover:border-primary/50 transition-colors">
                <Link :href="`/prestataires/${prestataire.id}`" class="block">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center font-bold text-primary">
                                <Briefcase class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="font-bold text-lg dark:text-white">{{ prestataire.nom_complet }}</h3>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-indigo-500 uppercase tracking-widest">{{ prestataire.specialite || 'Spécialité libre' }}</span>
                                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                    <span class="text-[10px] py-0.5 px-2 rounded-full font-bold uppercase transition-colors" :class="getStatusClasses(prestataire.disponibilite)">
                                        {{ prestataire.disponibilite.replace('_', ' ') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button @click.prevent="deletePrestataire(prestataire.id)" class="p-2 text-gray-300 hover:text-rose-500 transition-colors">
                            <UserX class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-zinc-500">
                            <Phone class="w-3.5 h-3.5" />
                            <span class="text-xs">{{ prestataire.telephone || 'Aucun numéro' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500 dark:text-zinc-500">
                            <Clock class="w-3.5 h-3.5" />
                            <span class="text-xs font-bold uppercase tracking-tighter">{{ prestataire.chantiers_count }} Chantiers</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-50 dark:border-zinc-800">
                        <div class="flex items-center gap-1 text-gray-400 dark:text-zinc-500 text-xs">
                            <MapPin class="w-3.5 h-3.5" />
                            <span class="truncate max-w-[220px]">{{ prestataire.adresse || 'Zone non définie' }}</span>
                        </div>
                        <ChevronRight class="w-5 h-5 text-gray-300 dark:text-zinc-600 group-hover:translate-x-1 transition-all" />
                    </div>
                </Link>
            </div>

            <div v-if="prestataires.data.length === 0" class="py-20 text-center space-y-4">
                <div class="w-20 h-20 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto">
                    <Briefcase class="w-10 h-10 text-gray-300 dark:text-zinc-600" />
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">Aucun prestataire</h3>
                <p class="text-gray-500 text-sm">Ajoutez vos partenaires externes ici.</p>
                <Link href="/prestataires/create" class="inline-flex items-center gap-2 text-primary font-bold uppercase text-xs tracking-widest">
                    Ajouter un prestataire <Plus class="w-4 h-4" />
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="prestataires.links?.length > 3" class="flex justify-center gap-1 pt-6 pb-12 overflow-x-auto scrollbar-hide">
            <template v-for="link in prestataires.links" :key="link.label">
                <Link v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      class="px-4 py-2 rounded-xl text-xs font-bold"
                      :class="link.active ? 'bg-primary text-primary-foreground' : 'bg-white dark:bg-zinc-900 text-gray-600 dark:text-zinc-400'">
                </Link>
            </template>
        </div>
    </Layout>
</template>
