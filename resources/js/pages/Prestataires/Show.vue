<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    Phone, 
    MapPin, 
    HardHat, 
    CreditCard, 
    ChevronRight, 
    Plus, 
    Edit, 
    ArrowLeft,
    MessagesSquare,
    Briefcase,
    DollarSign,
    BadgeCheck,
    Clock,
    TrendingUp
} from 'lucide-vue-next';

const props = defineProps({
    prestataire: Object,
    totalPaye: Number,
    totalConvenu: Number,
    soldeDu: Number,
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};

const callPrestataire = () => {
    if (props.prestataire.telephone) {
        window.location.href = `tel:${props.prestataire.telephone}`;
    }
};

const messageWhatsApp = () => {
    if (props.prestataire.telephone) {
        window.location.href = `https://wa.me/${props.prestataire.telephone.replace(/\s+/g, '')}`;
    }
};

const getStatusClasses = (status) => {
    switch (status) {
        case 'disponible': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400';
        case 'en_mission': return 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400';
        case 'indisponible': return 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400';
        default: return 'bg-gray-100 text-gray-700';
    }
};
</script>

<template>
    <Head :title="prestataire.nom_complet" />

    <Layout>
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between mb-2">
            <Link href="/prestataires" class="flex items-center gap-2 text-gray-400 transition-colors">
                <div class="p-2 bg-white dark:bg-zinc-900 rounded-full shadow-sm">
                    <ArrowLeft class="w-5 h-5" />
                </div>
            </Link>
            <h2 class="text-xl font-bold dark:text-white">Fiche Prestataire</h2>
            <Link :href="`/prestataires/${prestataire.id}/edit`" 
                  class="flex items-center gap-2 text-gray-400">
                <div class="p-2 bg-white dark:bg-zinc-900 rounded-full shadow-sm">
                    <Edit class="w-5 h-5" />
                </div>
            </Link>
        </div>

        <!-- Prestataire Profile Header -->
        <div class="text-center py-6">
            <div class="relative inline-block">
                <div class="w-24 h-24 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 rounded-full flex items-center justify-center text-3xl font-bold mb-4 border-4 border-white dark:border-zinc-950 shadow-xl overflow-hidden">
                    <Briefcase class="w-10 h-10" />
                </div>
                <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full border-2 border-white dark:border-zinc-950 flex items-center justify-center shadow-lg" :class="prestataire.disponibilite === 'disponible' ? 'bg-emerald-500' : 'bg-amber-500'">
                    <BadgeCheck class="w-5 h-5 text-white" />
                </div>
            </div>
            <h1 class="text-2xl font-bold dark:text-white">{{ prestataire.nom_complet }}</h1>
            <div class="flex items-center justify-center gap-2 mt-2">
                <span class="text-xs font-bold text-primary uppercase tracking-widest">{{ prestataire.specialite || 'Spécialité libre' }}</span>
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                <span class="text-[10px] py-0.5 px-3 rounded-full font-bold uppercase" :class="getStatusClasses(prestataire.disponibilite)">
                    {{ prestataire.disponibilite.replace('_', ' ') }}
                </span>
            </div>

            <!-- Communication Quick Actions -->
            <div class="flex justify-center gap-6 mt-8">
                <button @click="callPrestataire" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-white dark:bg-zinc-900 text-gray-600 dark:text-zinc-400 flex items-center justify-center shadow-sm border border-gray-100 dark:border-zinc-800 transition-all active:scale-95 group-active:translate-y-1">
                        <Phone class="w-7 h-7" />
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 dark:text-zinc-500 uppercase">Appeler</span>
                </button>
                <button @click="messageWhatsApp" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-white dark:bg-zinc-900 text-emerald-500 flex items-center justify-center shadow-sm border border-gray-100 dark:border-zinc-800 transition-all active:scale-95 group-active:translate-y-1">
                        <MessagesSquare class="w-7 h-7" />
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 dark:text-zinc-500 uppercase">WhatsApp</span>
                </button>
            </div>
        </div>

        <!-- Stats Grid Dashboard style -->
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-indigo-50 dark:bg-indigo-950/20 p-4 rounded-3xl text-center flex flex-col items-center justify-center">
                <span class="text-[10px] font-bold text-indigo-400 dark:text-indigo-600 uppercase tracking-tighter">Convenu</span>
                <span class="text-sm font-bold text-indigo-700 dark:text-indigo-300">{{ formatCurrency(totalConvenu).replace(',00', '') }}</span>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-950/20 p-4 rounded-3xl text-center flex flex-col items-center justify-center">
                <span class="text-[10px] font-bold text-emerald-400 dark:text-emerald-600 uppercase tracking-tighter">Payé</span>
                <span class="text-sm font-bold text-emerald-700 dark:text-emerald-300">{{ formatCurrency(totalPaye).replace(',00', '') }}</span>
            </div>
            <div class="bg-rose-50 dark:bg-rose-950/20 p-4 rounded-3xl text-center flex flex-col items-center justify-center border border-rose-100/30">
                <span class="text-[10px] font-bold text-rose-400 dark:text-rose-600 uppercase tracking-tighter">Solde</span>
                <span class="text-sm font-bold text-rose-700 dark:text-rose-300">{{ formatCurrency(soldeDu).replace(',00', '') }}</span>
            </div>
        </div>

        <!-- Details -->
        <div class="space-y-4">
             <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-zinc-800 flex items-center justify-center text-gray-400">
                        <DollarSign class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Base tarifaire</p>
                        <p class="text-sm font-bold dark:text-white">{{ prestataire.tarif_indicatif || 'Non renseigné' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-zinc-800 flex items-center justify-center text-gray-400">
                        <MapPin class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Zone / Adresse</p>
                        <p class="text-sm font-bold dark:text-white leading-tight">{{ prestataire.adresse || 'Aucune adresse' }}</p>
                    </div>
                </div>
                <div v-if="prestataire.notes" class="pt-4 border-t border-gray-50 dark:border-zinc-800">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Notes</p>
                    <p class="text-sm text-gray-600 dark:text-zinc-400 italic leading-snug">{{ prestataire.notes }}</p>
                </div>
            </div>

            <!-- Interventions History -->
            <div class="space-y-3 pt-4">
                <h3 class="font-bold text-gray-900 dark:text-white px-2 flex items-center gap-2">
                    <HardHat class="w-5 h-5 text-amber-500" />
                    Interventions ({{ prestataire.chantiers?.length || 0 }})
                </h3>
                <div class="space-y-2">
                    <div v-for="chantier in prestataire.chantiers" :key="chantier.id"
                         class="bg-white dark:bg-zinc-900 rounded-2xl p-4 flex items-center justify-between border border-gray-100 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-8 rounded-full bg-indigo-500"></div>
                            <div>
                                <p class="text-sm font-bold dark:text-white">{{ chantier.titre }}</p>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Rôle : {{ chantier.pivot.role || 'Général' }}</p>
                            </div>
                        </div>
                        <ChevronRight class="w-4 h-4 text-gray-300" />
                    </div>
                    <div v-if="!prestataire.chantiers?.length" class="text-center py-6 bg-white/50 dark:bg-zinc-900/50 rounded-2xl border border-dashed border-gray-100 dark:border-zinc-800">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Aucune intervention enregistrée</p>
                    </div>
                </div>
            </div>

            <!-- Payments History -->
            <div class="p-2 pt-6">
                <Link href="/paiements/create" class="w-full py-4 bg-primary text-primary-foreground rounded-2xl flex items-center justify-center gap-2 font-bold transition-transform active:scale-95 shadow-lg shadow-primary/20">
                    <CreditCard class="w-5 h-5" />
                    Enregistrer un paiement
                </Link>
            </div>
            
            <p class="text-[10px] text-gray-400 uppercase text-center font-bold tracking-[0.2em] pt-8 pb-12">Système de gestion DadouConfort</p>
        </div>
    </Layout>
</template>
