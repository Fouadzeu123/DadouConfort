<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    FileText, 
    Printer, 
    Copy, 
    CheckCircle2, 
    XCircle, 
    Edit, 
    ArrowLeft, 
    ArrowRight,
    Mail, 
    MessageSquare,
    Files,
    MoreVertical,
    Check,
    Briefcase,
    Calendar,
    ChevronRight,
    Hammer
} from 'lucide-vue-next';
import { ref } from 'vue';
import Layout from '@/layouts/MobileLayout.vue';

const props = defineProps<{
    devi: any;
}>();

const formatCurrency = (val) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(val);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
};

const printDevis = () => {
    window.open(`/devis/${props.devi.id}/pdf`, '_blank');
};

const duplicateDevis = () => {
    if (confirm('Dupliquer ce devis ?')) {
        router.post(`/devis/${props.devi.id}/dupliquer`);
    }
};

const convertToContract = () => {
    if (confirm('Convertir ce devis accepté en contrat et créer le chantier correspondant ?')) {
        router.post(`/devis/${props.devi.id}/convertir-contrat`);
    }
};

const updateStatus = (status) => {
    router.put(`/devis/${props.devi.id}`, { statut: status });
};

const showActions = ref(false);
</script>

<template>
    <Head :title="`Devis ${devi.numero}`" />

    <Layout>
        <!-- Mobile Action Toolbar (Sticky) -->
        <div class="flex items-center justify-between mb-4 sticky top-14 z-30 bg-gray-50/90 dark:bg-zinc-950/90 backdrop-blur-md py-4 -mx-4 px-4 border-b border-gray-100 dark:border-zinc-900 print:hidden transition-all">
            <Link href="/devis" class="p-2.5 bg-white dark:bg-zinc-900 rounded-full shadow-sm hover:scale-105 transition-transform active:scale-95">
                <ArrowLeft class="w-5 h-5 text-gray-500" />
            </Link>
            <div class="flex items-center gap-3">
                <button @click="printDevis" class="p-2.5 bg-white dark:bg-zinc-900 rounded-full shadow-sm text-indigo-500 hover:scale-110 active:scale-95 transition-all">
                    <Printer class="w-5 h-5" />
                </button>
                <div class="relative">
                    <button @click="showActions = !showActions" class="p-2.5 bg-white dark:bg-zinc-900 rounded-full shadow-sm text-primary hover:scale-110 active:scale-95 transition-all">
                        <MoreVertical class="w-5 h-5" />
                    </button>
                    <!-- Action Menu Float -->
                    <div v-if="showActions" 
                         @click="showActions = false"
                         class="absolute right-0 top-12 w-48 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-zinc-800 z-50 p-2 space-y-1 animate-in zoom-in-95 duration-200">
                        <Link :href="`/devis/${devi.id}/edit`" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-xl text-xs font-bold dark:text-gray-300">
                            <Edit class="w-4 h-4 text-primary" /> Modifier
                        </Link>
                        <button @click="duplicateDevis" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-xl text-xs font-bold dark:text-gray-300">
                            <Copy class="w-4 h-4 text-indigo-500" /> Dupliquer
                        </button>
                        <div class="h-px bg-gray-50 dark:bg-zinc-800 my-1 mx-2"></div>
                        <button v-if="devi.statut !== 'accepte'" @click="updateStatus('accepte')" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 rounded-xl text-xs font-bold text-emerald-600">
                            <CheckCircle2 class="w-4 h-4" /> Marquer accepté
                        </button>
                        <button v-if="devi.statut !== 'refuse'" @click="updateStatus('refuse')" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-rose-50 dark:hover:bg-rose-900/10 rounded-xl text-xs font-bold text-rose-500">
                            <XCircle class="w-4 h-4" /> Marquer refusé
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Devis Main Card (Document Style) -->
        <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-8 mb-6 relative overflow-hidden print:shadow-none print:border-none print:p-4 print:m-0">
            <!-- Professional Watermark (Dark mode only) -->
            <div class="absolute -top-10 -right-10 opacity-5 dark:opacity-5 grayscale pointer-events-none">
                <FileText class="w-80 h-80 rotate-12" />
            </div>

            <!-- Header Info -->
            <div class="relative z-10 flex flex-col gap-6">
                <!-- Status Badge Label -->
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">DEVIS</h1>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ devi.numero }}</p>
                    </div>
                    <div class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-zinc-800 dark:text-white">
                        {{ devi.statut }}
                    </div>
                </div>

                <!-- Companies Info Grid -->
                <div class="grid grid-cols-2 gap-8 pt-4">
                    <div class="space-y-3">
                        <p class="text-[9px] font-black text-primary dark:text-primary uppercase tracking-[0.2em] mb-2 pb-1 border-b border-primary/20 inline-block">De la part de</p>
                        <div class="space-y-1">
                            <p class="text-sm font-black dark:text-white">DadouConfort</p>
                            <p class="text-[10px] text-gray-500 dark:text-zinc-500 font-bold uppercase leading-relaxed">Artisan Menuisier & Services<br/>Yaoundé, Cameroun</p>
                            <p class="text-[10px] text-primary font-black uppercase pt-1">Tél: 672 320 021</p>
                        </div>
                    </div>
                    <div class="space-y-3 text-right">
                        <p class="text-[9px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-2 pb-1 border-b border-indigo-500/20 inline-block">Destiné à</p>
                        <div class="space-y-1">
                            <p class="text-sm font-black dark:text-white uppercase">{{ devi.client?.nom_complet }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-zinc-500 font-bold leading-[1.3]">{{ devi.client?.adresse || 'Adresse non renseignée' }}</p>
                            <p class="text-[10px] text-indigo-500 font-black uppercase pt-1">{{ devi.client?.telephone }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Dates Bar -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 p-4 rounded-2xl flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-zinc-500">
                <div class="flex items-center gap-2">
                    <Calendar class="w-4 h-4 text-gray-300" />
                    <span>Emis le {{ formatDate(devi.date) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <Check class="w-4 h-4 text-emerald-500" />
                    <span>Validité 30 jours</span>
                </div>
            </div>

            <!-- TABLE OF SERVICES (The heart of the document) -->
            <div class="space-y-4">
                <div class="grid grid-cols-12 gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest px-2 items-end">
                    <div class="col-span-1 text-center font-sans pr-2">#</div>
                    <div class="col-span-6">Désignation / Ouvrage</div>
                    <div class="col-span-2 text-center">Quantité</div>
                    <div class="col-span-3 text-right">Total (FCFA)</div>
                </div>

                <div class="space-y-6">
                    <div v-for="(ligne, index) in devi.lignes" :key="ligne.id" 
                         class="grid grid-cols-12 gap-2 items-start py-4 border-t border-gray-50/50 dark:border-zinc-800/50 last:border-b last:border-gray-100/50">
                        <div class="col-span-1 text-center text-[9px] font-black font-sans text-gray-300 pt-1">{{ index + 1 }}</div>
                        <div class="col-span-6">
                            <p class="text-xs font-bold leading-relaxed dark:text-white pr-2">{{ ligne.designation }}</p>
                            <p v-if="ligne.description" class="text-[10px] text-gray-500 dark:text-zinc-500 mt-1 italic">{{ ligne.description }}</p>
                            <p class="text-[8px] text-gray-400 uppercase font-black tracking-widest mt-1">{{ formatCurrency(ligne.prix_unitaire).replace(',00', '') }} / {{ ligne.unite }}</p>
                        </div>
                        <div class="col-span-2 text-center text-[11px] font-black dark:text-white pt-1">
                             {{ ligne.quantite }} <span class="text-[8px] text-gray-400 font-bold ml-0.5">{{ ligne.unite }}</span>
                        </div>
                        <div class="col-span-3 text-right text-xs font-black dark:text-white pt-1">
                             {{ formatCurrency(ligne.quantite * ligne.prix_unitaire).replace(',00', '') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Recap Section -->
            <div class="flex justify-end pt-4">
                <div class="w-full max-w-[240px] space-y-3">
                    <div class="flex justify-between text-[11px] font-bold text-gray-500">
                        <span class="uppercase tracking-widest">Sous-Total Hors Taxes</span>
                        <span class="dark:text-white">{{ formatCurrency(devi.sous_total).replace(',00', '') }}</span>
                    </div>

                    <div v-if="devi.cout_main_oeuvre > 0" class="flex justify-between text-[11px] font-bold text-gray-500">
                        <span class="uppercase tracking-widest">Main d'œuvre</span>
                        <span class="dark:text-white">+{{ formatCurrency(devi.cout_main_oeuvre).replace(',00', '') }}</span>
                    </div>
                    
                    <div v-if="devi.cout_transport > 0" class="flex justify-between text-[11px] font-bold text-gray-500">
                        <span class="uppercase tracking-widest">Transport</span>
                        <span class="dark:text-white">+{{ formatCurrency(devi.cout_transport).replace(',00', '') }}</span>
                    </div>

                    <div v-if="devi.remise > 0" class="flex justify-between text-[11px] font-bold text-rose-500 italic pb-2 border-b border-gray-100 dark:border-zinc-800">
                        <span class="uppercase font-black tracking-widest">Remise commerciale</span>
                        <span class="font-black">-{{ formatCurrency(devi.remise).replace(',00', '') }}</span>
                    </div>

                    <div class="flex justify-between items-center py-4 px-5 bg-indigo-950 text-white dark:bg-zinc-800 rounded-3xl shadow-xl shadow-indigo-950/20 transform hover:-translate-y-1 transition-all">
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] opacity-60">TOTAL À PAYER</span>
                        <div class="text-right">
                             <p class="text-xl font-black">{{ formatCurrency(devi.total_general).replace(',00', '') }}</p>
                             <p class="text-[8px] font-bold opacity-40 uppercase">Toutes Taxes Comprises (XAF)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Final Professional Notes -->
            <div v-if="devi.notes" class="pt-8 space-y-3">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-2 border-l-2 border-primary/20">Conditions & Notes</p>
                <div class="p-5 bg-gray-50/50 dark:bg-white/5 rounded-3xl border border-gray-100 dark:border-zinc-800">
                     <p class="text-[11px] font-medium leading-relaxed dark:text-zinc-400 italic">"{{ devi.notes }}"</p>
                </div>
            </div>

            <!-- Signature Area (for Print) -->
            <div class="hidden print:grid grid-cols-2 gap-20 pt-20 pb-10">
                <div class="text-center space-y-12">
                     <p class="text-[10px] font-black uppercase border-b border-gray-100 pb-2">Signature Client</p>
                     <div class="h-24"></div>
                </div>
                <div class="text-center space-y-12">
                     <p class="text-[10px] font-black uppercase border-b border-gray-100 pb-2">Cachet DadouConfort</p>
                     <div class="h-24 grayscale opacity-20">
                          <!-- Image cachet placeholder -->
                          <Files class="w-20 h-20 mx-auto" />
                     </div>
                </div>
            </div>

            <p class="text-[9px] text-gray-400 font-bold uppercase text-center mt-20 opacity-30 select-none">Édité via Système DadouConfort - Artisan Premium</p>
        </div>

        <!-- Acceptance Promotion (If Accepted but no Contract) -->
        <div v-if="devi.statut === 'accepte' && !devi.chantier_id" 
             class="p-4 mb-20 print:hidden bg-emerald-50 dark:bg-emerald-950/30 rounded-3xl border border-emerald-100 dark:border-emerald-900/30 flex items-center justify-between group">
            <div class="flex items-center gap-4">
                 <div class="w-12 h-12 bg-white dark:bg-emerald-800 rounded-2xl flex items-center justify-center text-emerald-500 shadow-sm animate-pulse">
                      <Hammer class="w-6 h-6" />
                 </div>
                 <div>
                      <p class="text-xs font-black dark:text-emerald-300 uppercase tracking-[0.1em]">Devis Approuvé !</p>
                      <p class="text-[10px] text-emerald-600/80 font-bold leading-tight">Voulez-vous démarrer le chantier ?</p>
                 </div>
            </div>
            <button @click="convertToContract" class="p-3 bg-emerald-500 text-white rounded-2xl shadow-lg transform active:scale-90 transition-transform group-hover:scale-105">
                 <ArrowRight class="w-5 h-5" />
            </button>
        </div>

        <!-- Contract Link (If exists) -->
        <div v-if="devi.chantier" class="p-6 mb-20 bg-indigo-50 dark:bg-indigo-950/20 rounded-[2rem] border border-indigo-100 dark:border-indigo-900/30 group animate-in slide-in-from-bottom-5">
             <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center gap-2">
                       <Files class="w-5 h-5 text-indigo-500" />
                       <h4 class="text-xs font-black text-indigo-900 dark:text-indigo-400 uppercase tracking-widest">Contrat en vigueur</h4>
                  </div>
                  <CheckCircle2 class="w-5 h-5 text-emerald-500" />
             </div>
             <p class="text-[10px] font-bold text-gray-500 mb-4 px-1">Ce devis est rattaché au chantier actif :</p>
             <Link :href="`/chantiers/${devi.chantier.id}`" 
                   class="bg-white dark:bg-zinc-800 p-4 rounded-2xl flex items-center justify-between text-sm font-black shadow-sm group-hover:shadow-md transition-shadow">
                  <span class="dark:text-white truncate max-w-[200px]">{{ devi.chantier.titre }}</span>
                  <ChevronRight class="w-4 h-4 text-indigo-500 transform group-hover:translate-x-1 transition-transform" />
             </Link>
        </div>

        <div class="h-10 print:hidden"></div>
    </Layout>
</template>

<style>
@media print {
    body { background: white !important; }
    .print\:hidden { display: none !important; }
    main { padding: 0 !important; max-width: none !important; }
    .rounded-\[2\.5rem\] { border-radius: 0 !important; }
    header, nav { display: none !important; }
    @page { margin: 1cm; size: A4; }
}
</style>
