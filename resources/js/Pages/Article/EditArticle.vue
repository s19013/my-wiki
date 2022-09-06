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

<style lang="scss">
.articleContainer {margin: 0 20px;}
textarea {
        width : 100%;
        resize: none;
        background-color: #f6f6f6;
        padding: 20px;
}
.markdown{
    padding: 0 10px;
    word-break:break-word;
    overflow-wrap:normal;
}

.tabLabel{
    li{
        display   : inline-block;
        list-style:none;
        border :black solid 1px;
        padding:10px 20px;
    }
    .active{
        font-weight: bold;
        cursor     : default;
    }

    .notActive{
        background: #919191;
        color : black;
        cursor: pointer;
    }
}

.head{margin-top: 10px;}
.articleError{padding-top: 5px;}
.v-input__details{
    margin : 0;
    padding: 0;
    height : 0;
    width  : 0;
}

</style>

