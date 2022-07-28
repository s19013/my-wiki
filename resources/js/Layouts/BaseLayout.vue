<template>
    <inertia-head :title="title" />
    <originalHead :pageTitle="pageTitle"/>
    <!-- <article>
        <slot />
    </article> -->
    <main>
        <slot />
    </main>

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
        props:{
            title: String,
            pageTitle: String
        },
    }
</script>
