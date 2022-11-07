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
                    @click="submitCheck()" :disabled="disabledFlag" :loading="disabledFlag">
                    <v-icon>mdi-content-save</v-icon>
                    <p>保存</p>
                </v-btn>
            </div>

            <DateLabel v-if="edit" :createdAt="originalArticle.created_at" :updatedAt="originalArticle.updated_at" size="0.8rem"/>

            <TagDialog
                ref="tagDialog"
                text = "つけたタグ"
                :originalCheckedTagList=originalCheckedTagList
                :disabledFlag="disabledFlag"
            />
            <v-form v-on:submit.prevent>
                <!-- タイトル入力欄とボタン2つ -->
                <v-text-field
                    v-model="articleTitle"
                    label="タイトル"
                    outlined hide-details="false"
                    :disabled="disabledFlag"
                    :loading="disabledFlag"
                    @keydown.enter.exact="focusToBody()"
                />

                <p class="global_css_error" v-if="articleBodyErrorFlag">本文を入力してください</p>
                <ArticleBody
                    ref="articleBody"
                    :originalArticleBody="articleBody"
                    :disabledFlag="disabledFlag"
                />

            </v-form>
        </div>

    </BaseLayout>
</template>

<script>
import TagDialog from '@/Components/dialog/TagDialog.vue';
import TagList from '@/Components/TagList.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'
import ArticleBody from '@/Components/article/ArticleBody.vue';
import DateLabel from '@/Components/DateLabel.vue';

export default {
    data() {
      return {
        articleTitle  :'',
        articleBody   :'',
        checkedTagList:[],

        //loding
        disabledFlag:false,

        // errorFlag
        articleBodyErrorFlag:false,
      }
    },
    components:{
        DeleteAlertComponent,
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
            default:null
        },
        originalCheckedTagList:{
            type  :Array,
            default:null
        },
        edit:{
            type  :Boolean,
            default:false
        },
    },
    methods: {
        switchDisabledFlag(){this.disabledFlag = !this.disabledFlag},
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
        //props受け渡し
        this.checkedTagList = this.originalCheckedTagList
        if (this.originalArticle !== null ) {
            this.articleTitle = this.originalArticle.title
            this.articleBody  = this.originalArticle.body
        }

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
    justify-content: flex-end;
}
</style>
