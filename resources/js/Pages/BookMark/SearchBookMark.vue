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

            <details>
                <summary >検索対象</summary>
                <input type="radio" id="option1" value="title" v-model="searchTarget" />
                <label for="option1" class="me-6">タイトルのみ</label>

                <input type="radio" id="option2" value="url" v-model="searchTarget" />
                <label for="option2" class="me-6">urlのみ</label>

                <!-- <input type="radio" id="option3" value="titleAndBody" v-model="searchTarget" />
                <label for="option3">タイトルまたは本文(低速)</label> -->
            </details>

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
import SearchField from '@/Components/SearchField.vue';
import BookMarkContainer from '@/Components/contents/BookMarkContainer.vue';

export default{
    data() {
        return {
            page: this.result.current_page,
            loading:false,
            searchTarget:this.old.searchTarget
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
        BookMarkContainer
    },
    methods: {
        // 検索用
        search(){
            this.loading     = true
            this.$inertia.get('/BookMark/Search' ,{
                page :1,
                keyword : this.$refs.SearchField.serveKeywordToParent(),
                tagList : this.$refs.tagDialog.serveCheckedTagListToParent(),
                searchTarget:this.searchTarget,
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
                searchTarget:this.old.searchTarget,
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
