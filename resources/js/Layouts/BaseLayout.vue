<template>
    <inertia-head :title="title" />
    <originalHead :title="title"></originalHead>
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
            title: String
        },
    }
</script>
