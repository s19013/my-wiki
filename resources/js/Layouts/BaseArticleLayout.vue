<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <section class="articleContainer">
            <v-form v-on:submit.prevent ="submitCheck">
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
                        <v-btn class="longButton" color="#BBDEFB" @click="submitCheck" :disabled="articleSending">
                        <v-icon>mdi-content-save</v-icon>
                        保存
                        </v-btn>
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

                    <!--  -->
                    <v-col><p class="error articleError" v-if="articleBodyErrorFlag">本文を入力してください</p></v-col>

                    <!-- タグ -->
                    <v-col cols="2">
                        <TagDialog ref="tagDialog"
                            :originalCheckedTagList=originalCheckedTagList
                            @closedTagDialog="updateCheckedTagList"
                            />
                    </v-col>

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
            <TagList :tagList="checkedTagList"/>
        </section>
        <!-- 送信中に表示 -->
        <loadingDialog :loadingFlag="articleSending"></loadingDialog>

    </BaseLayout>
</template>

<script>
import {marked} from 'marked';
import TagDialog from '@/Components/dialog/TagDialog.vue';
import TagList from '@/Components/TagList.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import loadingDialog from '@/Components/loading/loadingDialog.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'

export default {
    data() {
      return {
        activeTab     :0,
        articleTitle  :this.originalArticleTitle,
        articleBody   :this.originalArticleBody,
        checkedTagList:[],

        //loding
        articleSending:false,

        // errorFlag
        articleBodyErrorFlag:false,
      }
    },
    components:{
        DeleteAlertComponent,
        TagDialog,
        TagList,
        loadingDialog,
        BaseLayout,
    },
    emits: ['triggerSubmit','triggerDeleteArticle'],
    props:{
        title:{
            type   :String,
            default:''
        },
        pageTitle:{
            type   :String,
            default:''
        },
        originalArticleTitle:{
            type   :String,
            default:''
        },
        originalArticleBody:{
            type   :String,
            default:''
        },
        originalCheckedTagList:{
            type  :Array,
            default:null
        },
    },
    methods: {
        compiledMarkdown() {return marked(this.articleBody)},
        changeTab(num){this.activeTab = num},
        updateCheckedTagList (list) { this.checkedTagList = list },
        switchArticleSending(){this.articleSending = !this.articleSending},
        // 本文送信前のチェック
        submitCheck:_.debounce(_.throttle(async function(){
            //本文が空だったらエラーだして送信しない
            if (this.articleBody =='') {
                this.articleBodyErrorFlag = true
                return
            }
            else {this.submit()}
        },100),150),
        // 本文送信
        submit(){
            this.$emit('triggerSubmit',{
                articleTitle:this.articleTitle,
                articleBody :this.articleBody,
                tagList     :this.$refs.tagDialog.serveCheckedTagListToParent()
            })
        },
        deleteArticle() { this.$emit('triggerDeleteArticle') },
    },
    mounted() {
        this.checkedTagList = this.originalCheckedTagList
    },
}
</script>

<style lang="scss">
.articleContainer {margin: 0 20px;}
textarea {
        width  : 100%;
        resize : none;
        padding: 20px;
        background-color: #f6f6f6;
}
.markdown{
    padding      : 0 10px;
    word-break   :break-word;
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
</style>