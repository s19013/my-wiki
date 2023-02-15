<template>
    <Head>
        <title>{{ title }} | sundlf</title>
    </Head>
    <v-app>
        <originalHead :pageTitle="pageTitle"/>
        <v-main>
            <slot />
        </v-main>
        <BarAd></BarAd>
        <originalFooter/>
    </v-app>

</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import originalHead from '@/Components/head/originalHead.vue'
import originalFooter from '@/Components/foot/originalFooter.vue'
import BarAd from '@/Components/Ads/BarAd.vue';

export default {
    components: {
        Head,
        originalHead,
        originalFooter,
        BarAd,
    },
    props:['title','pageTitle'],
    mounted(){
        // 言語拾う
        if ((window.navigator.language).substring(0,2) == "ja") {this.$store.state.lang = "ja"}
    }
}
</script>

<style lang="scss" scoped>
.v-application{
    background-color: #fcfcfc;
}
#ad {
    margin: 1rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
