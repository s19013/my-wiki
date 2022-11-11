<template>
    <div>
        <!-- ダイアログを呼び出すためのボタン -->
        <div class="dialogAndList">
            <v-btn color="submit"
                size="small"
                class="global_css_haveIconButton_Margin"
                :disabled="disabledFlag" :loading="disabledFlag"
                @click.stop="openTagDialog()">
                <v-icon>mdi-tag</v-icon>
                <p>タグ</p>
            </v-btn>
            <TagList
                :tagList="checkedTagList"
                :text="text"
            />
        </div>

        <!-- v-modelがv-ifとかの代わりになっている -->
        <v-dialog v-model="tagDialogFlag" scrollable>

            <section class="global_css_Dialog tagDialog">
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
                @triggerSearch="searchBranch"
                >
                </SearchField>

                <!-- 操作ボタン -->
                <div class="control">
                    <v-btn
                        variant="outlined"
                        color="primary"
                        size="small"
                        @click.stop="clearAllCheck"
                        >
                            チェックをすべて外す
                    </v-btn>
                    <div class="existCheckbox">
                        <!-- この部分を既存チェックボックスという -->
                        <input type="checkbox" id="checked" v-model="onlyCheckedFlag">
                        <label for="checked">チェックがついているタグだけを表示</label>
                    </div>
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
                        class="global_css_haveIconButton_Margin my-4 global_css_longButton"
                        color="submit"
                        v-show="!createNewTagFlag"
                        @click.stop="createNewTagFlagSwitch">
                        <v-icon>mdi-tag-plus</v-icon>
                        <p>新規作成</p>
                    </v-btn>

                    <!-- 新規タグ作成 -->
                    <div class="areaCreateNewTag" v-show="createNewTagFlag">
                        <p class="global_css_error" v-if="newTagErrorFlag">文字を入力してください</p>
                        <p class="global_css_error" v-if="tagAlreadyExistsErrorFlag">そのタグはすでに登録されいます</p>

                        <v-form v-on:submit.prevent ="createNewTagCheck">
                            <v-text-field v-model="newTag" label="新しいタグ" outlined hide-details="false"></v-text-field>
                        </v-form>

                        <v-btn
                            class="global_css_haveIconButton_Margin global_css_longButton"
                            color="#BBDEFB"
                            elevation="2"
                            :disabled="newTagSending"
                            @click.stop="createNewTagCheck()">
                            <v-icon>mdi-content-save</v-icon>
                            <p>作成</p>
                        </v-btn>
                    </div>

                </div>
            </section>
        </v-dialog>
    </div>
</template>

