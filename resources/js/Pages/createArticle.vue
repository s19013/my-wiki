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
                    <v-col cols="1"> <v-btn color="error"> 削除 </v-btn> </v-col>
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
                    <v-col cols="2"><TagDialog :userId="$attrs.auth.user.id" ref="tagDialog"></TagDialog></v-col>
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

        <!-- <DeleteAlertComponent
            :open="deleteAlertFlag"
            @switch="deleteAlertFlagSwitch"
        ></DeleteAlertComponent> -->
    </div>
</template>

<script>
import {marked} from 'marked';
// import DeleteAlertComponent from '@/Components/DeleteAlertComponent.vue';
import TagDialog from '@/Components/TagDialog.vue';
import axios from 'axios'

export default {
    data() {
      return {
        activeTab:0,
        articleTitle:'',
        articleBody: '',

        // flag
        // deleteAlertFlag:false,

        //loding
        articleLoding :false,

        // errorFlag
        articleBodyErrorFlag:false,
      }
    },
    components:{
        // DeleteAlertComponent
        TagDialog
    },
    methods: {
        compiledMarkdown() {return marked(this.articleBody)},
        changeTab(num){this.activeTab = num},
        submitCheck(){
            console.log(this.$refs.tagDialog.serveCheckedTagListToParent());
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
                tagList:this.$refs.tagDialog.serveCheckedTagListToParent()
            })
            .then((res)=>{
                this.$inertia.get('/index')
            })
        },
        deleteAlertFlagSwitch() { this.deleteAlertFlag = !this.deleteAlertFlag },
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

.head{margin-top: 10px;}

</style>
