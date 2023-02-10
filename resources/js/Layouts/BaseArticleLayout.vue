<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <div class="articleContainer">
            <div class="head">
                <DeleteAlertComponent
                    ref="deleteAlert"
                    @deleteTrigger="deleteArticle"
                />
                <v-btn
                    color="#BBDEFB" class="global_css_haveIconButton_Margin"
                    @click="submit()">
                    <v-icon>mdi-content-save</v-icon>
                    <p>{{messages.save}}</p>
                </v-btn>
            </div>

            <DateLabel v-if="edit" :createdAt="originalArticle.created_at" :updatedAt="originalArticle.updated_at" />

            <TagDialog
                ref="tagDialog"
                :text = "messages.attachedTag"
                :originalCheckedTagList=originalCheckedTagList
            />

            <p
                v-show="errorMessages.others.length>0"
                v-for ="message of errorMessages.others" :key="message"
                class ="global_css_error"
            >
                <v-icon>mdi-alert-circle-outline</v-icon>
                {{message}}
            </p>

            <v-form v-on:submit.prevent>
                <!-- タイトル入力欄とボタン2つ -->
                <p
                    v-show="errorMessages.articleTitle.length>0"
                    v-for ="message of errorMessages.articleTitle" :key="message"
                    class ="global_css_error"
                >
                    <v-icon>mdi-alert-circle-outline</v-icon>
                    {{message}}
                </p>
                <v-text-field
                    v-model="articleTitle"
                    :label="messages.title"
                    outlined hide-details="false"
                    @keydown.enter.exact="focusToBody()"
                />

                <ArticleBody
                    ref="articleBody"
                    :originalArticleBody="articleBody"
                />

            </v-form>
        </div>
        <loadingDialog :loadingFlag="disabledFlag"/>
        <v-snackbar
          v-model="successed"
          :timeout="1000"
        >
          {{ messages.success }}
        </v-snackbar>
    </BaseLayout>
</template>

<script>
import TagDialog from '@/Components/dialog/TagDialog.vue';
import loadingDialog from '@/Components/dialog/loadingDialog.vue';
import TagList from '@/Components/TagList.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'
import ArticleBody from '@/Components/article/ArticleBody.vue';
import DateLabel from '@/Components/DateLabel.vue';

export default {
    data() {
      return {
        japanese:{
            save:'保存',
            attachedTag:'付けたタグ',
            title:"タイトル",
            otherError:"サーバー側でエラーが発生しました｡数秒待って再度送信してください",
            success:"保存しました",
        },
        messages:{
            save:'save',
            attachedTag:'Attached Tag',
            title:"title",
            otherError:"An error occurred on the server side, please wait a few seconds and try again",
            success:"saved",
        },

        articleId     :this.originalArticle.id,
        articleTitle  :this.originalArticle.title,
        articleBody   :this.originalArticle.body,
        checkedTagList:this.originalCheckedTagList,

        disabledFlag:false,
        successed:false,

        // 初期の読み込みで空配列などが無いとエラーを吐かれる
        errorMessages:{
            others:[],//サーバー側のエラー
            articleTitle:[],
        },

      }
    },
    components:{
        DeleteAlertComponent,
        loadingDialog,
        TagDialog,
        TagList,
        BaseLayout,
        ArticleBody,
        DateLabel,
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
        originalArticle:{
            type   :Object,
            default:{
                id:null,
                title:'',
                body :''
            }
        },
        originalCheckedTagList:{
            type  :Array,
            default:[]
        },
        edit:{
            type  :Boolean,
            default:false
        },
    },
    methods: {
        switchDisabledFlag(){this.disabledFlag = !this.disabledFlag},
        switchSuccessed(){this.successed = !this.successed},
        // 本文送信
        submit(){
            this.disabledFlag = true
            this.successed    = false
            this.resetErrors()
            this.$emit('triggerSubmit',{
                articleId   :this.articleId,
                articleTitle:this.articleTitle,
                articleBody :this.$refs.articleBody.serveBody(),
                tagList     :this.$refs.tagDialog.serveCheckedTagList()
            })
        },
        deleteArticle() {
            this.disabledFlag = true
            this.$emit('triggerDeleteArticle',{articleId:this.articleId})
        },
        focusToBody(){this.$refs.articleBody.focusToBody()},
        changeTab(){this.$refs.articleBody.changeTab()},
        setArticleId(articleId){this.articleId = articleId},
        resetErrors(){
            this.errorMessages = {
                others:[],
                articleTitle:[],
            }
        },
        // エラーを受け取る
        setErrors(errors){
            // サーバー側のエラー(500番台)だったら､もう一度送信するようにユーザーに促す
            if (String(errors.status)[0] == 5) {
                this.errorMessages = {
                    "others" : [this.messages.otherError]
                }
            }
            else { this.errorMessages = errors.data.messages }
        }
    },
    mounted() {
        this.$nextTick(function () {
            // ビュー全体がレンダリングされた後にのみ実行されるコード
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            // 削除ダイアログ呼び出し

            // 読み込み中には呼ばせない
            if(this.disabledFlag === false){
                if (event.key === "Delete") {
                    this.$refs.deleteAlert.deleteDialogFlagSwitch()
                    return
                }
                // 送信
                if (event.ctrlKey || event.key === "Meta") {
                    if(event.code === "Enter"){this.submit()}
                    return
                }
            }
        })
    },
}
</script>

<style lang="scss" scoped>
.articleContainer {
    margin: 0 1rem;
    margin-top: 1rem;
    @media (max-width: 900px){margin-top: 2rem;}
}

.head{
    display: grid;
    grid-template-columns:9fr auto auto;
    gap:2rem;
    .deleteAlertDialog{
        grid-column: 2/3;
    }
    .saveButton{
        margin-left:1rem ;
        grid-column: 3/4;
    }

}
.v-input{margin-bottom: 1.5rem;}
.DateLabel{
    margin: 0.5rem 0;
    justify-content: flex-start;
}
.TagDialog{margin:1rem 0;}
</style>
