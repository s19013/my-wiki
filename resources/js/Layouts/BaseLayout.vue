<template>
    <Head>
        <title>{{ title }}</title>
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
        props:['title','pageTitle']
        ,
    }
</script>
