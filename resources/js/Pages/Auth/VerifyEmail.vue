<script setup>
import { computed } from 'vue';
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, onMounted, onUnmounted,nextTick, reactive } from 'vue';
import { useStore } from "vuex";

const store = useStore()

const props = defineProps({
    status: String,
});

const form = useForm();

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');

const japanese = reactive({
    message:"メールでお送りしたリンクをクリックしてください｡届いていない場合再送信します",
    sendAgainMessage:"もう一度メールを送りました",
    sendAgain:"もう一度メールを受け取る",
    logout:"ログアウト"

})

const messages = reactive({
    message:"Thanks for signing up! could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.",
    sendAgainMessage:"A new verification link has been sent to the email address you provided during registration.",
    sendAgain:"Resend Verification Email",
    logout:"log out"
})

onMounted(() => {
    nextTick(() => {
        if (store.state.lang == "ja"){Object.assign(messages,japanese)}
    })
})
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-gray-600">
            <p>{{ messages.message }}</p>
        </div>

        <div class="mb-4 font-medium text-sm text-green-600" v-if="verificationLinkSent" >
            <p>{{ messages.sendAgainMessage }}</p>
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <BreezeButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <p>{{ messages.sendAgain }}</p>
                </BreezeButton>

                <Link :href="route('logout')" method="post" as="button" class="underline text-sm text-gray-600 hover:text-gray-900"><p>{{ messages.logout }}</p></Link>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
