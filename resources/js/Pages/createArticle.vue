<template>
    <div>
        <section class="articleContainer">
            <!-- <v-form  v-on:submit.prevent ="submit"> -->
            <v-form>
                <v-row class="head">
                    <v-col cols="10">
                        <v-text-field
                            v-model="articleTitle"
                            label="タイトル"
                        ></v-text-field>
                    </v-col>
                    <!-- <v-col cols="1"> <v-btn color="error"> 削除 </v-btn> </v-col> -->
                    <v-col cols="1"> <v-btn color="submit" @click="submitCheck"> 保存 </v-btn> </v-col>
                </v-row>
                <!--  -->
                <v-row>
                    <v-col>
                        <ul class="tabLabel">
                            <li @click="changeTab(0)" :class="{active: activeTab === 0,notActive: activeTab !== 0 }">
                                本文
                            </li>
                            <li @click="changeTab(1)" :class="{active: activeTab === 1,notActive: activeTab !== 1 }">
                                変換後
                            </li>
                        </ul>
                    </v-col>

                    <v-col><p class="error" v-if="articleBodyErrorFlag">本文を入力してください</p></v-col>
                    <v-col cols="2"><v-btn color="submit" @click="tagDialogFlag = !tagDialogFlag">tag </v-btn></v-col>
                </v-row>
                <!--  -->
                <div v-show="activeTab === 0">
                    <v-textarea
                        filled
                        auto-grow
                        label="本文 [必須]"
                        v-model = "articleBody"
                    ></v-textarea>
                </div>
                <div v-show="activeTab === 1" class="markdown" v-html="compiledMarkdown()"></div>
            </v-form>
        </section>
        {{$attrs.auth.user.id}}

        {{checkedTagList}}

        <v-dialog
            v-model="tagDialogFlag"
            scrollable
            persistent>
            <section class="Dialog tagDialog">
                <p @click.stop="tagDialogFlag = !tagDialogFlag">X 閉じる</p>
                <ul v-for="tag of allTagList" :key="tag.id">
                    <input type="checkbox" :id="tag.id" v-model="checkedTagList" :value="tag.id">
                    <label :for="tag.id">{{tag.name}}</label>
                </ul>
                <v-btn color="submit" elevation="2" v-if="!createNewTagFlag" @click="createNewTagFlagSwitch()">新規作成</v-btn>
                <!--  -->
                <div class="areaCreateNewTag">
                    <p class="error" v-if="newTagErrorFlag">文字を入力してください</p>
                    <v-text-field
                        v-if="createNewTagFlag"
                        v-model="newTag"
                        label="新しいタグ"
                    ></v-text-field>
                    <v-btn color="submit" elevation="2" v-if="createNewTagFlag" @click="createNewTagCheck()">作成</v-btn>
                </div>
            </section>
            <!-- <v-card>
                <p @click.stop="tagDialogFlag = !tagDialogFlag">X 閉じる</p>
                <v-card-text></v-card-text>

                <ul v-for="tag of allTagList" :key="tag.id">
                    <input type="checkbox" :id="tag.id" v-model="checkedTagList" :value="tag.id">
                    <label :for="tag.id">{{tag.name}}</label>
                </ul>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-text-field
                            v-if="createNewTagFlag"
                            v-model="newTag"
                            label="新しいタグ"
                        ></v-text-field>
                    <v-btn color="submit" elevation="2" @click="createNewTagFlagSwitch">新規作成</v-btn>
                </v-card-actions>
            </v-card> -->
        </v-dialog>


        <!-- <DeleteAlertComponent
            :open="deleteAlertFlag"
            @switch="deleteAlertFlagSwitch"
        ></DeleteAlertComponent> -->
    </div>
</template>

<script>
import {marked} from 'marked';
// import DeleteAlertComponent from '@/Components/DeleteAlertComponent.vue';
import axios from 'axios'

export default {
    data() {
      return {
        activeTab:0,
        articleTitle:'',
        articleBody: '',
        newTag:'',

        // flag
        tagDialogFlag:false,
        createNewTagFlag:false,
        // deleteAlertFlag:false,

        // errorFlag
        articleBodyErrorFlag:false,
        newTagErrorFlag:false,

        // tagList
        allTagList:[],
        checkedTagList:[]
      }
    },
    components:{
        // DeleteAlertComponent
    },
    methods: {
        compiledMarkdown() {return marked(this.articleBody)},
        changeTab(num){this.activeTab = num},
        submitCheck(){
            if (this.articleBody =='') {
                this.articleBodyErrorFlag = true
                return
            }
            else {this.submit()}
        },
        submit(){
            axios.post('/api/article/store',{
                userId:this.$attrs.auth.user.id,
                articleTitle:this.articleTitle,
                articleBody:this.articleBody,
                category:2,
                tagList:this.checkedTagList
            })
            .then((res)=>{
                console.log(res);
            })
        },
        createNewTagCheck(){
            if (this.newTag == '') {
                this.newTagErrorFlag = true
                return
            }
            else {this.createNewTag()}
        },
        createNewTag(){

        },
        deleteAlertFlagSwitch() { this.deleteAlertFlag = !this.deleteAlertFlag },
        createNewTagFlagSwitch(){ this.createNewTagFlag = !this.createNewTagFlag },
        async getAllTag(){
            await axios.post('/api/tag/serveUserAllTag',{userId:this.$attrs.auth.user.id})
            .then((res)=>{
                    for (const tag of res.data) {
                        console.log('id:',tag.id,' name:',tag);
                        this.allTagList.push({id:tag.id,name:tag.name})
                    }
            })
            .catch((error)=>{})
        },
    },
    mounted() {
        this.getAllTag()
    },
}
</script>

<style lang="scss">
.articleContainer {margin: 0 20px;}
textarea {
        width: 100%;
        resize: none;
        background-color: #f6f6f6;
        padding: 20px;
}
.markdown{
    word-break:break-word;
    overflow-wrap:normal;
}

.tabLabel{
    li{
        display: inline-block;
        list-style:none;
        border:black solid 1px;
        padding:10px 20px;
    }
}

.error{
    color: rgb(190, 0, 0);
    font-weight: bolder;
    margin-top: 10px;
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

.Dialog{
    background: #e1e1e1;
    width: 30vw;
}

.tagDialog{
    .areaCreateNewTag{
        margin-top: 10px;
        .v-input__details{ margin: 0px; }
    }
}


button{width:100%}
.head{margin-top: 10px;}


</style>
