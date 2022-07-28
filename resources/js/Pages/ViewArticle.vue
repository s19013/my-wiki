<template>
    <BaseLayout :title= "title">
        <section class="articleContainer">
                <!-- タイトルとボタン2つ -->
                <v-row class="head">
                    <v-col cols="10">
                        <p>{{articleTitle}}</p>
                    </v-col>
                    <v-col cols="1"> <DeleteAlertComponent @deleteAricleTrigger="deleteArticle"></DeleteAlertComponent> </v-col>
                    <v-col cols="1">
                        <v-btn class="longButton" color="#BBDEFB" :disabled="articleSending"> 保存 </v-btn>
                    </v-col>
                </v-row>

                <!-- md表示 -->
                <div class="markdown" v-html="compiledMarkdown()"></div>

                <!-- タブ -->
                <ul v-for="tag of tagList" :key="tag.name">
                    <li>{{tag.name}}</li>
                </ul>
        </section>
        <loadingDialog :loadingFlag="articleSending"></loadingDialog>
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
        articleTitle:'',
        articleBody: '',
        tagList:[],

        //loding
        articleLoding :false,
        articleSending:false,

        // errorFlag
        articleBodyErrorFlag:false,
      }
    },
    components:{
        DeleteAlertComponent,
        loadingDialog,
        BaseLayout,
    },
    methods: {
        compiledMarkdown() {return marked(this.articleBody)},
        // 記事とタグを取ってくる
        async getArticle(){
            axios.post('/api/article/read',{ articleId:1 })
            .then((res)=>{
                console.log(res);
                this.articleTitle = res.data[0].title
                this.articleBody  = res.data[0].body
            })
            console.log("title:",this.articleTitle);
        },
        // タグを取ってくる
        async getTag(){
            axios.post('/api/tag/read',{ articleId:1 })
            .then((res)=>{
                console.log(res);
                for (const tag of res.data) {
                    this.tagList.push( tag )
                }
            })
            console.log(this.tagList);
        },
        deleteArticle() {
            // 消す処理

            //遷移
            this.$inertia.get('/index')
            console.log('called');
        },
    },
    mounted() {
        this.getTag()
        this.getArticle()
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
    padding: 0 10px;
    word-break:break-word;
    overflow-wrap:normal;
}

.tabLabel{
    li{
        display: inline-block;
        list-style:none;
        border:black solid 1px;
        padding:10px 20px;
    }
    .active{
        font-weight: bold;
        cursor: default;
    }

    .notActive{
        background: #919191;
        color: black;
        cursor: pointer;
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
