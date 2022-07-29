<template>
    <BaseLayout title="検索画面" pageTitle="検索画面">
        <v-container>
            <template v-for="article of articleList" :key="article.id">
                <Link :href="'/ViewArticle/' + article.id">
                    <div class ="article ">
                        <h2>{{article.title}}</h2>
                    </div>
                </Link>
            </template>
        </v-container>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'
import { InertiaLink, InertiaHead } from '@inertiajs/inertia-vue3'
import { Link } from '@inertiajs/inertia-vue3';

export default{
    data() {
        return {
            articleList:[]
        }
    },
    components:{
        BaseLayout,
        InertiaLink,
        Link,
    },
    methods: {
        async getAricle(){
            await axios.post('/api/article/getUserAllArticle',{
                userId:this.$attrs.auth.user.id,
            })
            .then((res)=>{
                console.log(res);
                for (const article of res.data) {
                    this.articleList.push({
                        id:article.id,
                        title:article.title,
                        body:article.body
                    })
                }
            })
        },
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
</style>
