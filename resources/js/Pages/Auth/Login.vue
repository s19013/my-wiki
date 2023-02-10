<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeCheckbox from '@/Components/Checkbox.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, onMounted, onUnmounted,nextTick, reactive } from 'vue';
import { useStore } from "vuex";

const store = useStore()

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    // アプリケーションのCSRF保護を初期化する必要があるらしい。
    axios.get('/sanctum/csrf-cookie').then(response => {})
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const japanese = reactive({
    email:"メールアドレス",
    password:"パスワード",
    RememberMe:"ログインしたままにする",
    ForgotPassword:"パスワードをわすれましたか?",
    Register:"新規登録",
    login:"ログイン"
})

const messages = reactive({
    email:"Email",
    password:"Password",
    RememberMe:"Remember me",
    ForgotPassword:"Forgot your password",
    Register:"Register",
    login:"log in"
})

onMounted(() => {
    nextTick(() => {
        if (store.state.lang == "ja"){Object.assign(messages,japanese)}
    })
})
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Log in" />

        <BreezeValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <BreezeLabel for="email" :value="messages.email" />
                <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <BreezeLabel for="password" :value="messages.password" />
                <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <BreezeCheckbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600">{{ messages.RememberMe  }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4 gap-2">
                <Link :href="route('register')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    <p>{{ messages.Register }}</p>
                </Link>

                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    <p>{{ messages.ForgotPassword }}</p>
                </Link>

                <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <p>{{ messages.login }}</p>
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
