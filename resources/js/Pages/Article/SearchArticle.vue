<template>
    <BaseLayout :title="messages.title" :pageTitle="messages.title">
        <v-container>
            <SearchField
                ref = "SearchField"
                :searchLabel   ="messages.title"
                :orignalKeyWord="old.keyword"
                @triggerSearch="search({
                    page   :1,
                    keyword:this.$refs.SearchField.serveKeywordToParent(),
                    tagList:this.$refs.TagDialog.serveCheckedTagList(),
                    searchTarget  :this.searchTarget,
                    searchQuantity:this.$refs.SearchOption.serveSearchQuantity(),
                    sortType:this.$refs.SearchOption.serveSort()
                })"
                >
            </SearchField>

            <TagDialog
                ref="TagDialog"
                :text = "messages.TagDialogLabel"
                :originalCheckedTagList="old.tagList"
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
                searchTarget:{
                    label:"検索対象",
                    title:"タイトル",
                    body :"本文"
                },
            },
            messages:{
                title:'Search Article',
                TagDialogLabel:"Search Tag",
                searchTarget:{
                    label:"Search Target",
                    title:"title",
                    body :"body"
                },
            },
            page: this.result.current_page,
            searchTarget:this.old.searchTarget,
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
        search({page,keyword,tagList,searchTarget,searchQuantity,sortType}){
            this.$store.commit('switchGlobalLoading')
            this.$inertia.get('/Article/Search' ,{
                page    : page,
                keyword : keyword,
                tagList : tagList,
                searchTarget:searchTarget,
                searchQuantity:searchQuantity,
                sortType:sortType,
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
                    // 送信
                    if(event.code === "Enter"){
                        this.search({
                            page:1,
                            keyword:this.$refs.SearchField.serveKeywordToParent(),
                            tagList:this.$refs.TagDialog.serveCheckedTagList(),
                            searchTarget:this.searchTarget,
                            searchQuantity:this.$refs.SearchOption.serveSearchQuantity(),
                            sortType:this.$refs.SearchOption.serveSort()
                        })
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
    // ページネーションのボタン類を押した場合の処理
    // 厳密にはページネーションのボタン類を押すとpageの値が変化するのでそれをwatchしてページネーションを起動
        page:function(newValue,oldValue){
            console.log(newValue);
            this.search({
                page    : newValue,
                keyword : this.old.keyword,
                tagList : makeListTools.tagIdList(this.old.tagList),
                searchTarget  : this.old.searchTarget,
                searchQuantity: this.old.searchQuantity,
                sortType:this.old.sortType
            });
        }
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
</style>
