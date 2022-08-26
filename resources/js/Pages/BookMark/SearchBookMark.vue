<template>
    <BaseLayout title="ブックマーク検索" pageTitle="ブックマーク検索">
        <v-container>
            <SearchField
                ref        = "SearchField"
                searchLabel="タグ検索"
                :loadingFlag  ="loading"
                @triggerSearch="search"
                >
            </SearchField>

            <TagDialog ref="tagDialog" class="mb-10" :searchOnly="true"></TagDialog>

            <!-- loadingアニメ -->
            <loading v-show="loading"></loading>

            <template v-for="bookMark of bookMarkList" :key="bookMark.id">
                <div class ="contentsContainer" v-show="!loading">
                    <!-- 別タブで開くようにする -->
                    <a :href="bookMark.url" target="_blank" rel="noopener noreferrer">
                        <h2>
                            <v-icon>mdi-arrow-top-left-bold-box-outline</v-icon>
                            {{bookMark.title}}
                        </h2>
                    </a>
                    <Link :href="'/BookMark/Edit/' + bookMark.id">
                            <v-btn color="submit" elevation="2">
                                編集
                            </v-btn>
                    </Link>
                </div>
            </template>
        </v-container>
        <v-pagination
            v-model="currentPage"
            :length="pageCount"
            :total-visible="7"
        ></v-pagination>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'
import { Link } from '@inertiajs/inertia-vue3';
import TagDialog from '@/Components/dialog/TagDialog.vue';
import loading from '@/Components/loading/loading.vue';
import SearchField from '@/Components/SearchField.vue';

export default{
    data() {
        return {
            bookMarkList:null,
            currentPage : 1,
            pageCount   :1,
            loading     :false,
        }
    },
    components:{
        BaseLayout,
        Link,
        TagDialog,
        loading,
        SearchField
    },
    methods: {
        // 検索用
        async search(){
            this.loading     = true
            this.currentPage = 1 //検索するのでリセットする
            await axios.post('/api/bookmark/search',{
                currentPage     :this.currentPage,
                bookMarkToSearch:this.$refs.SearchField.serveKeywordToParent(),
                tagList         : this.$refs.tagDialog.serveCheckedTagListToParent()
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
                tagList         : this.$refs.tagDialog.serveCheckedTagListToParent()
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
</style>