<script>
import loading from '@/Components/loading/loading.vue'
import SearchField from '@/Components/SearchField.vue';
import TagList from '@/Components/TagList.vue';
export default{
    data() {
      return {
        newTag:'',

        // flag
        onlyCheckedFlag  :false,
        createNewTagFlag :false,
        tagDialogFlag    :false,
        isFirstSearchFlag:true,

        //loding
        tagSerchLoading:false,
        newTagSending  :false,

        // errorFlag
        newTagErrorFlag          :false,
        tagAlreadyExistsErrorFlag:false,

        // tagList
        checkedTagList     :[],
        tagSearchResultList:[],
        tagCashList        :[],//全件検索のキャッシュ
        allTagCashList     :[],//全件検索のキャッシュ
      }
    },
    props:{
        originalCheckedTagList:{
            //更新や閲覧画面で既にチェックがついているタグを受け取るため
            type   :Array,
            default:[],
        },
        searchOnly:{
            //記事検索などでは新規作成を表示させないようにするため
            type   :Boolean,
            default:false,
        },
        text:{
            type:String,
            default:"つけたタグ"
        },
        disabledFlag:{
            type   :Boolean,
            default:false
        }
    },
    components:{
        loading,
        SearchField,
        TagList
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
                //検索欄をリセット
                this.$refs.SearchField.resetKeyword()

                // 読み込み直し
                this.isFirstSearchFlag = true
                this.searchAllTag()

                // 入力欄を消す
                this.createNewTagFlag=false
                this.newTag=null

                //エラーを消す
                this.tagAlreadyExistsErrorFlag = false
                this.newTagErrorFlag = false
            })
            .catch((error) =>{
                // ダブりエラー
                if (error.response.status == 400) { this.tagAlreadyExistsErrorFlag = true }
                else{console.log(error.response);}
            })
            //ローディングアニメ解除
            this.newTagSending = false
        },
        // タグ検索
        searchBranch:_.debounce(_.throttle(function(){
            if (this.$refs.SearchField.serveKeywordToParent() == "") {
                //初期ローディング以外の全件検索だったらキャッシュを使う
                if (this.isFirstSearchFlag == false) {
                    this.tagSearchResultList = this.allTagCashList
                    this.tagCashList         = this.allTagCashList
                }
                // 初期ローディング､更新後の全件検索
                else {this.searchAllTag()}
            }
            // 他の検索
            else {this.searchTag()}
            console.log(this.tagSerchLoading);
        },100),150),
        // 全件検索
        async searchAllTag(){
            //ローディングアニメ開始
            this.tagSerchLoading = true

            //既存チェックボックスのチェックを外す
            this.onlyCheckedFlag = false

            //配列,キャッシュ初期化
            this.tagSearchResultList = []
            this.tagCashList         = []//キャッシュをクリアするのは既存チェックボックスを外す時に出てくるバグを防ぐため

            await axios.post('/api/tag/search',{tag:''})
            .then((res)=>{
                for (const tag of res.data) {
                    this.tagSearchResultList.push({
                        id:tag.id,
                        name:tag.name
                    })
                }
                //キャッシュにコピー
                this.allTagCashList = [...this.tagSearchResultList]
                this.tagCashList    = [...this.tagSearchResultList]
            })
            .catch((err)=>{console.log(err);})

            //ローディングアニメ解除
            this.tagSerchLoading = false

            //初期ローディングフラグを切る
            this.isFirstSearchFlag = false
        },
        // タグ検索
        async searchTag(){
            //ローディングアニメ開始
            this.tagSerchLoading = true
            console.log(this.tagSerchLoading);

            //既存チェックボックスのチェックを外す
            this.onlyCheckedFlag = false

            //配列,キャッシュ初期化
            this.tagSearchResultList = []
            this.tagCashList         = []//キャッシュをクリアするのは既存チェックボックスを外す時に出てくるバグを防ぐため
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
                this.tagCashList = [...this.tagSearchResultList]
            })
            .catch((err)=>{console.log(err);})

            //ローディングアニメ解除
            this.tagSerchLoading = false
        },
        //チェック全消し
        clearAllCheck(){this.checkedTagList = []},
        // 切り替え
        createNewTagFlagSwitch(){ this.createNewTagFlag = !this.createNewTagFlag },
        tagDialogFlagSwithch(){this.tagDialogFlag = !this.tagDialogFlag},
        //
        openTagDialog() {
            this.tagDialogFlagSwithch()
            this.searchAllTag()
        },
        closeTagDialog(){
            this.tagDialogFlagSwithch()
            // 新規登録の入力欄を消す
            this.createNewTagFlag =false
            this.newTag           = ''

            //エラーを消す
            this.tagAlreadyExistsErrorFlag = false
            this.newTagErrorFlag           = false

            //既存チェックのチェックを外す
            this.onlyCheckedFlag = false

            // チェックをつけたタグをソード
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
                this.tagSearchResultList = this.tagCashList
            }
        }
    },
    mounted() {
        //originalCheckedTagListの中が完全に空ではなかったら代入
        if (this.originalCheckedTagList.length != 0 ) {
            this.checkedTagList = this.originalCheckedTagList
        }

        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            //ダイアログが開いている時有効にする
            if(this.tagDialogFlag == true){
                if (event.key === "Escape") {
                    this.tagDialogFlag = false
                    return
                }
            }
        })
    },
}
</script>

<style lang="scss" scoped>
.tagDialog{
    .v-list{
        padding:0;
        .v-list-item{padding:0 1rem;}
    }
    label{
        font-size: 1.5rem;
        padding-left: 0.5rem;
        width:100%;
    }
    .areaCreateNewTag{
        margin:1rem 0;
        button{margin-top: 0.8rem;}
    }
    .clooseButton{margin-bottom: 0.5rem;}
    .existCheckbox{
        label{font-size: 1.1rem;}
        margin:0.5rem 0;
    }
}

@media (min-width: 601px){
    .control{
        display:grid;
        grid-template-columns:0.6fr 0.1fr 1fr;
        .v-btn{grid-column:1/2}
        .existCheckbox{
            text-align: right;
            grid-column:3/4
        }
    }
    .dialogAndList{
        display:grid;
        grid-template-rows:auto auto;
        grid-template-columns:5fr 1fr;
        margin:1.2rem 0;
        .tagList{
            grid-row: 1/3;
            grid-column: 1/2;
        }
        button{
            grid-row: 1/3;
            grid-column: 2/3;
        }
    }
}

@media (max-width: 600px){
.tagDialog{label{font-size: 1.2rem;}}
.dialogAndList{
        display:grid;
        grid-template-rows:auto auto;
        margin:1.2rem 0;
        .tagList{
            grid-row: 2/3;
        }
        button{
            margin-bottom:1rem;
            grid-row: 1/2;
        }
    }
}
</style>
