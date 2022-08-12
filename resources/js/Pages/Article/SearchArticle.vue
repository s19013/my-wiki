<template>
    <BaseLayout title="記事検索" pageTitle="記事検索">
        <v-container>
            <div class="searchArea">
                <v-text-field
                    v-model="articleToSearch"
                    label="検索"
                    clearable
                ></v-text-field>
                <v-btn color="submit"
                    elevation="2"
                    :disabled = "loading"
                    @click="searchArticle()">
                    <v-icon>mdi-magnify</v-icon>
                    検索
                </v-btn>
            </div>

            <details>
                <summary >検索対象</summary>
                <input type="radio" id="option1" value="title" v-model="searchTarget" />
                <label for="option1" class="me-6">タイトルのみ</label>

                <input type="radio" id="option2" value="body" v-model="searchTarget" />
                <label for="option2" class="me-6">本文のみ(低速)</label>

                <!-- <input type="radio" id="option3" value="titleAndBody" v-model="searchTarget" />
                <label for="option3">タイトルまたは本文(低速)</label> -->
            </details>

            <TagDialog ref="tagDialog" class="w-50 mb-10" :searchOnly="true"></TagDialog>

            <!-- loadingアニメ -->
            <loading v-show="loading"></loading>

            <template v-for="article of articleList" :key="article.id">
                    <div class ="contentsContainer" v-show="!loading">
                        <h2>{{article.title}}</h2>
                        <Link :href="'/Article/View/' + article.id">
                            <v-btn color="submit" elevation="2">
                                閲覧 編集
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
import { InertiaLink, InertiaHead } from '@inertiajs/inertia-vue3'
import { Link } from '@inertiajs/inertia-vue3';
import TagDialog from '@/Components/dialog/TagDialog.vue';
import loading from '@/Components/loading/loading.vue'

export default{
    data() {
        return {
            articleToSearch:'',
            articleList:null,
            currentPage: 1,
            pageCount:1,
            loading:false,
            searchTarget:"title"
        }
    },
    components:{
        BaseLayout,
        InertiaLink,
        Link,
        TagDialog,
        loading,
    },
    methods: {
        // 検索用
        async searchArticle(){
            this.loading = true
            this.currentPage = 1 //検索するのでリセットする
            await axios.post('/api/article/search',{
                currentPage:this.currentPage,
                articleToSearch:this.articleToSearch,
                tagList : this.$refs.tagDialog.serveCheckedTagListToParent(),
                searchTarget:this.searchTarget
            })
            .then((res) =>{
                this.pageCount= res.data.pageCount
                this.articleList = res.data.articleList
                this.loading = false
            })
            .catch((error) => {
                console.log(error);
                this.loading = false
            })
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
details{
    margin-bottom: 15px;
    input,label,summary{ cursor: pointer; }
}
</style>
