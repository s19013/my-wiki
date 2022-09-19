<template>
    <BaseArticleLayout
        ref="BaseArticleLayout"
        title="記事編集"
        pageTitle="記事編集"
        :originalArticleTitle   ="originalArticle.title"
        :originalArticleBody    ="originalArticle.body"
        :originalCheckedTagList ="originalCheckedTagList"
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
            this.$refs.BaseArticleLayout.switchArticleSending()
            axios.post('/api/article/update',{
                articleId   :this.originalArticle.id,
                articleTitle:articleTitle,
                articleBody :articleBody,
                tagList     :tagList
            })
            .then((res)=>{
                this.$inertia.get('/Article/Search')
            })
            .catch((err)=>{
                this.$refs.BaseArticleLayout.switchArticleSending()
            })
        },
        deleteArticle() {
            this.$refs.BaseArticleLayout.switchArticleSending()
            // 消す処理
            axios.delete('/api/article/' + this.originalArticle.id)
            .then((res) => {
                //遷移
                this.$inertia.get('/Article/Search')
            })
            .catch((error) => {
                console.log(error);
                this.$refs.BaseArticleLayout.switchArticleSending()
            })
        },
    },
    mounted() {
    },
}
</script>


