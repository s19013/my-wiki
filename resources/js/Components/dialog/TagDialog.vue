<template>
    <div>
        <!-- ダイアログを呼び出すためのボタン -->
        <v-btn class="longButton" color="submit" size="small" @click.stop="tagDialogFlagSwithch">
        <v-icon>mdi-tag</v-icon>
        タグ
        </v-btn>

        <!-- v-modelがv-ifとかの代わりになっている -->
        <v-dialog
            v-model="tagDialogFlag"
            scrollable
            persistent>

            <section class="Dialog tagDialog">
                <div class="clooseButton">
                    <v-btn color="#E57373" size="small" elevation="2" @click.stop="closeTagDialog()">
                        <v-icon
                        >mdi-close-box</v-icon>閉じる
                    </v-btn>
                </div>

                <SearchField
                ref = "SearchField"
                searchLabel="タグ検索"
                :loadingFlag="tagSerchLoading"
                @triggerSearch="searchTag"
                >
                </SearchField>

                <!-- 操作ボタン -->
                <div>
                    <v-row>
                        <v-col cols="3">
                            <v-btn
                            variant="outlined"
                            color="primary"
                            size="small"
                            @click.stop="clearAllCheck"
                            >
                                チェックをすべて外す
                            </v-btn>
                        </v-col>
                        <v-col cols="3"></v-col>
                        <v-col cols="6">
                            <!-- この部分を既存チェックボックスという -->
                            <input type="checkbox" id="checked" v-model="onlyCheckedFlag">
                            <label for="checked">チェックがついているタグだけを表示</label>
                        </v-col>
                    </v-row>
                </div>

                <!-- loadingアニメ -->
                <loading v-show="tagSerchLoading"></loading>

                <!-- タグ一覧 -->
                <v-list
                    class="overflow-y-auto mx-auto"
                    width="100%"
                    max-height="45vh">

                    <v-list-item v-for="tag of tagSearchResultList" :key="tag.id">
                        <input type="checkbox" :id="tag.id" v-model="checkedTagList" :value="{id:tag.id,name:tag.name}">
                        <label :for="tag.id">{{tag.name}}</label>
                    </v-list-item>

                </v-list>

                <!--  -->
                <div v-if="!searchOnly">

                    <v-btn
                        class="longButton my-4"
                        color="submit"
                        v-show="!createNewTagFlag"
                        @click.stop="createNewTagFlagSwitch">
                        <v-icon>mdi-tag-plus</v-icon>
                        新規作成
                    </v-btn>

                    <!-- 新規タグ作成 -->
                    <div class="areaCreateNewTag" v-show="createNewTagFlag">
                        <p class="error" v-if="newTagErrorFlag">文字を入力してください</p>
                        <p class="error" v-if="tagAlreadyExistsErrorFlag">そのタグはすでに登録されいます</p>

                        <v-form v-on:submit.prevent ="createNewTagCheck">
                            <v-text-field v-model="newTag" label="新しいタグ"></v-text-field>
                        </v-form>

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

                </div>
            </section>
        </v-dialog>
    </div>
</template>

