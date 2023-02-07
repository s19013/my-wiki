<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3'
import { ref, onMounted, onUnmounted,nextTick, reactive } from 'vue';
import { useStore } from "vuex";
const store = useStore();

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    lang:String,
})

const japanese = reactive({
    title:"sundlf  -- タグを使ってメモ､ブックマークを整理",
    login:"ログイン",
    Register:"新規登録",
    message:"メモ､ブックマークにタグを付けて保存して整理､検索などで探しやすくするツールアプリです",
    article:"メモ",
    articleMessage:"メモをmd形式でかいて保存できます",
    bookmark:"ブックマーク",
    bookmarkMessage:"ブックマークを保存できます",
    tag:"タグを付けられます",
    tagMessage:"タグをつけて整理したり検索などで探すことができます",
})

const messages = reactive({
    title:"sundlf  -- Organize article and bookmarks using tags",
    login:"Log in",
    Register:"Register",
    message:"this application is makes it easier to find by adding tags to memos and bookmarks, saving them, organizing them, and searching them.",
    article:"article",
    articleMessage:"You can write and save articles in markdown format",
    bookmark:"bookmark",
    bookmarkMessage:"You can save bookmarks",
    tag:"tag",
    tagMessage:"You can add tags to organize and search.",
})

onMounted(() => {
    nextTick(() => {
        if ((window.navigator.language).substring(0,2) == "ja") {store.commit('setLang','ja')}
        if (props.lang == "ja"){Object.assign(messages,japanese)}
    })
})
</script>

<template>
    <v-container>
        <Head>
            <!-- <title>{{ messages.title }}</title>
            <meta name="description" :content="messages.message" /> -->
            <link rel="alternate" hreflang="ja" href="https://sundlf.com/">
            <link rel="alternate" hreflang="en" href="https://sundlf.com/en/">
        </Head>
        <div v-if="canLogin" class="links" >
            <Link v-if="$page.props.auth.user" :href="route('SearchBookMark')" class="text-sm text-gray-700 underline">
                Home
            </Link>
            <template v-else>
                <Link :href="route('login')" class="">
                    <p>{{ messages.login }}</p>
                </Link>

                <Link v-if="canRegister" :href="route('register')" class="">
                    <p>{{ messages.Register }}</p>
                </Link>
            </template>
        </div>

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center pt-10 sm:justify-start sm:pt-0">
                <img :src="'/sundlf_logo.png'" alt="ロゴ">
            </div>

            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                <p class="p-3">{{ messages.message }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
                        <div class="flex items-center">
                            <v-icon>mdi-note</v-icon>
                            <div class="ml-4 text-lg leading-7 font-semibold">
                                <h2>
                                    {{ messages.article }}
                                </h2>
                            </div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-gray-600 text-sm">
                                <p>{{ messages.articleMessage }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
                        <div class="flex items-center">
                            <v-icon>mdi-bookmark</v-icon>
                            <div class="ml-4 text-lg leading-7 font-semibold">
                                <h2>{{ messages.bookmark }}</h2>
                            </div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-gray-600 text-sm">
                                <p>{{ messages.bookmarkMessage }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center">
                            <v-icon>mdi-tag</v-icon>
                            <div class="ml-4 text-lg leading-7 font-semibold">
                                <h2>{{ messages.tag }}</h2>
                            </div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-gray-600 text-sm">
                                <p>{{ messages.tagMessage }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-l">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg leading-7 font-semibold text-gray-900 dark:text-white"></div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-gray-600  text-sm">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
                <div class="text-center text-sm text-gray-500 sm:text-left">
                    <div class="flex items-center">
                        <v-icon>mdi-github</v-icon>
                        <a href="https://github.com/s19013/my-wiki" target="_blank" rel="noopener noreferrer" class="ml-1 underline">
                            repository
                        </a>
                    </div>
                </div>

                <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </div>
            </div>
        </div>
    </v-container>
</template>

<style scoped>
    img{
        height: 5rem;
        width: auto;
    }
    .links{
        display: flex;
        justify-content: flex-end;
        gap:1rem;
    }
    .bg-gray-100 {
        background-color: #f7fafc;
        background-color: rgba(247, 250, 252, var(--tw-bg-opacity));
    }

    .border-gray-200 {
        border-color: #edf2f7;
        border-color: rgba(237, 242, 247, var(--tw-border-opacity));
    }

    .text-gray-400 {
        color: #cbd5e0;
        color: rgba(203, 213, 224, var(--tw-text-opacity));
    }

    .text-gray-500 {
        color: #a0aec0;
        color: rgba(160, 174, 192, var(--tw-text-opacity));
    }

    .text-gray-600 {
        color: #718096;
        color: rgba(113, 128, 150, var(--tw-text-opacity));
    }

    .text-gray-700 {
        color: #4a5568;
        color: rgba(74, 85, 104, var(--tw-text-opacity));
    }

    .text-gray-900 {
        color: #1a202c;
        color: rgba(26, 32, 44, var(--tw-text-opacity));
    }

    @media (prefers-color-scheme: dark) {
        .dark\:bg-gray-800 {
            background-color: #2d3748;
            background-color: rgba(45, 55, 72, var(--tw-bg-opacity));
        }

        .dark\:bg-gray-900 {
            background-color: #1a202c;
            background-color: rgba(26, 32, 44, var(--tw-bg-opacity));
        }

        .dark\:border-gray-700 {
            border-color: #4a5568;
            border-color: rgba(74, 85, 104, var(--tw-border-opacity));
        }

        .dark\:text-white {
            color: #fff;
            color: rgba(255, 255, 255, var(--tw-text-opacity));
        }

        .dark\:text-gray-400 {
            color: #cbd5e0;
            color: rgba(203, 213, 224, var(--tw-text-opacity));
        }
    }
</style>
