<template>
    <BaseLayout title="検索画面" pageTitle="検索画面">
        <v-container>
            <div class="searchArea">
                <v-text-field
                    v-model="bookMarkToSearch"
                    label="検索"
                    clearable
                ></v-text-field>
                <v-btn color="submit"
                    elevation="2"
                    :disabled = "bookMarkSerchLoading"
                    @click="search()">
                    <v-icon>mdi-magnify</v-icon>
                    検索
                </v-btn>
            </div>
            <template v-for="bookMark of bookMarkList" :key="bookMark.id">
                <div class ="article ">
                    <!-- 別タブで開くようにする -->
                    <a :href="bookMark.url"><h2>{{bookMark.title}}</h2></a>
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

export default{
    data() {
        return {
            bookMarkToSearch:'',
            bookMarkList:null,
            currentPage: 1,
            pageCount:1,
            bookMarkSerchLoading:false,
        }
    },
    components:{
        BaseLayout,
        InertiaLink,
        Link,
    },
    methods: {
        // 検索用
        async search(){
            this.currentPage = 1 //検索するのでリセットする
            await axios.post('/api/bookmark/search',{
                currentPage:this.currentPage,
                bookMarkToSearch:this.bookMarkToSearch
            })
            .then((res) =>{
                this.pageCount= res.data.pageCount
                this.bookMarkList = res.data.bookMarkList
            })
        },
        async pagination(){
            await axios.post('/api/bookmark/search',{
                currentPage:this.currentPage,
                bookMarkToSearch:this.bookMarkToSearch
            })
            .then((res) =>{
                this.bookMarkList = res.data.bookMarkList
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
        this.search()
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