<script>
import loading from '@/Components/loading/loading.vue'
import SearchFieldVue from '@/Components/SearchField.vue';
export default{
    data() {
      return {
        newTag:'',

        // flag
        onlyCheckedFlag:false,
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
        tagSearchResultList:[],
        tagListCash:[],//キャッシュ 既存チェックボックスのつけ外しで使う
      }
    },
    props:{
        originalCheckedTagList:{
            //更新や閲覧画面で既にチェックがついているタグを受け取るため
            type:Array,
            default:null,
        },
        searchOnly:{
            //記事検索などでは新規作成を表示させないようにするため
            type:Boolean,
            default:false,
        },
    },
    components:{
        loading,
        SearchField
    },
    methods: {
        //エラーチェック
        createNewTagCheck:_.debounce(_.throttle(async function(){
            if (this.newTag == '') {
                this.newTagErrorFlag = true
                return
            }
            else {this.createNewTag()}
        },100),150),
        // 新規タグ作成
        createNewTag(){
            //ローディングアニメ開始
            this.newTagSending = true

            axios.post('/api/tag/store',{
                tag   :this.newTag
            })
            .then((res)=>{
                // 読み込み直し
                this.searchTag()

                // 入力欄を消す
                this.createNewTagFlag=false
                this.newTag=''

                //検索欄をリセット
                this.tagToSearch = ''

                //エラーを消す
                this.tagAlreadyExistsErrorFlag = false
                this.newTagErrorFlag = false
            })
            .catch((error) =>{
                // console.log(error.response);
                // ダブりエラー
                if (error.response.status == 400) { this.tagAlreadyExistsErrorFlag = true }
            })
            //ローディングアニメ解除
            this.newTagSending = false
        },
        // タグ検索
        searchTag:_.debounce(_.throttle(async function(){

            //ローディングアニメ開始
            this.tagSerchLoading = true

            //既存チェックボックスのチェックを外す
            this.onlyCheckedFlag = false

            //配列,キャッシュ初期化
            this.tagSearchResultList = []
            this.tagListCash = []//キャッシュをクリアするのは既存チェックボックスを外す時に出てくるバグを防ぐため

            await axios.post('/api/tag/search',{
                tag:this.$refs.SearchField.serveKeywordToParent()
            })
            .then((res)=>{
                for (const tag of res.data) {
                    this.tagSearchResultList.push({
                        id:tag.id,
                        name:tag.name
                    })
                }
                //キャッシュにコピー
                this.tagListCash = [...this.tagSearchResultList]
                //ローディングアニメ解除
                this.tagSerchLoading = false
            })
            .catch((err)=>{console.log(err);})
        },100),150),
        //チェック全消し
        clearAllCheck(){this.checkedTagList = []},
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

            //チェックを外す
            this.onlyCheckedFlag = false

            //開くときは全部取得した状態に
            if (this.tagDialogFlag == true) { this.searchTag() }
        },
        closeTagDialog(){ //閉じる時用
            this.tagDialogFlagSwithch()
            this.checkedTagList = this.checkedTagList.sort(this.sortArrayByName)
            this.$emit('closedTagDialog',this.checkedTagList)
        },
        //タグを名前順でソート
        sortArrayByName(x, y){
            if (x.name < y.name) {return -1;}
            if (x.name > y.name) {return 1;}
            return 0;
        },
        //親にチェックリストを渡す
        serveCheckedTagListToParent(){
            // this.checkedTagListにnameも追加しないといけなくなったのでそのままthis.checkedTagListを返せない
            // -> 返そうものならバックの処理に大きな変更が必要になる
            // ここでidだけの配列を作って返すほうが変更が少ない
            var temp = []
            for (const tag of this.checkedTagList){ temp.push(tag["id"]) }
            return temp
        },
    },
    watch:{
        onlyCheckedFlag:function(){
            //チェックをつけた場合
            if (this.onlyCheckedFlag == true) {
                //チェックがついているタグだけを表示
                this.checkedTagList.sort(this.sortArrayByName)
                this.tagSearchResultList = this.checkedTagList
            }
            else if (this.onlyCheckedFlag == false && this.tagDialogFlag == true) {
                //全タグのキャッシュに置き換える
                //参照元を変えるだけなので読み込みが早い
                this.tagSearchResultList = this.tagListCash
            }
        }
    },
    mounted() {
        //originalCheckedTagListの中が完全に空ではなかったら代入
        if (this.originalCheckedTagList != null) {
            this.checkedTagList = this.originalCheckedTagList
        }
    },
}
</script>

<style lang="scss" scoped>
.tagDialog{
    .v-list{
        margin :5px 0;
        padding:0;
        .v-list-item{
            padding:0 10px;
        }
    }
    label{
            font-size: 1.5vmax;
            padding-left: 10px;
            width: 100%;
    }
    .areaCreateNewTag{margin: 10px;}
    .clooseButton{margin-bottom: 10px;}
}
</style>
