<template>
    <BaseLayout title="記事検索" pageTitle="記事検索">
        <v-container>
            <template v-for="article of articleList" :key="article.id">
                <Link :href="'/ViewArticle/' + article.id">
                    <div class ="article ">
                        <h2>{{article.title}}</h2>
                    </div>
                </Link>
            </template>
        </v-container>
        <v-pagination
            v-model="pagination.current_page"
            :length="pagination.lastPage"
            :total-visible="5"
        ></v-pagination>
    <!-- @input="getAricle" -->
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'
import { InertiaLink, InertiaHead } from '@inertiajs/inertia-vue3'
import { Link } from '@inertiajs/inertia-vue3';

export default{
    data() {
        return {
            articleList:null,
            pagination:{
                current_page: 1,
                lastPage:1,
            }
        }
    },
    components:{
        BaseLayout,
        InertiaLink,
        Link,
    },
    methods: {
        async getAricle(){
            await axios.get('/api/article/getUserAllArticle',{
                params: {
                    page: this.current_page,	// /api/pref?page=[page]の形
                },
            })
            .then((res)=>{
                console.log(res);
                this.pagination.current_page = res.data.current_page
                this.pagination.lastPage= res.data.last_page
                this.articleList = res.data.data
            })
        },
    },
    watch: {
    // 厳密にはページネーションのボタン類を押すとpagination.current_pageが変化するのでそれをwatch
    // ページネーションのボタン類を押した場合の処理
        'pagination.current_page':function(newValue,oldValue){
            this.getAricle();
        }
    },
    mounted() {
        this.getAricle()
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
</style>
