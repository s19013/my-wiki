<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <div class="articleContainer">
            <div class="head">
                <DeleteAlertComponent
                    ref ="deleteAlert"
                    @deleteTrigger="deleteBookMark"
                />
                <v-btn color="#BBDEFB" class="global_css_haveIconButton_Margin" @click="submit()" >
                    <v-icon>mdi-content-save</v-icon>
                    <p>{{messages.save}}</p>
                </v-btn>
            </div>

            <DateLabel v-if="edit" :createdAt="originalBookMark.created_at" :updatedAt="originalBookMark.updated_at"/>

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

            <v-form @submit.prevent>
                <!-- タイトル入力欄とボタン2つ -->
                <p
                    v-show="errorMessages.bookMarkTitle.length>0"
                    v-for ="message of errorMessages.bookMarkTitle" :key="message"
                    class ="global_css_error"
                >
                    <v-icon>mdi-alert-circle-outline</v-icon>
                    {{message}}
                </p>

                <v-text-field
                    v-model="bookMarkTitle"
                    :label="messages.title"
                    outlined hide-details="false"
                    @keydown.enter.exact="this.$refs.url.focus()"
                />

                <p
                    v-show="errorMessages.bookMarkUrl.length>0"
                    v-for ="message of errorMessages.bookMarkUrl" :key="message"
                    class ="global_css_error"
                >
                    <v-icon>mdi-alert-circle-outline</v-icon>
                    {{message}}
                </p>

                <v-text-field
                    ref="url"
                    :label="messages.url"
                    v-model = "bookMarkUrl"
                    @keydown.enter.exact="this.submit()"
                ></v-text-field>
            </v-form>
        </div>
        <loadingDialog/>
    </BaseLayout>
</template>

<script>
import loadingDialog from '@/Components/dialog/loadingDialog.vue';
import TagDialog from '@/Components/dialog/TagDialog.vue';
import TagList from '@/Components/TagList.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import DateLabel from '@/Components/DateLabel.vue';


export default {
    data() {
      return {
        japanese:{
            save:'保存',
            attachedTag:'付けたタグ',
            title:"タイトル",
            url:"url [必須]",
            otherError:"サーバー側でエラーが発生しました｡数秒待って再度送信してください",
        },
        messages:{
            save:'save',
            attachedTag:'Attached Tag',
            title:"title",
            url:"url [required]",
            otherError:"An error occurred on the server side, please wait a few seconds and try again",
        },
        bookMarkTitle :this.originalBookMark.title,
        bookMarkUrl   :this.originalBookMark.url,
        checkedTagList:this.originalCheckedTagList,

        // 初期の読み込みで空配列などが無いとエラーを吐かれる
        errorMessages:{
            others:[],//サーバー側のエラー
            bookMarkTitle:[],
            bookMarkUrl:[],
        },
      }
    },
    components:{
        DeleteAlertComponent,
        loadingDialog,
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
        // 本文送信
        submit(){
            this.$store.commit('switchGlobalLoading')
            this.$emit('triggerSubmit',{
                bookMarkTitle:this.bookMarkTitle,
                bookMarkUrl  :this.bookMarkUrl,
                tagList      :this.$refs.tagDialog.serveCheckedTagList()
            })
        },
        deleteBookMark() {
            this.$store.commit('switchGlobalLoading')
            this.$emit('triggerDeleteBookMark')
        },
        // エラーを受け取る
        setErrors(errors){
            console.log(errors);
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
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            // 削除ダイアログ呼び出し
            if(this.disabledFlag === false){
                if (event.key === "Delete") {
                    this.$refs.deleteAlert.deleteDialogFlagSwitch()
                    return
                }

                if ((event.ctrlKey || event.key === "Meta")
                && event.altKey && event.code === "KeyT" ) {
                    event.preventDefault();
                    this.$refs.tagDialog.openTagDialog()
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
    .v-alert{padding :0.5rem}
}
</style>
