<template>
    <div class ="content">
        <!-- 別タブで開くようにする -->
        <DateLabel :createdAt="bookMark.created_at" :updatedAt="bookMark.updated_at" size="0.1rem"/>
        <a :href="url" target="_blank" rel="noopener noreferrer">
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

<script>
import { Link } from '@inertiajs/inertia-vue3';
import DateLabel from '@/Components/DateLabel.vue';
export default{
    components:{
        Link,
        DateLabel
    },
    props:{
        bookMark:{type:Object}
    }
}
</script>

<style scoped lang="scss">
.content{
    display: grid;
    grid-template-rows:1fr,10fr;
    grid-template-columns:10fr 1fr;
    background-color: #e1e1e1;
    border:black solid 1px;
    margin-bottom:20px;
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
