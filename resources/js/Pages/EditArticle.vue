<template>
    <BaseLayout :title= "title">
        <section class="articleContainer">
            <v-form v-on:submit.prevent ="submit">
                <!-- タイトル入力欄とボタン2つ -->
                <v-row class="head">
                    <v-col cols="10">
                        <v-text-field
                            v-model="articleTitle"
                            label="タイトル"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="1"> <DeleteAlertComponent @deleteAricleTrigger="deleteArticle"></DeleteAlertComponent> </v-col>
                    <v-col cols="1">
                        <v-btn class="longButton" color="#BBDEFB" @click="submitCheck" :disabled="articleSending"> 保存 </v-btn>
                    </v-col>
                </v-row>
                <!-- タブ -->
                <v-row>
                    <v-col>
                        <ul class="tabLabel">
                            <li @click="changeTab(0)" :class="{active: activeTab === 0,notActive: activeTab !== 0 }">
                                本文
                            </li>
                            <li @click="changeTab(1)" :class="{active: activeTab === 1,notActive: activeTab !== 1 }">
                                変換後
                            </li>
                        </ul>
                    </v-col>

                    <v-col><p class="error articleError" v-if="articleBodyErrorFlag">本文を入力してください</p></v-col>
                    <v-col cols="2"><TagDialog :userId="$attrs.auth.user.id" ref="tagDialog"></TagDialog></v-col>
                </v-row>
                <!-- md入力欄  -->
                <div v-show="activeTab === 0">
                    <v-textarea
                        filled
                        auto-grow
                        label="本文 [必須]"
                        v-model = "articleBody"
                    ></v-textarea>
                </div>
                <div v-show="activeTab === 1" class="markdown" v-html="compiledMarkdown()"></div>
            </v-form>
        </section>
        <loadingDialog :loadingFlag="articleSending"></loadingDialog>
    </BaseLayout>
</template>

<script>
import {marked} from 'marked';
import TagDialog from '@/Components/dialog/TagDialog.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import loadingDialog from '@/Components/loading/loadingDialog.vue';
import axios from 'axios'

export default {
    data() {
      return {
        activeTab:0,
        articleTitle:'',
        articleBody: '',

        //loding
        articleLoding :false,
        articleSending:false,

        // errorFlag
        articleBodyErrorFlag:false,
      }
    },
    components:{
        DeleteAlertComponent,
        TagDialog,
        loadingDialog,
    },
    methods: {
        compiledMarkdown() {return marked(this.articleBody)},
        changeTab(num){this.activeTab = num},
        // 本文送信
        submitCheck:_.debounce(_.throttle(async function(){
            if (this.articleBody =='') {
                this.articleBodyErrorFlag = true
                return
            }
            else {this.submit()}
        },100),150),
        submit(){
            this.articleSending = true
            axios.post('/api/article/store',{
                userId:this.$attrs.auth.user.id,
                articleTitle:this.articleTitle,
                articleBody:this.articleBody,
                category:2,
                tagList:this.$refs.tagDialog.serveCheckedTagListToParent()
            })
            .then((res)=>{
                this.articleSending = false
                this.$inertia.get('/index')
            })
        },
        deleteArticle() {
            // 消す処理

            //遷移
            this.$inertia.get('/index')
            console.log('called');
        },
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
