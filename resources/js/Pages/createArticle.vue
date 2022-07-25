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
                    <v-col cols="1"> <v-btn color="submit" @click="submit"> 保存 </v-btn> </v-col>
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
                    <v-col cols="2"><v-btn color="submit" @click="tagDialogFlag = !tagDialogFlag">tag </v-btn></v-col>
                </v-row>
                <!--  -->
                <div v-show="activeTab === 0">
                    <v-textarea
                        required
                        filled
                        auto-grow
                        label="本文"
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
            <v-card>
                <p @click.stop="tagDialogFlag = !tagDialogFlag">X 閉じる</p>
                <v-card-text></v-card-text>

                <ul v-for="tag of allTagList" :key="tag.id">
                    <input type="checkbox" :id="tag.id" v-model="checkedTagList" :value="tag.id">
                    <label :for="tag.id">{{tag.name}}</label>
                </ul>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="submit" elevation="2">新規作成</v-btn>
                </v-card-actions>
            </v-card>
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
        articleTitle:null,
        articleBody: '# hello',
        // deleteAlertFlag:false,
        tagDialogFlag:false,
        allTagList:[],
        checkedTagList:[]
      }
    },
    components:{
        // DeleteAlertComponent
    },
    methods: {
        changeTab(num){this.activeTab = num},
        compiledMarkdown() {return marked(this.articleBody)},
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
        deleteAlertFlagSwitch(){
            this.deleteAlertFlag = !this.deleteAlertFlag
            console.log(this.deleteAlertSwitch);
        },
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

.active{
    font-weight: bold;
    cursor: default;
}

.notActive{
    background: #919191;
    color: black;
    cursor: pointer;
}

button{width:100%}
.head{margin-top: 10px;}


</style>
