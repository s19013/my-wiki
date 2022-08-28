<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <div class="articleContainer">
            <v-form v-on:submit.prevent ="submitCheck">
                <!-- タイトル入力欄とボタン2つ -->
                <div class="head">
                    <DeleteAlertComponent @deleteTrigger="deleteArticle"/>
                    <SaveButton
                        :disabled="articleSending"
                        @click="submitCheck()"
                    />
                </div>
                <v-text-field
                    v-model="articleTitle"
                    label="タイトル"
                    outlined hide-details="false"
                />

                <TagDialog
                    ref="tagDialog"
                    :originalCheckedTagList=originalCheckedTagList
                />

                <p class="error" v-if="articleBodyErrorFlag">本文を入力してください</p>
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
import SaveButton from '@/Components/button/SaveButton.vue';

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
        SaveButton
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
    },
    mounted() {
        this.checkedTagList = this.originalCheckedTagList
    },
}
</script>

<style lang="scss">
.articleContainer {margin: 0 20px;}
.head{
    display: grid;
    grid-template-columns:10fr auto auto;
    margin: 10px;
    .deleteAlertDialog{
        grid-column: 2/3;
    }
    .saveButton{
        margin-left:10px ;
        grid-column: 3/4;
    }

}
</style>
