<template>
    <BaseLayout title="記事検索" pageTitle="記事検索">
        <v-container>

            <SearchField
                ref = "SearchField"
                searchLabel   ="記事検索"
                :loadingFlag  ="loading"
                :orignalKeyWord="old.keyword"
                @triggerSearch="search({
                    page:1,
                    keyword:this.$refs.SearchField.serveKeywordToParent(),
                    tagList:this.$refs.tagDialog.serveCheckedTagList(),
                    searchTarget:this.$refs.DetailComponent.serveChecked()
                })"
                >
            </SearchField>

            <TagDialog
                ref="tagDialog"
                text = "検索するタグ"
                :originalCheckedTagList="old.tagList"
                :searchOnly="true"/>

            <DetailComponent
                ref="DetailComponent"
                summary="検索対象"
                :defaltChecked="old.searchTarget"
                :elements="detailElements"
            ></DetailComponent>

            <!-- loadingアニメ -->
            <loading v-show="loading"></loading>

            <template v-for="article of result.data" :key="article.id">
                <ArticleContainer
                    v-if="!loading"
                    :article="article"
                />
            </template>
        <v-pagination
            v-model="page"
            :length="result.last_page"
        ></v-pagination>
        </v-container>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'

import TagDialog from '@/Components/dialog/TagDialog.vue';
import loading from '@/Components/loading/loading.vue'
import DetailComponent from '@/Components/atomic/DetailComponent.vue';
import SearchField from '@/Components/SearchField.vue';
import ArticleContainer from '@/Components/contents/ArticleContainer.vue';

import MakeListTools from '@/tools/MakeListTools.js';

const makeListTools = new MakeListTools()

export default{
    data() {
        return {
            page: this.result.current_page,
            loading:false,
            detailElements:[
                {
                    label:"タイトルのみ",
                    value:"title"
                },
                {
                    label:"本文のみ",
                    value:"body"
                }
            ]
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
        loading,
        SearchField,
        ArticleContainer,
        DetailComponent
    },
    methods: {
        // 検索用
        search({page,keyword,tagList,searchTarget}){
            this.loading     = true
            this.$inertia.get('/Article/Search' ,{
                page    : page,
                keyword : keyword,
                tagList : tagList,
                searchTarget:searchTarget,
                onError:(errors) => {
                    console.log(errors)
                    this.loading = false
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
    // mounted() {
    // },
}
</script>

<style lang="scss" scoped>
.content{margin-bottom: 1.2rem;}
.TagDialog{margin:1rem 0;}
.DetailComponent{margin:1rem 0 ;}
</style>
