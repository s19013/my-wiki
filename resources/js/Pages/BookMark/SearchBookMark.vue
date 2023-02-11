<template>
    <BaseLayout :title="messages.title" :pageTitle="messages.title">
        <v-container>
            <SearchField
                ref        = "SearchField"
                :searchLabel="messages.searchBookmarkLabel"
                :orignalKeyWord="old.keyword"
                @triggerSearch="search({
                    page:1,
                    keyword:this.$refs.SearchField.serveKeywordToParent(),
                    tagList:this.$refs.tagDialog.serveCheckedTagList(),
                    searchTarget:this.searchTarget
                })"
            />

            <TagDialog
                ref="tagDialog"
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
                        <input type="radio" id="url"
                        value="url" v-model="searchTarget"/>
                        <label for="url">
                            Url
                        </label>
                    </div>
                </div>
            </div>

            <template v-for="bookMark of result.data" :key="bookMark.id">
                <BookMarkContainer
                    :bookMark="bookMark"
                />
            </template>
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
import SearchField from '@/Components/SearchField.vue';
import BookMarkContainer from '@/Components/contents/BookMarkContainer.vue';
import loadingDialog from '@/Components/dialog/loadingDialog.vue';

import MakeListTools from '@/tools/MakeListTools.js';

const makeListTools = new MakeListTools()

export default{
    data() {
        return {
            japanese:{
                title:'ブックマーク検索',
                searchBookmarkLabel:"ブックマーク検索",
                searchTarget:{
                    label:"検索対象",
                    title:"タイトル",
                },
                TagDialogLabel:"検索するタグ"
            },
            messages:{
                title:'Search Bookmark',
                searchBookmarkLabel:"Search Bookmark",
                searchTarget:{
                    label:"Search Target",
                    title:"title",
                },
                TagDialogLabel:"Search Tag"
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
        Link,
        TagDialog,
        loadingDialog,
        SearchField,
        BookMarkContainer,
        DetailComponent
    },
    methods: {
        // 検索用
        search({page,keyword,tagList,searchTarget}){
            this.$store.commit('switchGlobalLoading')
            this.$inertia.get('/BookMark/Search' ,{
                page    : page,
                keyword : keyword,
                tagList : tagList,
                searchTarget:searchTarget,
                onError:(errors) => {
                    console.log(errors)
                    this.$store.commit('switchGlobalLoading')
                }
            })
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
                searchTarget : this.old.searchTarget
            });
        }
    },
    mounted() {
        this.$store.commit('setGlobalLoading',false)
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
    },
}
</script>

<style lang="scss" scoped>
.content{margin-bottom: 1.2rem;}
.TagDialog{margin:1rem 0;}
.DetailComponent{margin:1rem 0 ;}
.searchTarget{
    .options{
        display: flex;
        gap:1rem;
        .option{width:fit-content}
    }
}
</style>
