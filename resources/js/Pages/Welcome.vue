<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3'
import { ref, onMounted, onUnmounted,nextTick, reactive } from 'vue';
import { useStore } from "vuex";
import originalFooter from '@/Components/foot/originalFooter.vue'
import RakutenAd from '@/Components/Ads/RakutenAd.vue';
import WellcomMessageContainer from '@/Components/contents/WellcomMessageContainer.vue';
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
    firstHere:"初めての方はこちら",
    message:"メモ､ブックマークにタグを付けて保存して整理､検索などで探しやすくするツールアプリです",
    article:"メモ",
    articleMessage:"メモをmd形式でかいて保存できます",
    bookmark:"ブックマーク",
    bookmarkMessage:"ブックマークを保存できます",
    tag:"タグをつけて管理",
    tagMessage:"タグをつけて整理したり検索などで探すことができます",
    free:"無料で使える",
    freeMessage:"",
    search:"",
    searchMessage:"",
    returnHome:"ホーム画面に戻る"
})

const messages = reactive({
    title:"sundlf  -- Organize article and bookmarks using tags",
    login:"Log in",
    Register:"Register",
    firstHere:"Click here for the first time",
    message:"this application is makes it easier to find by adding tags to memos and bookmarks, saving them, organizing them, and searching them.",
    article:"article",
    articleMessage:"You can write and save articles in markdown format",
    bookmark:"bookmark",
    bookmarkMessage:"You can save bookmarks",
    tag:"Tags",
    tagMessage:"You can add tags to organize and search.",
    free:"Free to use",
    freeMessage:"",
    search:"",
    searchMessage:"",
    returnHome:"Return Home"
})

onMounted(() => {
    nextTick(() => {
        if ((window.navigator.language).substring(0,2) == "ja") {store.commit('setLang','ja')}
        if (props.lang == "ja"){Object.assign(messages,japanese)}
    })
})
</script>

<template>
    <Head>
        <title>{{ messages.title }}</title>
        <meta inertia name="description" :content="messages.message" />
        <meta inertia property="og:title" :content="messages.title"/>
        <meta inertia property="og:description" :content="messages.message"/>
        <link rel="alternate" hreflang="ja" href="https://sundlf.com"/>
        <link rel="alternate" hreflang="en" href="https://sundlf.com/en/"/>
        <link rel="alternate" hreflang="x-default" href="https://sundlf.com/en/"/>
    </Head>
    <v-container>
        <div v-if="canLogin" class="links" >
            <Link v-if="$page.props.auth.user" :href="route('SearchBookMark')" class="text-sm text-gray-700 underline">
                {{ messages.returnHome }}
            </Link>
            <template v-else>
                <Link v-if="props.lang == 'ja'" href="/en">English</Link>
                <Link v-else href="/">日本語</Link>
                <Link :href="route('login')" class="">
                    <p>{{ messages.login }}</p>
                </Link>

                <Link v-if="canRegister" :href="route('register')" class="">
                    <p>{{ messages.Register }}</p>
                </Link>
            </template>
        </div>

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <Link v-if="$page.props.auth.user" :href="route('SearchBookMark')">
                <div class="flex justify-center pt-10 sm:justify-start sm:pt-0">
                    <img :src="'/sundlf_logo.png'" alt="ロゴ">
                </div>
            </Link>
            <div v-else class="flex justify-center pt-10 sm:justify-start sm:pt-0">
                <img :src="'/sundlf_logo.png'" alt="ロゴ">
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow sm:rounded-lg">
                <p class="p-3">{{ messages.message }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <WellcomMessageContainer
                        icon="mdi-note"
                        :title="messages.article"
                        :message="messages.articleMessage"
                    />

                    <WellcomMessageContainer
                        icon="mdi-bookmark"
                        :title="messages.bookmark"
                        :message="messages.bookmarkMessage"
                    />

                    <WellcomMessageContainer
                        icon="mdi-tag"
                        :title="messages.tag"
                        :message="messages.tagMessage"
                    />

                    <WellcomMessageContainer
                        icon="mdi-currency-usd-off"
                        :title="messages.free"
                        :message="messages.freeMessage"
                    />

                    <!-- <WellcomMessageContainer
                        icon="mdi-currency-usd-off"
                        :title="messages.free"
                        :message="messages.freeMessage"
                    />

                    <WellcomMessageContainer
                        icon="mdi-currency-usd-off"
                        :title="messages.free"
                        :message="messages.freeMessage"
                    /> -->
                </div>
            </div>

            <div class="flex justify-center mt-2 sm:items-center sm:justify-between">
                <div class="text-center text-sm text-gray-500 sm:text-left">
                    <div class="flex items-center">
                        <v-icon>mdi-github</v-icon>
                        <a href="https://github.com/s19013/my-wiki" target="_blank" rel="noopener noreferrer" class="ml-1 underline">
                            repository
                        </a>
                    </div>
                </div>

                <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                    Laravel v9 (PHP v8)
                </div>
            </div>
            <Link v-if="$page.props.auth.user" :href="route('SearchBookMark')" class="">
                <v-btn class="fw-bolder" color="#BBDEFB">
                    {{messages.returnHome}}
                </v-btn>
            </Link>
            <template v-else>
                <Link v-if="canRegister" :href="route('register')" class="flex justify-center">
                    <v-btn class="fw-bolder" color="#BBDEFB">
                        <v-icon class="mr-2">mdi-account-plus</v-icon>
                        {{messages.firstHere}}
                    </v-btn>
                </Link>
            </template>
        </div>
    </v-container>
    <RakutenAd/>
    <originalFooter/>
</template>

<style scoped>
    #ad{
        margin: 1rem 0;
        padding: 1rem 0;
    }
    img{
        height: 5rem;
        width: auto;
    }
    .links{
        display: flex;
        justify-content: flex-start;
        gap:1rem;
    }
    .v-btn{margin: 1rem auto;}
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
