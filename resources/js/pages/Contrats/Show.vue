<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import AppBar from '@/components/AppBar.vue';
import { 
    Printer, 
    Copy, 
    CheckCircle2, 
    Edit, 
    ShieldCheck,
    Trash2
} from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    contrat: Object,
    totalPaye: Number,
    solde: Number,
    generatedContent: String,
    contractTitle: String,
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
};

const printContrat = () => {
    window.print();
};

const duplicateContrat = () => {
    if (confirm('Dupliquer ce contrat ?')) {
        router.post(`/contrats/${props.contrat.id}/dupliquer`);
    }
};

const deleteContrat = () => {
    if (confirm('Supprimer définitivement ce contrat ?')) {
        router.delete(`/contrats/${props.contrat.id}`);
    }
};

const partyLabel = computed(() => {
    return props.contrat?.type === 'client' ? 'Le Client (Maître d\'ouvrage)' : 'Le Prestataire (Sous-traitant)';
});
</script>

<template>
    <Head :title="`Contrat ${contrat.numero}`" />

    <Layout>
        <AppBar :title="contrat.numero" 
                show-back-button 
                back-href="/contrats" 
                :actions="[
                    { icon: Printer, onClick: printContrat, class: 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/20' },
                    { icon: Edit, href: `/contrats/${contrat.id}/edit`, class: 'bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-2xl shadow-sm text-primary' },
                    { icon: Copy, onClick: duplicateContrat, class: 'bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-2xl shadow-sm text-indigo-500' },
                    { icon: Trash2, onClick: deleteContrat, class: 'bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-2xl shadow-sm text-rose-500' }
                ]" 
                class="print:hidden" />

        <!-- Professional Contract View -->
        <div class="space-y-6 pb-20">
            <!-- Header Card -->
            <div class="bg-zinc-900 text-white rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden print:bg-white print:text-black print:border print:border-zinc-100 print:p-10 print:rounded-none pr-10">
                <div class="relative z-10 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-black uppercase tracking-[0.3em] opacity-50 px-3 py-1 bg-white/10 rounded-full border border-white/10 print:hidden">Document Contractuel</span>
                        <div class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm bg-primary text-white flex items-center gap-1 print:border print:border-primary print:text-primary">
                            <CheckCircle2 class="w-3 h-3" />
                            {{ contrat.statut }}
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black uppercase tracking-tight leading-none mb-2">{{ contractTitle }}</h1>
                        <p class="text-xl font-bold text-primary tracking-widest">{{ contrat.numero }}</p>
                    </div>
                </div>
                <div class="absolute -right-16 -bottom-16 opacity-[0.05] pointer-events-none rotate-12">
                   <ShieldCheck class="w-[300px] h-[300px]" />
                </div>
            </div>

            <!-- Generated Content Document -->
            <div class="bg-white dark:bg-zinc-950 rounded-[2.5rem] p-10 shadow-sm border border-gray-100 dark:border-zinc-900 relative print:shadow-none print:border-none print:p-0 print:m-0 min-h-[600px]">
                <!-- Watermark -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.02] dark:opacity-[0.04] pointer-events-none -rotate-12">
                    <h2 class="text-9xl font-black uppercase whitespace-nowrap">DadouConfort</h2>
                </div>

                <!-- Professional Text Content -->
                <div class="relative z-10 prose dark:prose-invert max-w-none contract-content">
                    <div class="text-justify text-sm leading-[1.8] font-medium text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap select-text px-8 md:px-16 py-4">
                        {{ generatedContent }}
                    </div>
                </div>

                <!-- OHADA Legal Notice -->
                <div class="mt-16 pt-8 border-t border-zinc-100 dark:border-zinc-900 grid grid-cols-2 gap-10">
                    <div class="text-center space-y-12">
                         <p class="text-[10px] font-black uppercase text-zinc-400 border-b border-zinc-50 dark:border-zinc-900 pb-2">Signature DadouConfort</p>
                         <div class="h-24 border-b border-dashed border-zinc-200 dark:border-zinc-800 mx-6 relative overflow-hidden">
                             <!-- Digital Stamp -->
                             <div class="absolute right-0 bottom-0 opacity-10 -rotate-12">
                                 <ShieldCheck class="w-20 h-20 text-primary" />
                             </div>
                         </div>
                    </div>
                    <div class="text-center space-y-12">
                         <p class="text-[10px] font-black uppercase text-zinc-400 border-b border-zinc-50 dark:border-zinc-900 pb-2">Signature {{ partyLabel }}</p>
                         <div class="h-24 border-b border-dashed border-zinc-200 dark:border-zinc-800 mx-6"></div>
                    </div>
                </div>

                <div class="mt-16 text-center space-y-2 opacity-30 select-none">
                    <p class="text-[8px] font-black uppercase tracking-[0.2em]">DadouConfort ERP - Pilotage d'Attribution et Contrôle Digital</p>
                    <p class="text-[7px] text-zinc-400">Document généré le {{ new Date().toLocaleString() }} - Authenticité vérifiable par QR Code interne</p>
                </div>
            </div>

            <!-- Financial Quick Stats (Hidden in Print) -->
            <div class="grid grid-cols-2 gap-4 print:hidden px-2">
                <div class="bg-indigo-500 text-white rounded-[2rem] p-6 shadow-xl shadow-indigo-500/20">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-1">Montant Convenu</p>
                    <p class="text-xl font-black">{{ formatCurrency(contrat.montant_convenu).replace(',00', '') }}<span class="text-[10px] ml-1">XAF</span></p>
                </div>
                <div class="bg-zinc-800 text-white rounded-[2rem] p-6 shadow-xl">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-1">Acompte à la signature</p>
                    <p class="text-xl font-black text-amber-400">{{ formatCurrency(contrat.acompte).replace(',00', '') }}<span class="text-[10px] ml-1">XAF</span></p>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
@media print {
    body { 
        background: white !important; 
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact; 
    }
    .print\:hidden { display: none !important; }
    main { padding: 0 !important; max-width: none !important; margin: 0 !important; }
    header, nav { display: none !important; }
    .prose { font-family: "Inter", "Segoe UI", sans-serif !important; }
    @page { margin: 1.5cm; size: A4; }
    .rounded-\[2\.5rem\] { border-radius: 0 !important; }
}

.contract-content ul, 
.contract-content ol {
    list-style-type: none !important;
    padding-left: 0 !important;
}

.contract-content li {
    margin-bottom: 0.5rem;
}
</style>
