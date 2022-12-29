<template>
    <inertia-head :title="title" />
    <originalHead :pageTitle="pageTitle"/>
    <v-app>
        <v-main>
            <slot />
        </v-main>
    </v-app>
    <originalFooter/>

</template>

<script>
    import { InertiaLink, InertiaHead } from '@inertiajs/inertia-vue3'
    import originalHead from '@/Components/head/originalHead.vue'
    import originalFooter from '@/Components/foot/originalFooter.vue'

    export default {
        components: {
            InertiaLink,
            InertiaHead,
            originalHead,
            originalFooter
        },
        props:['title','pageTitle']
        ,
    }
</script>
