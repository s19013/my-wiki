<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <div class="articleContainer">
            <div class="head">
                <DeleteAlertComponent
                    ref="deleteAlert"
                    @deleteTrigger="deleteArticle"
                />
                <v-btn color="#BBDEFB" class="global_css_haveIconButton_Margin" @click="submitCheck()" :disabled="articleSending">
                    <v-icon>mdi-content-save</v-icon>
                    <p>保存</p>
                </v-btn>
            </div>
            <TagDialog
                ref="tagDialog"
                text = "つけたタグ"
                :originalCheckedTagList=originalCheckedTagList
            />
            <v-form v-on:submit.prevent>
                <!-- タイトル入力欄とボタン2つ -->
                <v-text-field
                    v-model="articleTitle"
                    label="タイトル"
                    outlined hide-details="false"
                    @keydown.enter.exact="focusToBody()"
                />

                <p class="global_css_error" v-if="articleBodyErrorFlag">本文を入力してください</p>
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
        ArticleBody,
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
        focusToBody(){this.$refs.articleBody.focusToBody()},
        changeTab(){this.$refs.articleBody.changeTab()},
    },
    mounted() {
        this.checkedTagList = this.originalCheckedTagList
        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            // 削除ダイアログ呼び出し
            if (event.key === "Delete") {
                this.$refs.deleteAlert.deleteDialogFlagSwitch()
                return
            }
            // 送信
            if (event.ctrlKey || event.key === "Meta") {
                if(event.code === "Enter"){this.submitCheck()}
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
    margin-bottom: 1.5rem ;
    .deleteAlertDialog{
        grid-column: 2/3;
    }
    .saveButton{
        margin-left:1rem ;
        grid-column: 3/4;
    }

}
.v-input{margin-bottom: 1.5rem;}
</style>
