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
        submit({
            articleTitle,
            articleBody,
            tagList,
        }){
            this.$inertia.put('/Article/Update' ,{
                articleId   :this.originalArticle.id,
                articleTitle:articleTitle,
                articleBody :articleBody,
                tagList     :tagList,
                onError: (errors) => {
                    this.$refs.BaseArticleLayout.switchDisabledFlag()
                    console.log( errors )
                },
            })
        },
        deleteArticle() {
            this.disabledFlag = true
            // 消す処理
            this.$inertia.delete('/Article/' + this.originalArticle.id,{
                onError: (errors) => {console.log( errors )},
            })

        },
    },
    mounted() {
    },
}
</script>


