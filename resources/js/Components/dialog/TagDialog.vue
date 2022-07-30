<template>
    <div>
        <!-- ダイアログを呼び出すためのボタン -->
        <v-btn class="longButton" color="submit" @click.stop="tagDialogFlagSwithch">
        <v-icon>mdi-tag</v-icon>
        tag
        </v-btn>

        <!-- v-modelがv-ifとかの代わりになっている -->
        <v-dialog
            v-model="tagDialogFlag"
            scrollable
            persistent>

            <section class="Dialog tagDialog">
                <v-btn color="#E57373"   x-small elevation="2" @click.stop="tagDialogFlagSwithch()">
                <v-icon>mdi-close-box</v-icon>
                閉じる
                </v-btn>
                <!-- 検索窓とか -->
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
                        @click="searchTag()">
                        <v-icon>mdi-magnify</v-icon>
                        検索
                        </v-btn>
                    </v-col>
                </v-row>

                <!-- loadingアニメ -->
                <loading v-if="tagSerchLoading"></loading>

                <!-- タグ一覧 -->
                <v-list
                    class="overflow-y-auto mx-auto"
                    width="100%"
                    max-height="50vh">

                    <v-list-item v-for="tag of tagSearchResultList" :key="tag.id">
                        <input type="checkbox" :id="tag.id" v-model="checkedTagList" :value="tag.id">
                        <label :for="tag.id">{{tag.name}}</label>
                    </v-list-item>

                </v-list>

                <!--  -->
                <v-btn
                    class="longButton my-4"
                    color="submit"
                    v-if="!createNewTagFlag"
                    @click.stop="createNewTagFlagSwitch">
                    <v-icon>mdi-tag-plus</v-icon>
                    新規作成
                </v-btn>

                <!-- 新規タグ作成 -->
                <div class="areaCreateNewTag" v-if="createNewTagFlag">
                    <p class="error" v-if="newTagErrorFlag">文字を入力してください</p>
                    <p class="error" v-if="tagAlreadyExistsErrorFlag">そのタグはすでに登録されいます</p>

                    <v-text-field v-model="newTag" label="新しいタグ"></v-text-field>

                    <v-btn
                    class="longButton"
                    color="#BBDEFB"
                    elevation="2"
                    :disabled="newTagSending"
                    @click.stop="createNewTagCheck()">
                    <v-icon>mdi-content-save</v-icon>
                    作成
                    </v-btn>
                </div>
            </section>
        </v-dialog>
    </div>
</template>

<script>
import loading from '@/Components/loading/loading.vue'
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
        checkedTagList:[],
        tagSearchResultList:[]
      }
    },
    props:['originalCheckedTag'],
    components:{loading},
    methods: {
        // 新規タグ作成
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
                tag   :this.newTag
            })
            .then((res)=>{
                // 読み込み直し
                this.tagToSearch = ''
                this.searchTag()
                // this.getAddedTag()
                // 入力欄を消す
                this.createNewTagFlag=false
                this.newTag=''

                //エラーを消す
                this.tagAlreadyExistsErrorFlag = false
                this.newTagErrorFlag = false
            })
            .catch((error) =>{
                // console.log(error.response);
                // ダブりエラー
                if (error.response.status == 400) { this.tagAlreadyExistsErrorFlag = true }
            })
            this.newTagSending = false
        },
        // タグ検索
        searchTag:_.debounce(_.throttle(async function(){
            this.tagSerchLoading = true
            this.tagSearchResultList = []
            await axios.post('/api/tag/search',{
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
        },100),150),
        // 切り替え
        createNewTagFlagSwitch(){ this.createNewTagFlag = !this.createNewTagFlag },
        tagDialogFlagSwithch(){
            this.tagDialogFlag = !this.tagDialogFlag

            // 新規登録の入力欄を消す
            this.createNewTagFlag =false
            this.newTag = ''

            // 検索窓を初期化
            this.tagToSearch = ''

            //エラーを消す
            this.tagAlreadyExistsErrorFlag = false
            this.newTagErrorFlag = false

            //開くときは全部取得した状態に
            if (this.tagDialogFlag == true) { this.searchTag() }
        },
        //親にチェックリストを渡す
        serveCheckedTagListToParent(){ return this.checkedTagList},
    },
    mounted() {
        if (this.originalCheckedTag != null) {
            for (const tag of this.originalCheckedTag) {
                    this.checkedTagList.push(tag.id)
            }
        }
    },
}
</script>

<style lang="scss" scoped>
.tagDialog{
    .v-list{
        margin: 0;
        padding:0;
        .v-list-item{
            padding:0 10px;
        }
        label{
            font-size: 1.5vmax;
            padding-left: 10px;
        }
    }
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
