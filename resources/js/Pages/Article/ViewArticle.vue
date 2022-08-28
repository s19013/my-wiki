<template>
    <BaseLayout :title="'my-wiki ' + article.title" pageTitle="記事観覧">
        <div class="articleContainer">
                <!-- タイトルとボタン2つ -->
                <div class="head">
                    <DeleteAlertComponent
                        type="bookmark"
                        @deleteTrigger="deleteBookMark"
                    />
                    <Link :href="'/Article/Edit/' + article.id">
                        <v-btn class="longButton editButton" color="#BBDEFB">
                            <v-icon>mdi-pencil-plus</v-icon>
                            編集
                        </v-btn>
                    </Link>
                </div>
                <h1 class="title">{{article.title}}</h1>

                <!-- md表示 -->
                <div class="markdown" v-html="compiledMarkdown()"></div>

                <!-- タブ -->
                <TagList :tagList="articleTagList"/>
        </div>
        <loadingDialog :loadingFlag="articleDeleting"></loadingDialog>
    </BaseLayout>
</template>

<script>
import {marked} from 'marked';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import loadingDialog from '@/Components/loading/loadingDialog.vue';
import TagList from '@/Components/TagList.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'
import { Link } from '@inertiajs/inertia-vue3';
import axios from 'axios'


export default{
    data() {
      return {
        //loding
        articleDeleting:false,
      }
    },
    props:['article','articleTagList'],
    components:{
    DeleteAlertComponent,
    loadingDialog,
    TagList,
    BaseLayout,
    Link,
},
    methods: {
        compiledMarkdown() {return marked(this.article.body)},
        deleteArticle() {
            this.articleDeleting = true
            // 消す処理
            axios.post('/api/article/delete',{articleId:this.article.id})
            .then((res) => {
                //遷移
                this.$inertia.get('/Article/Search')
                this.articleDeleting = false
            })
            .catch((error) => {
                console.log(error);
                this.articleSending = false
            })
        },
    },
}
</script>

<style lang="scss" scoped>
.articleContainer {margin: 0 20px;}
.title{
    padding: 2px;
    border:black solid 1px;
}
.markdown{
    margin:20px;
    word-break   :break-word;
    overflow-wrap:normal;
}
.head{
    display: grid;
    grid-template-columns:10fr auto auto;
    margin: 10px;
    .deleteAlertDialog{
        grid-column: 2/3;
    }
    .editButton{
        margin-left:10px ;
        grid-column: 3/4;
    }

}

.head{margin-top: 10px;}
@media (max-width: 600px){
    .editButton{height: 3.5vh;}
}
</style>
