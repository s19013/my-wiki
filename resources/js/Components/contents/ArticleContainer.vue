<template>
    <div class ="content" v-show="!loading">
        <h2>{{title}}</h2>
        <Link :href="'/Article/View/' + id">
            <v-btn color="submit" elevation="2">
                閲覧 編集
            </v-btn>
        </Link>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
export default{
    components:{Link},
    props:['title','id','loading']
}
</script>

<style scoped lang="scss">
.content{
    display: grid;
    grid-template-columns:10fr 1fr;
    background-color: #e1e1e1;
    border:black solid 1px;
    padding: 5px;
    button{width: 100%;}
    h2{grid-column: 1/2;}
    a{
        text-decoration: none;
        color: black;
    }
}

</style>
