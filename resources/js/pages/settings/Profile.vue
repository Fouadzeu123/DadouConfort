<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Layout from '@/layouts/MobileLayout.vue';
import { ref, computed } from 'vue';
import { 
    User, 
    Mail,
    ArrowLeft,
    Check,
    Camera,
    Shield,
    Palette,
    LogOut,
    ChevronRight
} from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth.user as any);

const photoInput = ref(null);
const photoPreview = ref(null);

const form = useForm({
    _method: 'PATCH',
    name: user.value.name,
    email: user.value.email,
    photo: null,
});

const submit = () => {
    if (photoInput.value?.files[0]) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
             clearPhotoFileInput();
             photoPreview.value = null;
        },
    });
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <Head title="Mon Profil" />

    <Layout>
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between mb-8">
            <Link href="/dashboard" class="p-2.5 bg-white dark:bg-zinc-900 rounded-xl shadow-sm hover:scale-105 active:scale-95 transition-transform">
                <ArrowLeft class="w-5 h-5 text-gray-500" />
            </Link>
            <h2 class="text-sm font-black dark:text-white uppercase tracking-[0.2em] leading-none">Mon Profil</h2>
            <button @click="submit" 
                    :disabled="form.processing"
                    class="bg-primary text-white p-2.5 rounded-xl shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 disabled:opacity-50 transition-all">
                <Check class="w-5 h-5" v-if="!form.processing" />
                <div v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            </button>
        </div>

        <!-- Avatar Section -->
        <div class="flex flex-col items-center py-6">
            <div class="relative group">
                <!-- Hidden File Input -->
                <input type="file" class="hidden" ref="photoInput" @change="updatePhotoPreview">
                
                <div class="w-28 h-28 rounded-[2.5rem] bg-indigo-50 dark:bg-indigo-950/30 text-indigo-500 flex items-center justify-center shadow-xl border-4 border-white dark:border-zinc-900 overflow-hidden transition-all group-hover:rotate-3">
                    <!-- New Photo Preview -->
                    <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                    <!-- Current Profile Photo -->
                    <img v-else-if="user.profile_photo_url" :src="user.profile_photo_url" class="w-full h-full object-cover">
                    <!-- Initial Letter -->
                    <span v-else class="text-4xl font-black">{{ user.name.charAt(0).toUpperCase() }}</span>
                </div>
                
                <button type="button" @click="selectNewPhoto"
                        class="absolute -bottom-2 -right-2 bg-white dark:bg-zinc-800 p-2.5 rounded-2xl shadow-lg border border-gray-100 dark:border-zinc-700 text-gray-500 hover:text-primary transition-colors">
                    <Camera class="w-4 h-4" />
                </button>
            </div>
            <h3 class="mt-4 font-black text-xl dark:text-white">{{ user.name }}</h3>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mt-1">Gérant DadouConfort</p>
        </div>

        <!-- Form Section -->
        <form @submit.prevent="submit" class="space-y-6 mt-8">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-zinc-800 space-y-6">
                <!-- Nom Complet -->
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Nom Complet</label>
                    <div class="relative">
                        <User class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input v-model="form.name" type="text"
                               class="w-full pl-12 pr-4 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-sm font-bold dark:text-white shadow-inner focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <p v-if="form.errors.name" class="text-[10px] text-rose-500 font-bold px-2">{{ form.errors.name }}</p>
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Adresse Email</label>
                    <div class="relative">
                        <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input v-model="form.email" type="email"
                               class="w-full pl-12 pr-4 bg-gray-50 dark:bg-zinc-800 border-none rounded-2xl p-4 text-sm font-bold dark:text-white shadow-inner focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <p v-if="form.errors.email" class="text-[10px] text-rose-500 font-bold px-2">{{ form.errors.email }}</p>
                </div>
                
                <p v-if="form.errors.photo" class="text-[10px] text-rose-500 font-bold px-2">{{ form.errors.photo }}</p>
            </div>

            <!-- Additional Settings Links -->
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-2 shadow-sm border border-gray-100 dark:border-zinc-800 overflow-hidden">
                <Link href="/settings/security" class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-500 flex items-center justify-center">
                            <Shield class="w-5 h-5" />
                        </div>
                        <span class="text-sm font-bold dark:text-zinc-300">Sécurité et Mot de passe</span>
                    </div>
                    <ChevronRight class="w-4 h-4 text-gray-300 dark:text-zinc-600 group-hover:translate-x-1 transition-transform" />
                </Link>

                <div class="h-[1px] bg-gray-100 dark:bg-zinc-800 mx-4"></div>

                <Link href="/settings/appearance" class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/20 text-orange-500 flex items-center justify-center">
                            <Palette class="w-5 h-5" />
                        </div>
                        <span class="text-sm font-bold dark:text-zinc-300">Apparence de l'application</span>
                    </div>
                    <ChevronRight class="w-4 h-4 text-gray-300 dark:text-zinc-600 group-hover:translate-x-1 transition-transform" />
                </Link>
            </div>

            <!-- Logout -->
            <div class="pt-4 pb-12">
                <Link href="/logout" method="post" as="button" 
                      class="w-full p-5 bg-rose-50 dark:bg-rose-950/10 text-rose-500 rounded-3xl font-black text-xs uppercase tracking-[0.3em] flex items-center justify-center gap-3 active:scale-95 transition-all shadow-sm border border-rose-100 dark:border-rose-900/10">
                    <LogOut class="w-5 h-5" />
                    Déconnexion
                </Link>
            </div>
        </form>

        <p class="text-[8px] font-black text-gray-300 uppercase text-center tracking-[0.4em] pb-10 select-none">DadouConfort v1.0 • Yaoundé, CMR</p>
    </Layout>
</template>
