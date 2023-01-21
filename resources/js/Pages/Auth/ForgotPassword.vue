<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import { ref, onMounted, onUnmounted,nextTick, reactive } from 'vue';
import { useStore } from "vuex";

const store = useStore()

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};

const japanese = reactive({
    email:"メールアドレス",
    message:"パスワードを再設定するにはメールアドレスが必要です",
    submit:"送信",
})

const messages = reactive({
    email:"Email",
    message:"Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.",
    submit:"Email Password Reset Link",
})

onMounted(() => {
    nextTick(() => {
        if (store.state.lang == "ja"){Object.assign(messages,japanese)}
    })
})
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600">
            {{ messages.message }}
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <BreezeValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <BreezeLabel for="email" :value="messages.email" />
                <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <BreezeButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ messages.submit }}
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
