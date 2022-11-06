<template>
    <inertia-head :title="title" />
    <v-app>
        <originalHead :pageTitle="pageTitle"/>
        <v-main>
            <slot />
        </v-main>
    </v-app>

</template>

<script>
    import { InertiaLink, InertiaHead } from '@inertiajs/inertia-vue3'
    import originalHead from '@/Components/head/originalHead.vue'

    export default {
        components: {
            InertiaLink,
            InertiaHead,
            originalHead
        },
        props:['title','pageTitle']
        ,
    }
</script>
