<script setup>
const props = defineProps({
    icon: {
        type:String,
        default:null
    },
    title: {
        type:String,
        default:""
    },
    message: {
        type:String,
        default:""
    },
})
</script>

<template>
    <div class="MessageContainer p-6 border-t border-gray-200 md:border-t-0 md:border-l">
        <div class="flex items-center">
            <template v-if="icon != null">
                <v-icon>{{ icon }}</v-icon>
            </template>
            <div class="ml-4 text-lg leading-7 font-semibold">
                <h2>
                    {{ title }}
                </h2>
            </div>
        </div>
        <div class="ml-12">
            <div class="mt-2 text-gray-600 text-sm">
                <p>{{ message }}</p>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.border-gray-200 {
    border-color: #edf2f7;
    border-color: rgba(237, 242, 247, var(--tw-border-opacity));
}
.text-gray-600 {
    color: #718096;
    color: rgba(113, 128, 150, var(--tw-text-opacity));
}
</style>
