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
                    :disabled = "articleSerchLoading"
                    @click="searchArticle()">
                    <v-icon>mdi-magnify</v-icon>
                    検索
                </v-btn>
            </div>
            <template v-for="article of articleList" :key="article.id">
                <Link :href="'/Article/View/' + article.id">
                    <div class ="article">
                        <h2>{{article.title}}</h2>
                    </div>
                </Link>
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

export default{
    data() {
        return {
            articleToSearch:'',
            articleList:null,
            currentPage: 1,
            pageCount:1,
            articleSerchLoading:false,
        }
    },
    components:{
        BaseLayout,
        InertiaLink,
        Link,
    },
    methods: {
        // 検索用
        async searchArticle(){
            this.currentPage = 1 //検索するのでリセットする
            await axios.post('/api/article/search',{
                currentPage:this.currentPage,
                articleToSearch:this.articleToSearch
            })
            .then((res) =>{
                this.pageCount= res.data.pageCount
                this.articleList = res.data.articleList
            })
        },
        async pagination(){
            await axios.post('/api/article/search',{
                currentPage:this.currentPage,
                articleToSearch:this.articleToSearch
            })
            .then((res) =>{
                this.articleList = res.data.articleList
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
.article{
    border:black solid 1px;
    margin-bottom:20px;
    padding: 5px;
    cursor: pointer;
}
a{
    text-decoration: none;
    color: black;
}
.searchArea{
    display:grid;
    grid-template-columns:5fr 1fr;
    .v-input{
        grid-column: 1/2;
    }
    button{
        grid-column: 2/3;
        margin: 0 auto;
    }
}
</style>
