<template>
    <BaseLayout :title="messages.title" :pageTitle="messages.title">
        <v-container>
            <SearchField
                ref = "SearchField"
                :searchLabel   ="messages.title"
                :orignalKeyWord="old.keyword"
                @triggerSearch="search()"
                >
            </SearchField>

            <div class="untaggedCheckbox">
                <!-- この部分を既存チェックボックスという -->
                <input type="checkbox" id="checked" v-model="isSearchUntaggedCheckBox">
                <label for="checked">{{ messages.untaggedLabel }}</label>
            </div>

            <TagDialog
                ref="TagDialog"
                :text = "messages.TagDialogLabel"
                :originalCheckedTagList="old.tagList"
                :disabled="isSearchUntaggedCheckBox"
                :searchOnly="true"/>

            <div class="searchTarget">
                <p>{{messages.searchTarget.label}}</p>
                <div class="options">
                    <div class="option">
                        <input type="radio" id="searchTargetTitle"
                        value="title" v-model="searchTarget"/>
                        <label for="searchTargetTitle">
                            {{messages.searchTarget.title}}
                        </label>
                    </div>
                    <div class="option">
                        <input type="radio" id="searchTargetBody"
                        value="body" v-model="searchTarget"/>
                        <label for="searchTargetBody">
                            {{messages.searchTarget.body}}
                        </label>
                    </div>
                </div>
            </div>

            <SearchOption
                ref="SearchOption"
                :oldSearchQuantity="Number(this.old.searchQuantity)"
                :oldSortType="this.old.sortType"
                :sortLabelList="this.messages.sort"
            />

            <template v-for="article of result.data" :key="article.id">
                <ArticleContainer
                    :article="article"
                />
            </template>

        <PageController
            :page="page"
            :length="result.last_page"
            @clickPre ="page -= 1"
            @clickNext="page += 1"
        />

        <v-pagination
            v-model="page"
            :length="result.last_page"
        ></v-pagination>
        </v-container>
        <!-- loadingアニメ -->
        <loadingDialog/>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'

import TagDialog from '@/Components/dialog/TagDialog.vue';
import loadingDialog from '@/Components/dialog/loadingDialog.vue';
import DetailComponent from '@/Components/atomic/DetailComponent.vue';
import SearchField from '@/Components/SearchField.vue';
import ArticleContainer from '@/Components/contents/ArticleContainer.vue';
import PageController from '@/Components/PageController.vue';
import SearchOption from '@/Components/SearchOption.vue';

import MakeListTools from '@/tools/MakeListTools.js';

const makeListTools = new MakeListTools()

