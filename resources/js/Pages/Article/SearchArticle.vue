<template>
    <BaseLayout title="記事検索" pageTitle="記事検索">
        <v-container>

            <SearchField
                ref = "SearchField"
                searchLabel   ="記事検索"
                :loadingFlag  ="loading"
                @triggerSearch="searchArticle"
                >
            </SearchField>

            <TagDialog
                ref="tagDialog"
                class="mb-10"
                text = "検索するタグ"
                :searchOnly="true"/>

            <details>
                <summary >検索対象</summary>
                <input type="radio" id="option1" value="title" v-model="searchTarget" />
                <label for="option1" class="me-6">タイトルのみ</label>

                <input type="radio" id="option2" value="body" v-model="searchTarget" />
                <label for="option2" class="me-6">本文のみ(低速)</label>

                <!-- <input type="radio" id="option3" value="titleAndBody" v-model="searchTarget" />
                <label for="option3">タイトルまたは本文(低速)</label> -->
            </details>

            <!-- loadingアニメ -->
            <loading v-show="loading"></loading>

            <template v-for="article of articleList" :key="article.id">
                <ArticleContainer
                    v-if="!loading"
                    :article="article"
                />
            </template>
        <v-pagination
            v-model="currentPage"
            :length="pageCount"
        ></v-pagination>
        </v-container>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'
import TagDialog from '@/Components/dialog/TagDialog.vue';
import loading from '@/Components/loading/loading.vue'
import SearchField from '@/Components/SearchField.vue';
import ArticleContainer from '@/Components/contents/ArticleContainer.vue';

export default{
    data() {
        return {
            articleList:null,
            currentPage: 1,
            pageCount:1,
            loading:false,
            searchTarget:"title"
        }
    },
    components:{
        BaseLayout,
        TagDialog,
        loading,
        SearchField,
        ArticleContainer
    },
    methods: {
        // 検索用
        async searchArticle(){
            this.loading = true
            this.currentPage = 1 //検索するのでリセットする
            await axios.post('/api/article/search',{
                currentPage    :this.currentPage,
                articleToSearch:this.$refs.SearchField.serveKeywordToParent(),
                tagList     : this.$refs.tagDialog.serveCheckedTagListToParent(),
                searchTarget:this.searchTarget
            })
            .then((res) =>{
                this.pageCount= res.data.pageCount
                this.articleList = res.data.articleList
            })
            .catch((error) => {console.log(error);})
            this.loading = false
        },
        // ページめくり
        async pagination(){
            this.loading = true
            await axios.post('/api/article/search',{
                currentPage:this.currentPage,
                articleToSearch:this.articleToSearch,
                tagList : this.$refs.tagDialog.serveCheckedTagListToParent(),
                searchTarget:this.searchTarget
            })
            .then((res) =>{
                this.articleList = res.data.articleList
                this.loading = false
            })
            .catch((error) => {
                console.log(error);
                this.loading = false
            })
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
        this.searchArticle();
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
