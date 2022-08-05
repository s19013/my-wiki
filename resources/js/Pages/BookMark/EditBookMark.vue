<template>
    <BaseLayout title="ブックマーク編集" pageTitle="ブックマーク編集">
        <section class="articleContainer">
            <v-form v-on:submit.prevent ="submit">
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
                    <v-col cols="2"><TagDialog ref="tagDialog" :originalCheckedTag=originalCheckedTag></TagDialog></v-col>
                </v-row>

                <v-text-field
                        label="url [必須]"
                        v-model = "bookMarkUrl"
                ></v-text-field>
            </v-form>
        </section>
        <!-- 送信中に表示 -->
        <loadingDialog :loadingFlag="bookMarkSending"></loadingDialog>

    </BaseLayout>
</template>

<script>
import {marked} from 'marked';
import TagDialog from '@/Components/dialog/TagDialog.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import loadingDialog from '@/Components/loading/loadingDialog.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'
import axios from 'axios'

export default {
    data() {
      return {
        activeTab:0,
        bookMarkTitle:'',
        bookMarkUrl: '',

        //loding
        bookMarkLoding :false,
        bookMarkSending:false,

        // errorFlag
        bookMarkUrlErrorFlag:false,
      }
    },
    props:['originalBookMark','originalCheckedTag'],
    components:{
        DeleteAlertComponent,
        TagDialog,
        loadingDialog,
        BaseLayout,
    },
    methods: {
        compiledMarkdown() {return marked(this.bookMarkUrl)},
        changeTab(num){this.activeTab = num},
        // 本文送信
        submitCheck:_.debounce(_.throttle(async function(){
            if (this.bookMarkUrl =='') {
                this.bookMarkUrlErrorFlag = true
                return
            }
            else {this.submit()}
        },100),150),
        submit(){
            this.bookMarkSending = true
            axios.post('/api/bookmark/update',{
                bookMarkId:this.originalBookMark.id,
                bookMarkTitle:this.bookMarkTitle,
                bookMarkUrl:this.bookMarkUrl,
                tagList:this.$refs.tagDialog.serveCheckedTagListToParent()
            })
            .then((res)=>{
                this.bookMarkSending = false
                this.$inertia.get('/BookMark/Search')
            })
        },
        deleteBookMark() {
            this.bookMarkDeleting = true
            // 消す処理
            axios.post('/api/bookmark/delete',{bookMarkId:this.originalBookMark.id})
            .then((res) => {
                //遷移
                this.$inertia.get('/bookmark/search')
                this.bookMarkDeleting = false
            })
            .catch((error) => {
                console.log(error);
                this.articleSending = false
            })
        },
    },
    mounted() {
        console.log(this.originalCheckedTag);
        this.bookMarkTitle = this.originalBookMark.title
        this.bookMarkUrl   = this.originalBookMark.url
    },
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
