<template>
    <div>
        <!-- ダイアログを呼び出すためのボタン -->
        <v-btn class="longButton" color="submit" @click.stop="tagDialogFlagSwithch">tag </v-btn>


<!-- v-modelがv-ifとかの代わりになっている -->
        <v-dialog
            v-model="tagDialogFlag"
            scrollable
            persistent>

            <section class="Dialog tagDialog">
                <v-btn color="#E57373"   x-small elevation="2" @click.stop="tagDialogFlagSwithch()">X 閉じる</v-btn>
                <v-row class="areaTagSerch">
                    <v-col cols="10">
                        <v-text-field
                            v-model="tagToSearch"
                            label="タグ検索"
                            clearable
                        ></v-text-field>
                    </v-col>
                    <v-col cols="1">
                        <v-btn color="submit"
                        elevation="2"
                        :disabled = "tagSerchLoading"
                        @click="searchTagCheck()">検索</v-btn>
                    </v-col>
                </v-row>
                <!--  -->
                <loading v-if="tagSerchLoading"></loading>
                <v-list
                    class="overflow-y-auto mx-auto"
                    width="100%"
                    max-height="50vh">

                    <v-list-item v-for="tag of tagSearchResultList" :key="tag.id">
                        <input type="checkbox" :id="tag.id" v-model="checkedTagList" :value="tag.id">
                        <label :for="tag.id">{{tag.name}}</label>
                    </v-list-item>

                </v-list>
                <v-btn
                class="longButton my-4"
                color="submit"
                v-if="!createNewTagFlag"
                @click.stop="createNewTagFlagSwitch">新規作成</v-btn>
                <!--  -->
                <div class="areaCreateNewTag" v-if="createNewTagFlag">
                    <p class="error" v-if="newTagErrorFlag">文字を入力してください</p>
                    <p class="error" v-if="tagAlreadyExistsErrorFlag">そのタグはすでに登録されいます</p>

                    <v-text-field v-model="newTag" label="新しいタグ"></v-text-field>

                    <v-btn
                    class="longButton"
                    color="#BBDEFB"
                    elevation="2"
                    :disabled="newTagSending"
                    @click.stop="createNewTagCheck()">作成</v-btn>
                </div>
            </section>
        </v-dialog>
    </div>
</template>

<script>
import loading from './loading/loading.vue'
export default{
    data() {
      return {
        tagToSearch:'',
        newTag:'',

        // flag
        createNewTagFlag:false,
        tagDialogFlag:false,

        //loding
        tagSerchLoading:false,
        newTagSending:false,

        // errorFlag
        newTagErrorFlag:false,
        tagAlreadyExistsErrorFlag:false,

        // tagList
        allTagList:[],//キャッシュみたいなもの
        checkedTagList:[],
        tagSearchResultList:[]
      }
    },
    props:{ userId:{type:Number},},
    components:{loading},
    methods: {
        createNewTagCheck:_.debounce(_.throttle(async function(){
            if (this.newTag == '') {
                this.newTagErrorFlag = true
                return
            }
            else {this.createNewTag()}
        },100),150),
        createNewTag(){
            this.newTagSending = true
            axios.post('/api/tag/store',{
                userId:this.userId,
                tag   :this.newTag
            })
            .then((res)=>{
                this.getAddedTag()
                // 入力欄を消す
                this.createNewTagFlag=false
                this.newTag=''

                //エラーを消す
                this.tagAlreadyExistsErrorFlag = false
                this.newTagErrorFlag = false
            })
            .catch((error) =>{
                // console.log(error.response);
                if (error.response.status == 400) { this.tagAlreadyExistsErrorFlag = true }
            })
            this.newTagSending = false
        },
        searchTagCheck:_.debounce(_.throttle(async function(){
            //空の状態ならalltagを入れとく
            if (this.tagToSearch == '') {
                this.tagSearchResultList = this.allTagList
                return
            }
            this.searchTag()
        },100),150),
        async searchTag(){
            this.tagSerchLoading = true
            this.tagSearchResultList = []
            await axios.post('/api/tag/search',{
                userId:this.userId,
                tag:this.tagToSearch
            })
            .then((res)=>{
                for (const tag of res.data) {
                    this.tagSearchResultList.push({
                        id:tag.id,
                        name:tag.name
                    })
                }
                this.tagSerchLoading = false
            })
        },
        async getAllTag(){
            await axios.post('/api/tag/serveUserAllTag',{userId:this.userId})
            .then((res)=>{
                    for (const tag of res.data) {
                        this.allTagList.push({
                            id:tag.id,
                            name:tag.name
                        })
                    }
                    this.tagSearchResultList = this.allTagList
            })
            .catch((error)=>{})
        },
        async getAddedTag(){
            await axios.post('/api/tag/serveAddedTag',{userId:this.userId})
            .then((res)=>{
                // リストに追加
                this.allTagList.push({
                    id:res.data.id,
                    name:res.data.name
                })
            })
            .catch((error)=>{})
        },
        createNewTagFlagSwitch(){ this.createNewTagFlag = !this.createNewTagFlag },
        tagDialogFlagSwithch(){
            this.tagDialogFlag = !this.tagDialogFlag

            // 新規登録の入力欄を消す
            this.createNewTagFlag =false
            this.newTag = ''

            // 全部のタグをリストに表示するように戻す
            this.tagToSearch = ''
            this.tagSearchResultList = this.allTagList

            //エラーを消す
            this.tagAlreadyExistsErrorFlag = false
            this.newTagErrorFlag = false
        },
        serveCheckedTagListToParent(){ return this.checkedTagList}
    },
    mounted() {
        this.getAllTag()
    },
}
</script>

<style lang="scss" scoped>
.tagDialog{
    .v-list{
        margin: 0;
        padding:0;
    }
    // .v-input__details{
    //     margin: 0;
    //     padding: 0;
    //     height: 0;
    //     width: 0;
    // }
    .areaCreateNewTag{margin: 10px;}
    .areaTagSerch{
        margin: 20px 5px 5px 5px;
        .v-col{
            padding-top:0;
            padding-bottom:0;
        }
    }
}
</style>
