<template>
    <div>
        <section class="articleContainer">
            <!-- <v-form  v-on:submit.prevent ="submit"> -->
            <v-form>
                <v-row class="head">
                    <v-col cols="10">
                        <v-text-field
                            v-model="title"
                            label="タイトル"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="1"> <v-btn color="error" @click="change()"> 削除 </v-btn> </v-col>
                    <v-col cols="1"> <v-btn color="submit"> 保存 </v-btn> </v-col>
                </v-row>
                <DeleteAlertComponent></DeleteAlertComponent>
                <ul class="tabLabel">
                        <li @click="changeTab(0)" :class="{active: activeTab === 0,notActive: activeTab !== 0 }">
                            本文
                        </li>
                        <li @click="changeTab(1)" :class="{active: activeTab === 1,notActive: activeTab !== 1 }">
                            変換後
                        </li>
                </ul>

                <div v-show="activeTab === 0">
                    <v-textarea
                        filled
                        auto-grow
                        label="本文"
                        v-model = "article"
                    ></v-textarea>
                </div>
                <div v-show="activeTab === 1" class="markdown" v-html="compiledMarkdown()"></div>
            </v-form>
        </section>
        {{$attrs.auth.user.id}}
        <v-btn color="submit" @click="getTag()">tag </v-btn>
        <ul v-for="tag of allTag" :key="tag.id">
            <li>{{tag.name}}</li>
        </ul>
        <DeleteAlertComponent
            :open="deleteAlertFlag"
            @switch="deleteAlertFlagSwitch"
        ></DeleteAlertComponent>
    </div>
</template>

<script>
import {marked} from 'marked';
import DeleteAlertComponent from '@/Components/DeleteAlertComponent.vue';
import axios from 'axios'

export default {
    data() {
      return {
        activeTab:0,
        title:null,
        article: '# hello',
        deleteAlertFlag:false,
        allTag:[],
      }
    },
    components:{
        DeleteAlertComponent
    },
    methods: {
        changeTab(num){this.activeTab = num},
        compiledMarkdown() {return marked(this.article)},
        submit(){},
        deleteAlertFlagSwitch(){
            this.deleteAlertFlag = !this.deleteAlertFlag
            console.log(this.deleteAlertSwitch);
        },
        async getTag(){
            await axios.post('/api/serveTag',{id:this.$attrs.auth.user.id})
                .then((res)=>{
                    for (const tag of res.data) {
                        console.log('id:',tag.id,' name:',tag);
                        this.allTag.push({id:tag.id,name:tag.name})
                    }
            })
        },
    }
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

ul{
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
