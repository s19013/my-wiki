<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <div class="articleContainer">
            <div class="head">
                <DeleteAlertComponent
                    ref="deleteAlert"
                    :disabledFlag="disabledFlag"
                    @deleteTrigger="deleteArticle"
                />
                <v-btn
                    color="#BBDEFB" class="global_css_haveIconButton_Margin"
                    @click="submit()" :disabled="disabledFlag" :loading="disabledFlag">
                    <v-icon>mdi-content-save</v-icon>
                    <p>保存</p>
                </v-btn>
            </div>

            <DateLabel v-if="edit" :createdAt="originalArticle.created_at" :updatedAt="originalArticle.updated_at" />

            <TagDialog
                ref="tagDialog"
                text = "つけたタグ"
                :originalCheckedTagList=originalCheckedTagList
                :disabledFlag="disabledFlag"
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
                    label="タイトル"
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
        articleTitle  :this.originalArticle.title,
        articleBody   :this.originalArticle.body,
        checkedTagList:this.originalCheckedTagList,

        //loding
        disabledFlag:false,

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
        // 本文送信
        submit(){
            this.$emit('triggerSubmit',{
                articleTitle:this.articleTitle,
                articleBody :this.$refs.articleBody.serveBody(),
                tagList     :this.$refs.tagDialog.serveCheckedTagList()
            })
        },
        deleteArticle() { this.$emit('triggerDeleteArticle') },
        focusToBody(){this.$refs.articleBody.focusToBody()},
        changeTab(){this.$refs.articleBody.changeTab()},
        // エラーを受け取る
        setErrors(errors){
            // サーバー側のエラー(500番台)だったら､もう一度送信するようにユーザーに促す
            if (String(errors.status)[0] == 5) {
                this.errorMessages = {
                    "others" : ["サーバー側でエラーが発生しました｡数秒待って再度送信してください"]
                }
            }
            else { this.errorMessages = errors.data.messages }
        }
    },
    mounted() {
        //props受け渡し
        // this.checkedTagList = this.originalCheckedTagList
        // if (this.originalArticle !== null ) {
        //     this.articleTitle = this.originalArticle.title
        //     this.articleBody  = this.originalArticle.body
        // }

        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            // 削除ダイアログ呼び出し
            if (event.key === "Delete") {
                this.$refs.deleteAlert.deleteDialogFlagSwitch()
                return
            }
            // 送信
            if (event.ctrlKey || event.key === "Meta") {
                if(event.code === "Enter"){this.submit()}
                return
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
