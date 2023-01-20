<template>
    <Head>
        <title>{{ title }} | sundlf</title>
    </Head>
    <v-app>
        <originalHead :pageTitle="pageTitle"/>
        <v-main>
            <slot />
        </v-main>
        <originalFooter/>
    </v-app>

</template>

<script>
    import { Head } from '@inertiajs/inertia-vue3'
    import originalHead from '@/Components/head/originalHead.vue'
    import originalFooter from '@/Components/foot/originalFooter.vue'

    export default {
        components: {
            Head,
            originalHead,
            originalFooter
        },
        props:['title','pageTitle'],
        mounted(){
            // 言語拾う
            if ((window.navigator.language).substring(0,2) == "ja") {this.$store.state.lang = "ja"}
        }
    }
</script>
