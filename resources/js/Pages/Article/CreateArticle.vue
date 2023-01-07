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
        async submit({
            articleTitle,
            articleBody,
            tagList,
        })
        {
            await axios.post('/api/article/store',{
                articleTitle:articleTitle,
                articleBody :articleBody,
                tagList     :tagList,
                timezone    :Intl.DateTimeFormat().resolvedOptions().timeZone
            })
            .then((res)=>{
                this.$inertia.get('/Article/Search')
            })
            .catch((errors) => {
                this.$refs.BaseArticleLayout.switchDisabledFlag()
                this.$refs.BaseArticleLayout.setErrors(errors.response)
            })
        },
        deleteArticle() {
            //遷移
            this.$inertia.get('/Article/Search')
        },
    },
}
</script>


