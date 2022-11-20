<template>
    <div class ="content">
        <DateLabel :createdAt="article.created_at" :updatedAt="article.updated_at"/>
        <div class="elements">
            <h2>{{article.title}}</h2>
            <Link :href="'/Article/View/' + article.id">
                <v-btn color="submit" elevation="2">
                    閲覧 編集
                </v-btn>
            </Link>
        </div>
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

<style scope lang="scss">
.DateLabel{ justify-content: flex-end; }
.elements{
    display: grid;
    grid-template-columns:10fr 1fr;
    gap:0.5rem;
    background-color: #e1e1e1;
    border:black solid 1px;
    padding: 5px;
    h2{
        margin: auto 0;
        grid-column: 1/2;
        word-break   :break-word;
        overflow-wrap:normal;
    }
    button{
        width: 100%;
        grid-column: 2/3;
    }
    a{
        text-decoration: none;
        color: black;
    }
}

</style>
