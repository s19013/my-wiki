<template>
    <BaseLayout title="検索画面" pageTitle="検索画面">
        <v-container>
            <template v-for="article of bookMarkList" :key="article.id">
                    <div class ="article ">
                        <!-- 別タブで開くようにする -->
                        <a :href="article.body"><h2>{{article.title}}</h2></a>
                    </div>
            </template>
        </v-container>
        <v-pagination
            v-model="pagination.current_page"
            :length="pagination.lastPage"
            :total-visible="5"
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
            bookMarkList:null,
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
        async getBookMark(){
            await axios.get('/api/bookmark/getUserAllBookMark',{
                params: {
                    page: this.current_page,	// /api/pref?page=[page]の形
                },
            })
            .then((res)=>{
                console.log(res);
                this.pagination.current_page = res.data.current_page
                this.pagination.lastPage= res.data.last_page
                this.bookMarkList = res.data.data
            })
        },
    },
    watch: {
    // 厳密にはページネーションのボタン類を押すとpagination.current_pageが変化するのでそれをwatch
    // ページネーションのボタン類を押した場合の処理
        'pagination.current_page':function(newValue,oldValue){
            this.getBookMark();
        }
    },
    mounted() {
        this.getBookMark()
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
