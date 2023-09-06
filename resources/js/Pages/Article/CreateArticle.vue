<template>
    <BaseArticleLayout
        ref="BaseArticleLayout"
        :title="$store.state.lang == 'ja' ? '新規作成' : 'Create New'"
        :pageTitle="$store.state.lang == 'ja' ? '新規作成' : 'Create New'"
        @triggerSubmit="submit"
        @triggerDeleteArticle="deleteArticle"
    >
    </BaseArticleLayout>
</template>

<script>
import BaseArticleLayout from "@/Layouts/BaseArticleLayout.vue";

export default {
    data() {
        return {};
    },
    components: {
        BaseArticleLayout,
    },
    methods: {
        // 本文送信
        async submit({ articleId, articleTitle, articleBody, tagList }) {
            // 新規登録
            if (articleId == null) {
                await axios
                    .post("/api/article/store", {
                        articleTitle: articleTitle,
                        articleBody: articleBody,
                        tagList: tagList,
                        timezone:
                            Intl.DateTimeFormat().resolvedOptions().timeZone,
                    })
                    .then((res) => {
                        console.log(res);
                        this.$store.commit("switchGlobalLoading");
                        this.$refs.BaseArticleLayout.switchSuccessed();
                        this.$refs.BaseArticleLayout.setArticleId(
                            res.data.articleId
                        );
                    })
                    .catch((errors) => {
                        this.$store.commit("switchGlobalLoading");
                        this.$refs.BaseArticleLayout.setErrors(errors.response);
                    });
            } else {
                await axios
                    .put("/api/article/update", {
                        articleId: articleId,
                        articleTitle: articleTitle,
                        articleBody: articleBody,
                        tagList: tagList,
                        timezone:
                            Intl.DateTimeFormat().resolvedOptions().timeZone,
                    })
                    .then((res) => {
                        this.$store.commit("switchGlobalLoading");
                        this.$refs.BaseArticleLayout.switchSuccessed();
                    })
                    .catch((errors) => {
                        this.$store.commit("switchGlobalLoading");
                        this.$refs.BaseArticleLayout.setErrors(errors.response);
                    });
            }
        },
        deleteArticle({ articleId }) {
            //遷移
            console.log(articleId);
            if (articleId == null) {
                this.$inertia.get("/Article/Search");
                return;
            }
            axios
                .delete("/api/article/" + articleId)
                .then((res) => {
                    this.$inertia.get("/Article/Search");
                })
                .catch((errors) => {
                    this.$store.commit("switchGlobalLoading");
                });
        },
        mounted() {
            this.$store.commit("setGlobalLoading", false);
        },
    },
};
</script>
