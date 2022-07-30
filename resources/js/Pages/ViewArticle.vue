<template>
    <BaseLayout :title="'my-wiki ' + article.title" pageTitle="記事観覧">
        <section class="articleContainer">
                <!-- タイトルとボタン2つ -->
                <v-row class="head">
                    <v-col cols="10">
                        <h1>{{article.title}}</h1>
                    </v-col>
                    <v-col cols="1"> <DeleteAlertComponent @deleteAricleTrigger="deleteArticle"></DeleteAlertComponent> </v-col>
                    <v-col cols="1">
                        <v-btn class="longButton" color="#BBDEFB" @click="TransitionToEdit">
                            <v-icon>mdi-pencil-plus</v-icon>
                            編集
                        </v-btn>
                    </v-col>
                </v-row>

                <!-- md表示 -->
                <div class="markdown" v-html="compiledMarkdown()"></div>

                <!-- タブ -->
                <div class="tab">
                    <p><v-icon>mdi-tag</v-icon> つけたタグ</p>
                    <ul >
                        <li v-for="tag of articleTag" :key="tag">{{tag.name}}</li>
                    </ul>
                </div>
        </section>
        <loadingDialog :loadingFlag="articleDeleting"></loadingDialog>
    </BaseLayout>
</template>

<script>
import {marked} from 'marked';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import loadingDialog from '@/Components/loading/loadingDialog.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'
import axios from 'axios'
export default{
    data() {
      return {
        //loding
        articleDeleting:false,
      }
    },
    props:['article','articleTag'],
    components:{
        DeleteAlertComponent,
        loadingDialog,
        BaseLayout,
    },
    methods: {
        compiledMarkdown() {return marked(this.article.body)},
        deleteArticle() {
            this.articleDeleting = true
            // 消す処理
            axios.post('/api/article/delete',{articleId:this.article.id})
            .then((res) => {
                //遷移
                this.$inertia.get('/index')
                this.articleDeleting = false
            })
            .catch((error) => {
                this.articleDeleting = false
            })
        },
        TransitionToEdit(){
            this.$inertia.post('/EditArticle',{
                articleTitle:this.article.title,
                articleBody :this.article.body,
                category:this.article.category,
                tagList :this.articleTag
            })
        }
    },
}
</script>

<style lang="scss">
.articleContainer {margin: 0 20px;}
textarea {
    width: 100%;
    resize: none;
    background-color: #f6f6f6;
    padding: 20px;
}
.markdown{
    margin-top:10px;
    margin-bottom:30px;
    padding: 10px;
    border:black solid 1px;
    word-break:break-word;
    overflow-wrap:normal;
}

.tab{
    ul{
        display: flex;
        flex-wrap: wrap;
    }
    li{
        list-style:none;
        border:black solid 1px;
        padding: 0 10px ;
        margin:5px;
        cursor: default;

    }
}

.head{margin-top: 10px;}
.articleError{padding-top: 5px;}
.v-input__details{
    margin: 0;
    padding: 0;
    height: 0;
    width: 0;
}

</style>
