<template>
    <BaseArticleLayout
        ref  ="BaseArticleLayout"
        title="新規作成"
        pageTitle      ="新規作成"
        @triggerSubmit = "submit"
        @triggerDeleteArticle = "deleteArticle"
    >
    </BaseArticleLayout>
</template>

<script>
import BaseArticleLayout from '@/Layouts/BaseArticleLayout.vue'

export default {
    data() {
      return {}
    },
    components:{
        BaseArticleLayout
    },
    methods: {
        // 本文送信
        submit({
            articleTitle,
            articleBody,
            tagList,
        })
        {
            this.$refs.BaseArticleLayout.switchDisabledFlag()
            axios.post('api/article/store',{
                articleTitle:articleTitle,
                articleBody :articleBody,
                tagList     :tagList,
            })
            .then((res)=>{this.$inertia.get('/Article/Search')})
            .catch((error) => {
                this.$refs.BaseArticleLayout.switchDisabledFlag()
                console.log(error);
            })
        },
        deleteArticle() {
            //遷移
            this.$refs.BaseArticleLayout.switchDisabledFlag()
            this.$inertia.get('/Article/Search')
        },
    },
}
</script>


