<template>
    <BaseLayout :title="`${title} ${url}   ${messages.title}`" :pageTitle="messages.title">
        <v-container>
            <div class="searchField">
                <v-form v-on:submit.prevent ="search()">
                    <v-text-field
                        v-model="title"
                        :label ="messages.titleLabel"
                        outlined hide-details="false"
                        clearable
                        @keypress.enter="search()"
                    ></v-text-field>

                    <v-text-field
                        v-model="url"
                        :label ="messages.urlLabel"
                        outlined hide-details="false"
                        clearable
                        @keypress.enter="search()"
                    ></v-text-field>

                    <v-btn color="submit"
                        class="global_css_haveIconButton_Margin"
                        elevation="2"
                        @click.stop="search()">
                        <v-icon>mdi-magnify</v-icon>
                        <p v-if="$store.state.lang == 'ja'">検索</p>
                        <p v-else>search</p>
                    </v-btn>
                </v-form>
            </div>

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

            <div class="searchOption">

                <SortAndQuantityOption
                    ref="SortAndQuantityOption"
                    :oldSearchQuantity="Number(this.old.searchQuantity)"
                    :oldSortType="this.old.sortType"
                    :sortLabelList="this.messages.sort"
                />
            </div>

            <template v-for="bookMark of result.data" :key="bookMark.id">
                <BookMarkContainer
                    :bookMark="bookMark"
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
            />
        </v-container>
        <!-- loadingアニメ -->
        <loadingDialog/>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'

import { Link } from '@inertiajs/inertia-vue3';
import TagDialog from '@/Components/dialog/TagDialog.vue';
import DetailComponent from '@/Components/atomic/DetailComponent.vue';
import BookMarkContainer from '@/Components/contents/BookMarkContainer.vue';
import PageController from '@/Components/PageController.vue';
import loadingDialog from '@/Components/dialog/loadingDialog.vue';
import SortAndQuantityOption from '@/Components/SortAndQuantity.vue';

import MakeListTools from '@/tools/MakeListTools.js';

const makeListTools = new MakeListTools()

export default{
    data() {
        return {
            japanese:{
                title:'ブックマーク検索',
                titleLabel:'タイトル',
                urlLabel:'url',
                TagDialogLabel:"検索するタグ",
                untaggedLabel:"タグがないブックマークを探す",
                radioItems:[
                    {
                        value:"title",
                        label:"タイトル"
                    },
                    {
                        value:"url",
                        label:"URL"
                    },
                ],
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
                title:'Search BookMark',
                titleLabel:'title',
                urlLabel:'url',
                TagDialogLabel:"Search Tag",
                untaggedLabel:"Search bookmarks without tags",
                radioItems:[
                    {
                        value:"title",
                        label:"Title"
                    },
                    {
                        value:"url",
                        label:"URL"
                    },
                ],
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
            title:this.old.title,
            url:this.old.url,
            page: this.result.current_page,
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
        Link,
        TagDialog,
        loadingDialog,
        BookMarkContainer,
        DetailComponent,
        PageController,
        SortAndQuantityOption,
    },
    methods: {
        // 検索用
        async search(){
            this.$store.commit('switchGlobalLoading')
            await this.$inertia.get('/BookMark/Search' ,{
                page:1,
                title:this.title,
                url:this.url,
                tagList:this.$refs.TagDialog.serveCheckedTagList(),
                searchQuantity:this.$refs.SortAndQuantityOption.serveSearchQuantity(),
                sortType:this.$refs.SortAndQuantityOption.serveSort(),
                isSearchUntagged :(this.isSearchUntaggedCheckBox == true) ? 1 : 0,
                onError:(errors) => {
                    console.log(errors)
                    this.$store.commit('switchGlobalLoading')
                }
            })
        },
        pagination(page){
            this.$store.commit('switchGlobalLoading')
            this.$inertia.get('/BookMark/Search' ,{
                page    : page,
                title:this.title,
                url:this.url,
                tagList : makeListTools.tagIdList(this.old.tagList),
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
                    // 送信
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
        pageIncrease(){
            if (this.page < this.result.last_page) { this.page += 1 }
        },
        pageDecrease(){
            if (this.page > 1) {this.page -= 1}
        }
    },
    watch: {
    // @input="pagination"でできるはずなのにできないのでwatchで対応
    // 厳密にはページネーションのボタン類を押すとpageの値が変化するのでそれをwatchしてページネーションを起動
    // ページネーションのボタン類を押した場合の処理
        page:function(newValue,oldValue){this.pagination(newValue)}
    },
    mounted() {
        this.$store.commit('setGlobalLoading',false)
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })

        //キーボード受付
        document.addEventListener('keydown', this.keyEvents)
    },
    beforeUnmount() {
        //キーボードによる動作の削除(副作用みたいエラーがでるため)
        document.removeEventListener("keydown", this.keyEvents);
    }
}
</script>

<style lang="scss" scoped>
.content{margin-bottom: 1.2rem;}
.TagDialog{margin:0.5rem 0;}
.untaggedCheckbox{
    margin-top:0.5rem;
    label{
        margin-left:0.5rem;
        width:100%
    }
}
.searchField {
    form{
        display:grid;
        gap:0.5rem;
        grid-template-rows: 1fr 1fr;
        grid-template-columns:5fr 1fr;
        .v-input{grid-column: 1/2;}
        .v-btn  {
            grid-row: 1/2;
            grid-column: 2/3;
            width:100%;
        }
    }
}
.SearchTarget{margin-bottom: 0.8rem;}
.searchOption{margin-top: 1rem;}
</style>
