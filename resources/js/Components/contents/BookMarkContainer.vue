<template>
    <div class ="bookMarkContainer" v-show="!loading">
        <!-- 別タブで開くようにする -->
        <a :href="url" target="_blank" rel="noopener noreferrer">
            <h2>
                <v-icon>mdi-arrow-top-left-bold-box-outline</v-icon>
                {{title}}
            </h2>
        </a>
        <Link :href="'/BookMark/Edit/' + id">
            <v-btn color="submit" elevation="2">
                編集
            </v-btn>
        </Link>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
export default{
    components:{Link},
    props:['title','url','id','loading']
}
</script>

<style scoped lang="scss">
.bookMarkContainer{
    display: grid;
    grid-template-columns:10fr 1fr;
    background-color: #e1e1e1;
    border:black solid 1px;
    margin-bottom:20px;
    padding: 5px;
    button{width: 100%;}
    h2{grid-column: 1/2;}
    a{
        text-decoration: none;
        color: black;
    }
}
</style>
