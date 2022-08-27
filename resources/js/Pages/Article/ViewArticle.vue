<template>
    <BaseLayout :title="'my-wiki ' + article.title" pageTitle="記事観覧">
        <div class="articleContainer">
                <!-- タイトルとボタン2つ -->
                <v-row class="head">
                    <v-col cols="10">
                        <h1>{{article.title}}</h1>
                    </v-col>
                    <v-col cols="1"> <DeleteAlertComponent @deleteTrigger="deleteArticle"></DeleteAlertComponent> </v-col>
                    <v-col cols="1">
                        <Link :href="'/Article/Edit/' + article.id">
                            <v-btn class="longButton" color="#BBDEFB">
                                <v-icon>mdi-pencil-plus</v-icon>
                                編集
                            </v-btn>
                        </Link>
                    </v-col>
                </v-row>

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

<style lang="scss">
.articleContainer {margin: 0 20px;}
.markdown{
    margin-top:20px;
    margin-bottom:30px;
    padding: 10px;
    word-break:break-word;
    overflow-wrap:normal;
}

.head{margin-top: 10px;}

</style>
