<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    Phone,
    FileText,
    ChevronRight,
    Edit,
    MessagesSquare,
    HardHat,
    Plus,
    Mail
} from 'lucide-vue-next';

const props = defineProps({
    client: Object,
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};

const callClient = () => {
    if (props.client.telephone) {
        window.location.href = `tel:${props.client.telephone}`;
    }
};

const messageClient = () => {
    if (props.client.telephone) {
        window.location.href = `https://wa.me/${props.client.telephone.replace(/\s+/g, '')}`;
    }
};
</script>

<template>
    <Head :title="client.nom_complet" />

    <Layout>
        <AppBar :title="client.nom_complet" show-back-button back-href="/clients" :actions="[{ icon: Edit, href: `/clients/${client.id}/edit` }]" />

        <!-- Client Header Profile -->
        <div class="text-center py-6">
            <div class="w-24 h-24 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-4xl font-bold mx-auto mb-4 shadow-xl">
                {{ client.nom_complet.substring(0, 2).toUpperCase() }}
            </div>
            <h1 class="text-2xl font-bold dark:text-white">{{ client.nom_complet }}</h1>
            <p class="text-gray-500 dark:text-zinc-500 text-sm mt-1">{{ client.localisation || 'Aucune localisation' }}</p>

            <!-- Communication Quick Actions -->
            <div class="flex justify-center gap-6 mt-8">
                <button @click="callClient" 
                        class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 flex items-center justify-center shadow-sm">
                        <Phone class="w-6 h-6" />
                    </div>
                    <span class="text-[10px] font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-widest">Appeler</span>
                </button>
                <button @click="messageClient" 
                        class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 dark:bg-sky-950/30 flex items-center justify-center shadow-sm">
                        <MessagesSquare class="w-6 h-6" />
                    </div>
                    <span class="text-[10px] font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-widest">WhatsApp</span>
                </button>
                <div class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-600 dark:bg-zinc-800 flex items-center justify-center shadow-sm">
                        <Mail class="w-6 h-6" />
                    </div>
                    <span class="text-[10px] font-bold text-gray-700 dark:text-zinc-400 uppercase tracking-widest">Email</span>
                </div>
            </div>
        </div>

        <!-- Details Sections Grid -->
        <div class="grid grid-cols-1 gap-4">
            <!-- Infos Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                    Coordonnées
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Téléphone</p>
                        <p class="text-sm dark:text-white">{{ client.telephone || 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Zone</p>
                        <p class="text-sm dark:text-white">{{ client.localisation || 'N/A' }}</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Adresse</p>
                    <p class="text-sm dark:text-white leading-relaxed">{{ client.adresse || 'N/A' }}</p>
                </div>
                <div v-if="client.notes" class="pt-4 border-t border-gray-50 dark:border-zinc-800 space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Notes</p>
                    <p class="text-sm text-gray-600 dark:text-zinc-400 leading-relaxed italic">{{ client.notes }}</p>
                </div>
            </div>

            <!-- History Tabs / Lists -->
            <div class="space-y-4 pt-4">
                <!-- Chantiers List -->
                <div class="flex items-center justify-between px-1">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <HardHat class="w-5 h-5 text-amber-500" />
                        Chantiers
                    </h3>
                    <Link :href="`/chantiers/create?client_id=${client.id}`" 
                          class="flex items-center gap-1 text-primary text-xs font-bold uppercase tracking-wider">
                        <Plus class="w-3 h-3" /> Nouveau
                    </Link>
                </div>
                <div class="space-y-2">
                    <Link v-for="chantier in client.chantiers" :key="chantier.id"
                          :href="`/chantiers/${chantier.id}`"
                          class="bg-white dark:bg-zinc-900 rounded-2xl p-4 flex items-center justify-between border border-gray-50 dark:border-zinc-800 hover:border-primary/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-2 h-10 rounded-full" :class="{ 
                                'bg-sky-500': chantier.statut === 'en_cours',
                                'bg-emerald-500': chantier.statut === 'termine',
                                'bg-amber-500': chantier.statut === 'en_attente',
                                'bg-rose-500': chantier.statut === 'annule'
                            }"></div>
                            <div>
                                <p class="text-sm font-bold dark:text-white">{{ chantier.titre }}</p>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">{{ chantier.numero }}</p>
                            </div>
                        </div>
                        <ChevronRight class="w-4 h-4 text-gray-300" />
                    </Link>
                    <div v-if="!client.chantiers?.length" class="text-center py-8 bg-white/50 dark:bg-zinc-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-zinc-800">
                        <p class="text-xs text-gray-400">Aucun chantier actif</p>
                    </div>
                </div>

                <!-- Devis List -->
                <div class="flex items-center justify-between px-1 pt-4">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <FileText class="w-5 h-5 text-indigo-500" />
                        Devis récents
                    </h3>
                    <Link :href="`/devis/create?client_id=${client.id}`" 
                          class="flex items-center gap-1 text-primary text-xs font-bold uppercase tracking-wider">
                        <Plus class="w-3 h-3" /> Nouveau
                    </Link>
                </div>
                <div class="space-y-2 pb-10">
                    <Link v-for="devi in client.devis" :key="devi.id"
                          :href="`/devis/${devi.id}`"
                          class="bg-white dark:bg-zinc-900 rounded-2xl p-4 flex items-center justify-between border border-gray-50 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <FileText class="w-5 h-5 text-gray-400" />
                            <div>
                                <p class="text-sm font-bold dark:text-white">{{ devi.numero }}</p>
                                <p class="text-[10px] text-gray-400">{{ new Date(devi.date).toLocaleDateString('fr-FR') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                             <div class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase" :class="{
                                 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400': devi.statut === 'accepte',
                                 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400': devi.statut === 'envoye',
                                 'bg-gray-100 text-gray-600': devi.statut === 'brouillon'
                             }">{{ devi.statut }}</div>
                             <ChevronRight class="w-4 h-4 text-gray-300" />
                        </div>
                    </Link>
                    <div v-if="!client.devis?.length" class="text-center py-8 bg-white/50 dark:bg-zinc-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-zinc-800">
                        <p class="text-xs text-gray-400">Aucun devis enregistré</p>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
