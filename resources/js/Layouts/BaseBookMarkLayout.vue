<template>
    <BaseLayout :title="title" :pageTitle="pageTitle">
        <section class="articleContainer">
            <v-form v-on:submit.prevent ="submitCheck">
                <!-- タイトル入力欄とボタン2つ -->
                <v-row class="head">
                    <v-col cols="10">
                        <v-text-field
                            v-model="bookMarkTitle"
                            label="タイトル"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="1"> <DeleteAlertComponent @deleteAricleTrigger="deleteBookMark"></DeleteAlertComponent> </v-col>
                    <v-col cols="1">
                        <v-btn class="longButton" color="#BBDEFB" @click="submitCheck" :disabled="bookMarkSending">
                        <v-icon>mdi-content-save</v-icon>
                        保存
                        </v-btn>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col><p class="error articleError" v-if="bookMarkUrlErrorFlag">urlを入力してください</p></v-col>

                    <!-- タグ -->
                    <v-col cols="2">
                        <TagDialog ref="tagDialog"
                            :originalCheckedTagList=originalCheckedTagList
                            @closedTagDialog       ="updateCheckedTagList"
                        ></TagDialog>
                        </v-col>
                </v-row>

                <v-text-field
                        label="url [必須]"
                        v-model = "bookMarkUrl"
                ></v-text-field>
            </v-form>
            <TagList :tagList="checkedTagList"/>
        </section>
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

export default {
    data() {
      return {
        bookMarkTitle:this.originalBookMarkTitle,
        bookMarkUrl:this.originalBookMarkUrl,
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
    },
    emits: ['triggerSubmit','triggerDeleteArticle'],
    props:{
        title:{
            type:String,
            default:''
        },
        pageTitle:{
            type:String,
            default:''
        },
        originalBookMarkTitle:{
            type:String,
            default:''
        },
        originalBookMarkUrl:{
            type:String,
            default:''
        },
        originalCheckedTagList:{
            type:Array,
            default:null
        },
    },
    methods: {
        switchBookMarkSending(){this.bookMarkSending = !this.bookMarkSending},
        updateCheckedTagList (list) { this.checkedTagList = list },
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

.tabLabel{
    li{
        display: inline-block;
        list-style:none;
        border:black solid 1px;
        padding:10px 20px;
    }
    .active{
        font-weight: bold;
        cursor: default;
    }

    .notActive{
        background: #919191;
        color: black;
        cursor: pointer;
    }
}

.head{margin-top: 10px;}
.bookMarkError{padding-top: 5px;}
.v-input__details{
    margin: 0;
    padding: 0;
    height: 0;
    width: 0;
}

</style>
