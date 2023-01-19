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

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    })
};

const japanese = reactive({
    password:"パスワード",
    message:"確認のためにパスワードを入力してください",
    submit:"送信",
})

const messages = reactive({
    password:"パスワード",
    message:"確認のためにパスワードを入力してください",
    submit:"送信",
})

onMounted(() => {
    nextTick(() => {
        if (store.state.lang == "ja"){Object.assign(messages,japanese)}
    })
})
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Confirm Password" />

        <div class="mb-4 text-sm text-gray-600">
            <p>{{ messages.message }}</p>
        </div>

        <BreezeValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <BreezeLabel for="password" :value="messages.password" />
                <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <p>{{ messages.submit }}</p>
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
