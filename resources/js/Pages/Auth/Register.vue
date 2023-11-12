<script setup>
import BreezeButton from "@/Components/Button.vue";
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import BreezeInput from "@/Components/Input.vue";
import BreezeLabel from "@/Components/Label.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import { ref, onMounted, onUnmounted, nextTick, reactive } from "vue";
import { useStore } from "vuex";

const store = useStore();

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
    privacy: false,
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

const japanese = reactive({
    name: "ユーザーネーム",
    email: "メールアドレス",
    password: "パスワード",
    confirm: "確認のため パスワードをもう一度入力してください",
    login: "ログインはこちら",
    register: "登録",
    message:
        "ユーザーごとにデータを保存するためにアカウントを作る必要があります",
});

const messages = reactive({
    name: "Name",
    email: "Email",
    password: "Password",
    confirm: "Confirm Password",
    login: "Log in is here",
    register: "Register",
    message: "An account must be created for each user to store data",
});

onMounted(() => {
    nextTick(() => {
        if (store.state.lang == "ja") {
            Object.assign(messages, japanese);
        }
    });
});
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Register" />

        <p class="mt-2 mb-10">{{ messages.message }}</p>

        <BreezeValidationErrors class="mb-4" />
        <form @submit.prevent="submit">
            <div>
                <BreezeLabel for="name" :value="messages.name" />
                <BreezeInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    data-testid="nameInput"
                />
            </div>

            <div class="mt-4">
                <BreezeLabel for="email" :value="messages.email" />
                <BreezeInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    data-testid="emailInput"
                />
                <!-- autocomplete="username"っておかしくない? -->
            </div>

            <div class="mt-4">
                <BreezeLabel for="password" :value="messages.password" />
                <BreezeInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    data-testid="passwordInput"
                />
            </div>

            <div class="mt-4">
                <BreezeLabel
                    for="password_confirmation"
                    :value="messages.confirm"
                />
                <BreezeInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    data-testid="passwordConfirmationInput"
                />
            </div>

            <div class="mt-4">
                <input
                    type="checkbox"
                    id="terms"
                    data-testid="termsCheckbox"
                    required
                />
                <label for="terms" v-if="store.state.lang == 'ja'">
                    <a
                        target="_blank"
                        rel="noopener noreferrer"
                        href="https://docs.google.com/document/d/e/2PACX-1vQRfqPmWcI2irs1HpRBOjA9lyo2CiIFWRBpWY2lmHnMM8gWZUEmng57BEs1t-VC5Bd_kSCHhmG9gmAA/pub"
                        >利用規約</a
                    >に同意します
                </label>
                <label for="terms" v-else>
                    Agree with the
                    <a
                        href="https://docs.google.com/document/d/e/2PACX-1vSdKoL_dJ3sVwsBBdNh-lVetKD-WupAVq56CYTpTkpkcMHvTNfBruWBnqleHuYDPa7yv1CkdfCL79im/pub"
                        >terms of service</a
                    >
                </label>
            </div>
            <div class="mt-4">
                <input
                    type="checkbox"
                    id="privacy"
                    data-testid="privacyCheckbox"
                    required
                />
                <label for="privacy" v-if="store.state.lang == 'ja'">
                    <a
                        target="_blank"
                        rel="noopener noreferrer"
                        href="https://docs.google.com/document/d/e/2PACX-1vQCW5pRoXeXHiZJ-vz8MImLVm-XTViLIdy1TxTBtsbAAzYb4MpPEaEFucHaWnpzDkI905s5AeW6rui3/pub"
                        >プライバシーポリシー</a
                    >に同意します
                </label>
                <label for="privacy" v-else>
                    Agree with the
                    <a
                        href="https://docs.google.com/document/d/e/2PACX-1vQ0WcXsD3kbYli0SqzaQ0yks_r5uuIgIClMLmT_4g6WLbx9zjcXeoAvxHcLmxbQC_OkrFcieFUaghU_/pub"
                        >privacy policy</a
                    >
                </label>
            </div>
            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                >
                    <p>{{ messages.login }}</p>
                </Link>

                <BreezeButton
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <p>{{ messages.register }}</p>
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
