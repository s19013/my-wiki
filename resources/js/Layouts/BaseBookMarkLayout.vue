<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <div class="articleContainer">
            <div class="head">
                <DeleteAlertComponent
                    type="bookmark"
                    ref ="deleteAlert"
                    :disabledFlag="disabledFlag"
                    @deleteTrigger="deleteBookMark"
                />
                <v-btn color="#BBDEFB" class="global_css_haveIconButton_Margin" @click="submitCheck()" :disabled="disabledFlag" :loading="disabledFlag">
                    <v-icon>mdi-content-save</v-icon>
                    <p>保存</p>
                </v-btn>
            </div>

            <DateLabel v-if="edit" :createdAt="originalBookMark.created_at" :updatedAt="originalBookMark.updated_at"/>

            <TagDialog
                ref="tagDialog"
                text = "つけたタグ"
                :originalCheckedTagList=originalCheckedTagList
                :disabledFlag="disabledFlag"
            />

            <p class="global_css_error" v-if="bookMarkUrlErrorFlag">urlを入力してください</p>
            <p class="global_css_error" v-if="alreadyExistErrorFlag">そのURLはすでに登録されています</p>

            <v-form @submit.prevent>
                <!-- タイトル入力欄とボタン2つ -->
                <v-text-field
                    v-model="bookMarkTitle"
                    label="タイトル"
                    outlined hide-details="false"
                    :disabled="disabledFlag"
                    :loading="disabledFlag"
                    @keydown.enter.exact="this.$refs.url.focus()"
                />
                <v-text-field
                    ref="url"
                    label="url [必須]"
                    v-model   = "bookMarkUrl"
                    :disabled ="disabledFlag"
                    :loading  ="disabledFlag"
                    @keydown.enter.exact="this.submitCheck()"
                ></v-text-field>
            </v-form>
        </div>

    </BaseLayout>
</template>

<script>
import TagDialog from '@/Components/dialog/TagDialog.vue';
import TagList from '@/Components/TagList.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import DateLabel from '@/Components/DateLabel.vue';

export default {
    data() {
      return {
        bookMarkTitle :this.originalBookMark.title,
        bookMarkUrl   :this.originalBookMark.url,
        checkedTagList:this.originalCheckedTagList,

        //loding
        disabledFlag:false,

        // errorFlag
        bookMarkUrlErrorFlag :false,
        alreadyExistErrorFlag:false,
      }
    },
    components:{
        DeleteAlertComponent,
        TagDialog,
        TagList,
        BaseLayout,
        DateLabel,
    },
    emits: ['triggerSubmit','triggerDeleteBookMark'],
    props:{
        title:{
            type   :String,
            default:''
        },
        pageTitle:{
            type   :String,
            default:''
        },
        originalBookMark:{
            type   :Object,
            default:{
                title:'',
                url  :''
            }
        },
        originalCheckedTagList:{
            type   :Array,
            default:[]
        },
        edit:{
            type  :Boolean,
            default:false
        },
    },
    methods: {
        switchAlreadyExistErrorFlag(){this.alreadyExistErrorFlag = !this.alreadyExistErrorFlag},
        switchDisabledFlag(){this.disabledFlag = !this.disabledFlag},
        // 本文送信
        submitCheck:_.debounce(_.throttle(async function(){
            if (this.bookMarkUrl == null) {
                this.bookMarkUrlErrorFlag = true
                return
            }
            else {this.submit()}
        },100),150),
        submit(){
            this.$emit('triggerSubmit',{
                bookMarkTitle:this.bookMarkTitle,
                bookMarkUrl  :this.bookMarkUrl,
                tagList      :this.$refs.tagDialog.serveCheckedTagListToParent()
            })
        },
        deleteBookMark() {this.$emit('triggerDeleteBookMark')},
    },
    mounted() {
        // this.checkedTagList = this.originalCheckedTagList
        // if (this.originalBookMark !== null) {
        //     this.bookMarkTitle =this.originalBookMark.title
        //     this.bookMarkUrl   =this.originalBookMark.url
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
