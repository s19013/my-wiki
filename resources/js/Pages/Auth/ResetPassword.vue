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

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const japanese = reactive({
    email:"メールアドレス",
    password:"パスワード",
    confirm:"確認のため パスワードをもう一度入力してください",
    submit:"送信"
})

const messages = reactive({
    email:"Email",
    password:"Password",
    confirm:"Confirm Password",
    login:"Reset Password"
})

onMounted(() => {
    nextTick(() => {
        if (store.state.lang == "ja"){Object.assign(messages,japanese)}
    })
})
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Reset Password" />

        <BreezeValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <BreezeLabel for="email" :value="messages.email" />
                <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <BreezeLabel for="password" :value="messages.password" />
                <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <BreezeLabel for="password_confirmation" :value="messages.confirm" />
                <BreezeInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <BreezeButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <p>{{ messages.submit }}</p>
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
