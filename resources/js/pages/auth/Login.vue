<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Loader2, Lock, Mail, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword?: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion - DadouConfort" />

    <div class="min-h-screen flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-zinc-950">
        <div class="w-full max-w-md">
            <!-- Logo area -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-primary text-white rounded-[2rem] shadow-2xl shadow-primary/20 mb-6 font-black text-3xl">
                    <img src="/img/logo.jpeg" alt="Logo" class="w-full h-full object-cover rounded-[2rem]">
                </div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">DadouConfort</h1>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-2">Artisan Premium Management</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-zinc-800">
                <div v-if="status" class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-2xl border border-emerald-100 dark:border-emerald-900/30">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email field -->
                    <div class="space-y-2">
                        <label for="email" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Adresse Email</label>
                        <div class="relative group">
                            <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors" />
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="votre@email.com"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary/20 dark:text-white transition-all outline-none"
                            >
                        </div>
                        <p v-if="form.errors.email" class="text-[10px] font-black text-rose-500 mt-1 ml-1 uppercase tracking-wider">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password field -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label for="password" class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Mot de passe</label>
                            <button v-if="canResetPassword" type="button" class="text-[10px] font-black text-primary uppercase tracking-widest hover:opacity-70">Oublié ?</button>
                        </div>
                        <div class="relative group">
                            <Lock class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors" />
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full pl-12 pr-12 py-4 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary/20 dark:text-white transition-all outline-none"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                            >
                                <Eye v-if="!showPassword" class="w-5 h-5" />
                                <EyeOff v-else class="w-5 h-5" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="text-[10px] font-black text-rose-500 mt-1 ml-1 uppercase tracking-wider">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-3 ml-1">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="form.remember" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 dark:bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                        <span class="text-[10px] font-black text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Se souvenir de moi</span>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-5 bg-primary text-white rounded-3xl font-black text-xs uppercase tracking-[0.3em] flex items-center justify-center gap-3 active:scale-95 transition-all shadow-xl shadow-primary/20 disabled:opacity-50 disabled:active:scale-100"
                    >
                        <Loader2 v-if="form.processing" class="w-5 h-5 animate-spin" />
                        Se Connecter
                    </button>
                </form>
            </div>

            <!-- Footer info -->
            <p class="text-center mt-10 text-[8px] font-black text-gray-400 dark:text-zinc-600 uppercase tracking-[0.4em]">Propulsé par DadouConfort ERP • v1.0</p>
        </div>
    </div>
</template>
