<script setup>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/inertia-vue3';
import { ref, onMounted, onUnmounted,nextTick } from 'vue';
import { useStore } from "vuex";
import originalFooter from '@/Components/foot/originalFooter.vue'

const store = useStore()

onMounted(() => {
    if ((window.navigator.language).substring(0,2) == "ja") {store.commit('setLang','ja')}
})
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <Link href="/">
                <img :src="'/sundlf_logo.png'" alt="ロゴ"/>
            </Link>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
    <originalFooter/>
</template>

<style lang="scss" scoped>
img {
    width:30vw;
    @media (max-width: 900px){width:50vw;}
    height: auto;
}
</style>
