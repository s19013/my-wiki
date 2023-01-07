<template>
    <BaseArticleLayout
        ref="BaseArticleLayout"
        title="記事編集"
        pageTitle="記事編集"
        :originalArticle        ="originalArticle"
        :originalCheckedTagList ="originalCheckedTagList"
        :edit="true"
        @triggerSubmit          = "submit"
        @triggerDeleteArticle   = "deleteArticle"
        >
    </BaseArticleLayout>
</template>

<script>
import BaseArticleLayout from '@/Layouts/BaseArticleLayout.vue'

export default {
    data() {
      return {
      }
    },
    props:['originalArticle','originalCheckedTagList'],
    components:{BaseArticleLayout},
    methods: {
        async submit({
            articleTitle,
            articleBody,
            tagList,
        }){
            await axios.put('/api/article/update',{
                articleId   :this.originalArticle.id,
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
                console.log(errors);
            })
        },
        deleteArticle() {
            // 消す処理
            axios.delete('/api/article/' + this.originalArticle.id)
            .then((res)=>{
                this.$inertia.get('/Article/Search')
            })
            .catch((errors) => {
                this.$refs.BaseArticleLayout.switchDisabledFlag()
                console.log(errors);
            })
        },
    },
    mounted() {
    },
}
</script>


