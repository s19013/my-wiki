<template>
    <BaseLayout title="ブックマーク検索" pageTitle="ブックマーク検索">
        <v-container>
            <SearchField
                ref        = "SearchField"
                searchLabel="ブックマーク検索"
                :loadingFlag  ="loading"
                :orignalKeyWord="old.keyword"
                @triggerSearch="search"
                />

            <TagDialog
                ref="tagDialog"
                class="mb-10"
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
            <loading v-show="loading"/>

            <template v-for="bookMark of result.data" :key="bookMark.id">
                <BookMarkContainer
                    v-if="!loading"
                    :bookMark="bookMark"
                />
            </template>
            <v-pagination
                v-model="page"
                :length="result.last_page"
            />
        </v-container>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'
import { Link } from '@inertiajs/inertia-vue3';
import TagDialog from '@/Components/dialog/TagDialog.vue';
import loading from '@/Components/loading/loading.vue';
import DetailComponent from '@/Components/atomic/DetailComponent.vue';
import SearchField from '@/Components/SearchField.vue';
import BookMarkContainer from '@/Components/contents/BookMarkContainer.vue';

export default{
    data() {
        return {
            page: this.result.current_page,
            loading:false,
            searchTarget:this.old.searchTarget,
            detailElements:[
                {
                    label:"タイトルのみ",
                    value:"title"
                },
                {
                    label:"urlのみ",
                    value:"url"
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
        Link,
        TagDialog,
        loading,
        SearchField,
        BookMarkContainer,
        DetailComponent
    },
    methods: {
        // 検索用
        search(){
            this.loading     = true
            this.$inertia.get('/BookMark/Search' ,{
                page :1,
                keyword : this.$refs.SearchField.serveKeywordToParent(),
                tagList : this.$refs.tagDialog.serveCheckedTagListToParent(),
                searchTarget:this.$refs.DetailComponent.serveChecked(),
                onError:(error) => {
                    console.log(error)
                    this.loading = false
                }
            })
        },
        // ページめくり
        pagination(){
            this.loading = true
            this.$inertia.get('/BookMark/Search' ,{
                page : this.page,
                keyword : this.old.keyword,
                tagList : this.old.tagList,
                searchTarget:this.$refs.DetailComponent.serveChecked(),
                onError:(error) => {
                    console.log(error)
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
            this.pagination();
        }
    },
    mounted() {
    },
}
</script>

<style lang="scss" scoped>
.content{margin-bottom: 1.2rem;}
details{
    margin-bottom: 0.5rem;
    input,label,summary{ cursor: pointer; }
}
</style>
