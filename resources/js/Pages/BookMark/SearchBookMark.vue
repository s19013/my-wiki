<template>
    <BaseLayout title="ブックマーク検索" pageTitle="ブックマーク検索">
        <v-container>
            <SearchField
                ref        = "SearchField"
                searchLabel="ブックマーク検索"
                :loadingFlag  ="loading"
                @triggerSearch="search"
                />

            <TagDialog
                ref="tagDialog"
                class="mb-10"
                text = "検索するタグ"
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

            <template v-for="bookMark of bookMarkList" :key="bookMark.id">
                <BookMarkContainer
                    :title="bookMark.title"
                    :url  ="bookMark.url"
                    :id   ="bookMark.id"
                    :loading="loading"
                />
            </template>
            <v-pagination
                v-model="currentPage"
                :length="pageCount"
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
            bookMarkList :null,
            currentPage  : 1,
            pageCount    :1,
            loading      :false,
            searchTarget:"title"
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
        async search(){
            this.loading     = true
            this.currentPage = 1 //検索するのでリセットする
            await axios.post('/api/bookmark/search',{
                currentPage     :this.currentPage,
                bookMarkToSearch:this.$refs.SearchField.serveKeywordToParent(),
                tagList         : this.$refs.tagDialog.serveCheckedTagListToParent(),
                searchTarget:this.searchTarget
            })
            .then((res) =>{
                this.pageCount    = res.data.pageCount
                this.bookMarkList = res.data.bookMarkList
            })
            .catch((error) => {
                console.log(error);
            })
            this.loading = false
        },
        // ページめくり
        async pagination(){
            this.loading = true
            await axios.post('/api/bookmark/search',{
                currentPage     :this.currentPage,
                bookMarkToSearch:this.bookMarkToSearch,
                tagList         : this.$refs.tagDialog.serveCheckedTagListToParent(),
                searchTarget:this.searchTarget
            })
            .then((res) =>{
                this.bookMarkList = res.data.bookMarkList
            })
            .catch((error) => {
                console.log(error);
            })
            this.loading = false
        },
    },
    watch: {
    // 厳密にはページネーションのボタン類を押すとpagination.current_pageが変化するのでそれをwatch
    // ページネーションのボタン類を押した場合の処理
        currentPage:function(newValue,oldValue){
            this.pagination();
        }
    },
    mounted() {
        this.search()
    },
}
</script>

<style lang="scss" scoped>
.content{margin-bottom: 1.2rem;}
details{
    margin-bottom: 15px;
    input,label,summary{ cursor: pointer; }
}
</style>
