<template>
    <BaseBookMarkLayout
        ref="BaseBookMarkLayout"
        :title="$store.state.lang == 'ja' ? '新規作成' : 'Create New'"
        :pageTitle="$store.state.lang == 'ja' ? '新規作成' : 'Create New'"
        @triggerSubmit="submit"
        @triggerDeleteBookMark="deleteBookMark"
    >
    </BaseBookMarkLayout>
</template>

<script>
import BaseBookMarkLayout from "@/Layouts/BaseBookMarkLayout.vue";

export default {
    data() {
        return {};
    },
    components: { BaseBookMarkLayout },
    methods: {
        async submit({ bookMarkTitle, bookMarkUrl, tagList }) {
            await axios
                .post("/api/bookmark/store", {
                    bookMarkTitle: bookMarkTitle,
                    bookMarkUrl: bookMarkUrl,
                    tagList: tagList,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                })
                .then((res) => {
                    this.$inertia.get("/BookMark/Search");
                })
                .catch((errors) => {
                    this.$store.commit("switchGlobalLoading");
                    this.$refs.BaseBookMarkLayout.setErrors(errors.response);
                });
        },
        deleteBookMark() {
            //遷移だけで良い
            this.$inertia.get("/BookMark/Search");
        },
    },
    mounted() {
        this.$store.commit("setGlobalLoading", false);
    },
};
</script>

<style lang="scss"></style>
