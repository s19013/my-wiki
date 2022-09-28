<template>
    <div class ="content">
        <h2>{{article.title}}</h2>
        <DateLabel :createdAt="article.created_at" :updatedAt="article.updated_at" size="0.1rem"/>
        <Link :href="'/Article/View/' + article.id">
            <v-btn color="submit" elevation="2">
                閲覧 編集
            </v-btn>
        </Link>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
import DateLabel from '@/Components/DateLabel.vue';
export default{
    components:{
        Link,
        DateLabel
    },
    props:{article:{type:Object}}
}
</script>

<style scoped lang="scss">
.content{
    display: grid;
    grid-template-rows:1fr,10fr;
    grid-template-columns:10fr 1fr;
    background-color: #e1e1e1;
    border:black solid 1px;
    padding: 5px;
    button{width: 100%;}
    .DateLabel{
        grid-row: 1/2;
        grid-column: 1/3;
    }
    h2{
        grid-row: 2/3;
        grid-column: 1/2;
    }
    a{
        grid-row: 2/3;
        grid-column: 2/3;
        text-decoration: none;
        color: black;
    }
}

</style>
