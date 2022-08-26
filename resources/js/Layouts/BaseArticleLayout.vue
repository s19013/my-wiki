<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <div class="articleContainer">
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
                <TagList :tagList="checkedTagList"/>
                <v-col cols="2">
                        <TagDialog
                            ref="tagDialog"
                            :originalCheckedTagList=originalCheckedTagList
                            @closedTagDialog="updateCheckedTagList"
                            />
                </v-col>
                <ArticleBody
                    ref="articleBody"
                    :originalArticleBody="originalArticleBody"
                />

            </v-form>
        </div>
        <!-- 送信中に表示 -->
        <loadingDialog :loadingFlag="articleSending"></loadingDialog>

    </BaseLayout>
</template>

<script>
import TagDialog from '@/Components/dialog/TagDialog.vue';
import TagList from '@/Components/TagList.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import loadingDialog from '@/Components/loading/loadingDialog.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'
import ArticleBody from '@/Components/article/ArticleBody.vue';

export default {
    data() {
      return {
        articleTitle  :this.originalArticleTitle,
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
        ArticleBody
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
        updateCheckedTagList (list) { this.checkedTagList = list },
        switchArticleSending(){this.articleSending = !this.articleSending},
        // 本文送信前のチェック
        submitCheck:_.debounce(_.throttle(async function(){
            //本文が空だったらエラーだして送信しない
            if (this.$refs.articleBody.serveBody() =='') {
                this.articleBodyErrorFlag = true
                return
            }
            else {this.submit()}
        },100),150),
        // 本文送信
        submit(){
            this.$emit('triggerSubmit',{
                articleTitle:this.articleTitle,
                articleBody :this.$refs.articleBody.serveBody(),
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
.head{margin-top: 10px;}
.articleError{padding-top: 5px;}
</style>
