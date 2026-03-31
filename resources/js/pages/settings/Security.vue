<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { 
    Shield, 
    ArrowLeft,
    Check,
    Lock,
    Eye,
    EyeOff
} from 'lucide-vue-next';
import { ref } from 'vue';

const showCurrent = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('user-password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
            }
            if (form.errors.current_password) {
                form.reset('current_password');
            }
        },
    });
};
</script>

<template>
    <Head title="Sécurité" />

    <Layout>
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between mb-8">
            <Link href="/settings/profile" class="p-2.5 bg-white dark:bg-zinc-900 rounded-xl shadow-sm hover:scale-105 active:scale-95 transition-transform">
                <ArrowLeft class="w-5 h-5 text-gray-500" />
            </Link>
            <h2 class="text-sm font-black dark:text-white uppercase tracking-[0.2em] leading-none">Sécurité</h2>
            <button @click="submit" 
                    :disabled="form.processing"
                    class="bg-emerald-500 text-white p-2.5 rounded-xl shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 disabled:opacity-50 transition-all">
                <Check class="w-5 h-5" v-if="!form.processing" />
                <div v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            </button>
        </div>

        <!-- Header Icon Section -->
        <div class="flex flex-col items-center py-6 mb-4">
            <div class="w-20 h-20 rounded-[2rem] bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shadow-xl border border-emerald-100 dark:border-emerald-900/30 mb-4">
                <Shield class="w-10 h-10" />
            </div>
            <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest leading-none">Protection du compte</p>
        </div>

        <!-- Form Section -->
        <form @submit.prevent="submit" class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                
                <!-- Mot de passe actuel -->
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Mot de passe actuel</label>
                    <div class="relative">
                        <Lock class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input v-model="form.current_password" :type="showCurrent ? 'text' : 'password'"
                               class="w-full pl-12 pr-12 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-sm font-bold dark:text-white shadow-inner focus:ring-2 focus:ring-emerald-500/20 transition-all" />
                        <button type="button" @click="showCurrent = !showCurrent" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <Eye v-if="!showCurrent" class="w-4 h-4" />
                            <EyeOff v-else class="w-4 h-4" />
                        </button>
                    </div>
                    <p v-if="form.errors.current_password" class="text-[10px] text-rose-500 font-bold px-2">{{ form.errors.current_password }}</p>
                </div>

                <div class="h-[1px] bg-gray-50 dark:bg-zinc-800"></div>

                <!-- Nouveau mot de passe -->
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Nouveau mot de passe</label>
                    <div class="relative">
                        <Lock class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input v-model="form.password" :type="showNew ? 'text' : 'password'"
                               class="w-full pl-12 pr-12 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-sm font-bold dark:text-white shadow-inner focus:ring-2 focus:ring-emerald-500/20 transition-all" />
                        <button type="button" @click="showNew = !showNew" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <Eye v-if="!showNew" class="w-4 h-4" />
                            <EyeOff v-else class="w-4 h-4" />
                        </button>
                    </div>
                    <p v-if="form.errors.password" class="text-[10px] text-rose-500 font-bold px-2">{{ form.errors.password }}</p>
                </div>

                <!-- Confirmation -->
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Confirmer le nouveau mot de passe</label>
                    <div class="relative">
                        <Lock class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input v-model="form.password_confirmation" :type="showConfirm ? 'text' : 'password'"
                               class="w-full pl-12 pr-12 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-sm font-bold dark:text-white shadow-inner focus:ring-2 focus:ring-emerald-500/20 transition-all" />
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <Eye v-if="!showConfirm" class="w-4 h-4" />
                            <EyeOff v-else class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-amber-50 dark:bg-amber-950/20 rounded-3xl border border-amber-100 dark:border-amber-900/20">
                <p class="text-[10px] text-amber-700 dark:text-amber-400 font-medium leading-relaxed">
                    <strong>Conseil :</strong> Utilisez un mot de passe d'au moins 8 caractères mélangeant lettres, chiffres et symboles pour une sécurité maximale.
                </p>
            </div>

            <div class="pt-10 pb-20">
                <button @click="submit" :disabled="form.processing"
                        class="w-full p-5 bg-emerald-500 text-white rounded-3xl font-black text-xs uppercase tracking-[0.3em] flex items-center justify-center gap-3 active:scale-95 transition-all shadow-xl shadow-emerald-500/20 disabled:opacity-50">
                    Mettre à jour la sécurité
                </button>
            </div>
        </form>
    </Layout>
</template>