export default{
    data() {
        return {
            japanese:{
                title:'記事検索',
                TagDialogLabel:"検索するタグ",
                untaggedLabel:"タグがない記事を探す",
                searchTarget:{
                    label:"検索対象",
                    title:"タイトル",
                    body :"本文"
                },
                sort:[
                    {
                        label:"更新日 新 → 古",
                        value:"updated_at_desc"
                    },
                    {
                        label:"更新日 古 → 新",
                        value:"updated_at_asc"
                    },
                    {
                        label:"作成日 新 → 古",
                        value:"created_at_desc"
                    },
                    {
                        label:"作成日 古 → 新",
                        value:"created_at_asc"
                    },
                    {
                        label:"タイトル あ → ん",
                        value:"title_asc"
                    },
                    {
                        label:"タイトル ん → あ",
                        value:"title_desc"
                    },
                    {
                        label:"閲覧数 多 → 少",
                        value:"count_desc"
                    },
                    {
                        label:"閲覧数 少 → 多",
                        value:"count_asc"
                    },
                    {
                        label:"ランダム",
                        value:"random"
                    },
                ]
            },
            messages:{
                title:'Search Article',
                TagDialogLabel:"Search Tag",
                untaggedLabel:"Search articles without tags",
                searchTarget:{
                    label:"Search Target",
                    title:"title",
                    body :"body"
                },
                sort:[
                    {
                        label:"Updated Date new → old",
                        value:"updated_at_desc"
                    },
                    {
                        label:"Updated Date old → new",
                        value:"updated_at_asc"
                    },
                    {
                        label:"Created Date new → old",
                        value:"created_at_desc"
                    },
                    {
                        label:"Created Date old → new",
                        value:"created_at_asc"
                    },
                    {
                        label:"Title A → Z",
                        value:"title_asc"
                    },
                    {
                        label:"Title Z → A",
                        value:"title_desc"
                    },
                    {
                        label:"Views Most → Less",
                        value:"count_desc"
                    },
                    {
                        label:"Views Less → Most",
                        value:"count_asc"
                    },
                    {
                        label:"Random",
                        value:"random"
                    },
                ]
            },
            page: this.result.current_page,
            searchTarget:this.old.searchTarget,
            isSearchUntaggedCheckBox:(this.old.isSearchUntagged == 1) ? true : false
        }
    },
    props:{
        result:{
            type:Object
        },
        old:{
            type:Object
        }
    },
    components:{
        BaseLayout,
        TagDialog,
        loadingDialog,
        SearchField,
        ArticleContainer,
        DetailComponent,
        PageController,
        SearchOption
    },
    methods: {
        // 検索用
        pageIncrease(){if (this.page < this.result.last_page) { this.page += 1 }},
        pageDecrease(){if (this.page > 1) {this.page -= 1}},
        search(){
            this.$store.commit('switchGlobalLoading')
            this.$inertia.get('/Article/Search' ,{
                page:1,
                keyword:this.$refs.SearchField.serveKeywordToParent(),
                tagList:this.$refs.TagDialog.serveCheckedTagList(),
                searchTarget:this.searchTarget,
                searchQuantity:this.$refs.SearchOption.serveSearchQuantity(),
                sortType:this.$refs.SearchOption.serveSort(),
                isSearchUntagged :(this.isSearchUntaggedCheckBox == true) ? 1 : 0,
                onError:(errors) => {
                    console.log(errors)
                    this.$store.commit('switchGlobalLoading')
                }
            })
        },
        pagination(page){
            this.$store.commit('switchGlobalLoading')
            this.$inertia.get('/Article/Search' ,{
                page    : page,
                keyword : this.old.keyword,
                tagList : makeListTools.tagIdList(this.old.tagList),
                searchTarget  : this.old.searchTarget,
                searchQuantity: this.old.searchQuantity,
                sortType : this.old.sortType,
                isSearchUntagged :(this.old.isSearchUntagged == true) ? 1 : 0,
                onError:(errors) => {
                    console.log(errors)
                    this.$store.commit('switchGlobalLoading')
                }
            })
        },
        keyEvents(event){
            // ダイアログが開いている時,読み込み中には呼ばせない
            if( this.$store.state.globalLoading === false &&
                this.$store.state.someDialogOpening === false
            ){
                if (event.ctrlKey || event.key === "Meta") {
                    // 通常検索送信
                    if(event.code === "Enter"){
                        this.search()
                        return
                    }

                    // タグダイアログを開く
                    if ((event.ctrlKey || event.key === "Meta")
                    && event.altKey && event.code === "KeyT" ) {
                        event.preventDefault();
                        this.$refs.TagDialog.openTagDialog()
                    }

                    // ページめくり
                    if (event.key === "ArrowRight") {
                        this.pageIncrease()
                        return
                    }

                    // ページめくり
                    if (event.key === "ArrowLeft"
                    ) {
                        this.pageDecrease()
                        return
                    }
                }
            }
        },
    },
    watch: {
    // @input="pagination"でできるはずなのにできないのでwatchで対応
    // 厳密にはページネーションのボタン類を押すとpageの値が変化するのでそれをwatchしてページネーションを起動
    // ページネーションのボタン類を押した場合の処理
        page:function(newValue,oldValue){this.pagination(newValue)}
    },
    mounted() {
        //キーボード受付
        document.addEventListener('keydown', this.keyEvents)

        this.$store.commit('setGlobalLoading',false)
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
    },
    beforeUnmount() {
        //キーボードによる動作の削除(副作用みたいエラーがでるため)
        document.removeEventListener("keydown", this.keyEvents);
    }
}
</script>

<style lang="scss" scoped>
.content{margin-bottom: 1.2rem;}
.TagDialog{margin:1rem 0;}
.searchTarget{
    .options{
        display: flex;
        gap:1rem;
        .option{width:fit-content}
    }
    margin-bottom: 1rem;
}
.untaggedCheckbox{
    margin:0.5rem 0;
    label{
        margin-left:1rem;
        width:100%
    }
}

</style>
