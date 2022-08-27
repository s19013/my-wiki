<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <div class="articleContainer">
            <v-form v-on:submit.prevent ="submitCheck">
                <!-- タイトル入力欄とボタン2つ -->
                <div class="head">
                    <DeleteAlertComponent
                        type="bookmark"
                        @deleteTrigger="deleteBookMark"
                    />
                    <SaveButton
                        :disabled="bookMarkSending"
                        @click="submitCheck()"
                    />
                </div>
                <v-text-field
                    v-model="bookMarkTitle"
                    label="タイトル"
                    outlined hide-details="false"
                />

                <TagDialog
                    ref="tagDialog"
                    :originalCheckedTagList=originalCheckedTagList
                />

                <p class="error articleError" v-if="bookMarkUrlErrorFlag">urlを入力してください</p>
                <v-text-field
                        label="url [必須]"
                        v-model = "bookMarkUrl"
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
        bookMarkUrlErrorFlag:false,
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
.bookMarkError{padding-top: 5px;}


</style>
