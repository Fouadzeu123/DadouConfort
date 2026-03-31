<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import AppBar from '@/components/AppBar.vue';
import { 
    Search, 
    Plus, 
    Phone, 
    MapPin, 
    MoreVertical, 
    ChevronRight,
    UserPlus,
    UserX,
    Filter,
    Users,
    HardHat,
    FileText
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    clients: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

watch(search, (value) => {
    router.get('/clients', { search: value }, { preserveState: true, replace: true });
});

const deleteClient = (id) => {
    if (confirm('Supprimer ce client ?')) {
        router.delete(`/clients/${id}`);
    }
};
</script>

<template>
    <Head title="Gestion des Clients" />

    <Layout>
        <AppBar title="Clients" :actions="[{ icon: Plus, href: '/clients/create' }]" />

        <!-- Top Sticky Search -->
        <div class="space-y-4">
            <div class="relative group">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-zinc-500 group-focus-within:text-primary transition-colors" />
                <input v-model="search"
                       type="text" 
                       placeholder="Rechercher un client..."
                       class="w-full pl-12 pr-4 py-4 bg-white dark:bg-zinc-900 border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-primary dark:text-white dark:placeholder-zinc-600 transition-all" />
            </div>
        </div>

        <!-- Filter Bubbles -->
        <div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide">
            <button class="px-4 py-2 rounded-full bg-primary text-primary-foreground text-xs font-bold whitespace-nowrap">Tous</button>
            <button class="px-4 py-2 rounded-full bg-white dark:bg-zinc-900 text-gray-600 dark:text-zinc-400 border border-gray-100 dark:border-zinc-800 text-xs font-bold whitespace-nowrap flex items-center gap-1">
                <Filter class="w-3 h-3" />
                Filtrer
            </button>
        </div>

        <!-- Clients List -->
        <div class="space-y-3">
            <div v-for="client in clients.data" :key="client.id" 
                 class="group bg-white dark:bg-zinc-900 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-zinc-800 hover:border-primary/50 transition-colors">
                <Link :href="`/clients/${client.id}`" class="block">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-zinc-800 flex items-center justify-center font-bold text-gray-400 dark:text-zinc-600">
                                {{ client.nom_complet.substring(0, 2).toUpperCase() }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg dark:text-white group-hover:text-primary transition-colors">{{ client.nom_complet }}</h3>
                                <div class="flex items-center gap-1 text-gray-500 dark:text-zinc-500">
                                    <Phone class="w-3 h-3" />
                                    <span class="text-xs">{{ client.telephone || 'Non renseigné' }}</span>
                                </div>
                            </div>
                        </div>
                        <button @click.prevent="deleteClient(client.id)" class="p-2 text-gray-400 hover:text-rose-500 transition-colors">
                            <UserX class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-zinc-500 mb-4 px-1">
                        <span class="flex items-center gap-1">
                            <HardHat class="w-3 h-3" /> {{ client.chantiers_count }} CHANTIER{{ client.chantiers_count > 1 ? 'S' : '' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <FileText class="w-3 h-3" /> {{ client.devis_count }} DEVIS
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-50 dark:border-zinc-800">
                        <div class="flex items-center gap-1 text-gray-600 dark:text-zinc-400 text-sm">
                            <MapPin class="w-4 h-4 text-primary/50" />
                            <span class="truncate max-w-[200px]">{{ client.localisation || client.adresse || 'N/A' }}</span>
                        </div>
                        <ChevronRight class="w-5 h-5 text-gray-300 dark:text-zinc-600 transition-all group-hover:translate-x-1" />
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-if="clients.data.length === 0" class="py-20 text-center space-y-4">
                <div class="w-20 h-20 bg-gray-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto">
                    <Users class="w-10 h-10 text-gray-300 dark:text-zinc-600" />
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">Aucun client trouvé</h3>
                <p class="text-gray-500 text-sm">Commencez par ajouter votre premier client.</p>
                <Link href="/clients/create" class="inline-flex items-center gap-2 text-primary font-bold uppercase text-xs tracking-widest">
                    Ajouter un client <Plus class="w-4 h-4" />
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="clients.links?.length > 3" class="flex justify-center gap-1 pt-6 pb-12 overflow-x-auto scrollbar-hide">
            <template v-for="link in clients.links" :key="link.label">
                <Link v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      class="px-4 py-2 rounded-xl text-xs font-bold transition-colors"
                      :class="link.active ? 'bg-primary text-primary-foreground select-none' : 'bg-white dark:bg-zinc-900 text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800'">
                </Link>
            </template>
        </div>
    </Layout>
</template>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
