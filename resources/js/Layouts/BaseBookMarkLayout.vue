<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <div class="articleContainer">
            <div class="head">
                <DeleteAlertComponent
                    type="bookmark"
                    @deleteTrigger="deleteBookMark"
                />
                <v-btn color="#BBDEFB" class="global_css_haveIconButton_Margin" @click="submitCheck()" :disabled="bookMarkSending">
                    <v-icon>mdi-content-save</v-icon>
                    <p>保存</p>
                </v-btn>
            </div>
            <TagDialog
                ref="tagDialog"
                text = "つけたタグ"
                :originalCheckedTagList=originalCheckedTagList
            />
            <p class="global_css_error" v-if="bookMarkUrlErrorFlag">urlを入力してください</p>
            <p class="global_css_error" v-if="alreadyExistErrorFlag">そのURLはすでに登録されています</p>
            <v-form @submit.prevent>
                <!-- タイトル入力欄とボタン2つ -->
                <v-text-field
                    v-model="bookMarkTitle"
                    label="タイトル"
                    outlined hide-details="false"
                    @keydown.enter.exact="this.$refs.url.focus()"
                    @keydown.ctrl.enter.exact="submitCheck"
                    @keydown.meta.enter.exact="submitCheck"
                />
                <v-text-field
                    ref="url"
                    label="url [必須]"
                    v-model = "bookMarkUrl"
                    @keydown.ctrl.enter.exact="submitCheck"
                    @keydown.meta.enter.exact="submitCheck"
                ></v-text-field>
            </v-form>
        </div>
        <!-- 送信中に表示 -->
        <loadingDialog :loadingFlag="bookMarkSending"></loadingDialog>

    </BaseLayout>
</template>

<script>
import TagDialog from '@/Components/dialog/TagDialog.vue';
import TagList from '@/Components/TagList.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import loadingDialog from '@/Components/loading/loadingDialog.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'
import SaveButton from '@/Components/button/SaveButton.vue';

export default {
    data() {
      return {
        bookMarkTitle :this.originalBookMarkTitle,
        bookMarkUrl   :this.originalBookMarkUrl,
        checkedTagList:[],

        //loding
        bookMarkSending:false,

        // errorFlag
        bookMarkUrlErrorFlag :false,
        alreadyExistErrorFlag:false,
      }
    },
    components:{
        DeleteAlertComponent,
        TagDialog,
        TagList,
        loadingDialog,
        BaseLayout,
        SaveButton,
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
        originalBookMarkTitle:{
            type   :String,
            default:''
        },
        originalBookMarkUrl:{
            type   :String,
            default:''
        },
        originalCheckedTagList:{
            type   :Array,
            default:null
        },
    },
    methods: {
        switchAlreadyExistErrorFlag(){this.alreadyExistErrorFlag = !this.alreadyExistErrorFlag},
        switchBookMarkSending(){this.bookMarkSending = !this.bookMarkSending},
        // 本文送信
        submitCheck:_.debounce(_.throttle(async function(){
            if (this.bookMarkUrl =='') {
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
    mounted() {this.checkedTagList = this.originalCheckedTagList},
}
</script>

<style lang="scss" scoped>
.articleContainer {
    margin: 0 20px;
    margin-top: 2rem;
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
