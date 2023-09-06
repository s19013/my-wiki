<template>
    <BaseArticleLayout
        ref="BaseArticleLayout"
        :title="$store.state.lang == 'ja' ? '記事編集' : 'Edit Article'"
        :pageTitle="$store.state.lang == 'ja' ? '記事編集' : 'Edit Article'"
        :originalArticle="originalArticle"
        :originalCheckedTagList="originalCheckedTagList"
        :edit="true"
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
    props: ["originalArticle", "originalCheckedTagList"],
    components: { BaseArticleLayout },
    methods: {
        async submit({ articleId, articleTitle, articleBody, tagList }) {
            await axios
                .put("/api/article/update", {
                    articleId: articleId,
                    articleTitle: articleTitle,
                    articleBody: articleBody,
                    tagList: tagList,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                })
                .then((res) => {
                    // スナックバーを表示させる
                    this.$store.commit("switchGlobalLoading");
                    this.$refs.BaseArticleLayout.switchSuccessed();
                })
                .catch((errors) => {
                    this.$store.commit("switchGlobalLoading");
                    this.$refs.BaseArticleLayout.setErrors(errors.response);
                    console.log(errors);
                });
        },
        deleteArticle() {
            // 消す処理
            axios
                .delete("/api/article/" + this.originalArticle.id)
                .then((res) => {
                    this.$inertia.get("/Article/Search");
                })
                .catch((errors) => {
                    this.$store.commit("switchGlobalLoading");
                    console.log(errors);
                });
        },
    },
    mounted() {
        this.$store.commit("setGlobalLoading", false);
    },
};
</script>
