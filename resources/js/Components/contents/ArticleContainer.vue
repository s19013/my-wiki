<template>
    <div class ="content">
        <DateLabel :createdAt="article.created_at" :updatedAt="article.updated_at"/>
        <div class="elements">
            <Link :href="'/Article/View/' + article.id">
                <h2>{{article.title}}</h2>
            </Link>
            <Link :href="'/Article/Edit/' + article.id">
                <v-btn color="submit" elevation="2">
                    <p>{{ messages.button }}</p>
                </v-btn>
            </Link>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
import DateLabel from '@/Components/DateLabel.vue';
export default{
    data() {
        return {
            japanese:{
                button:"編集"
            },
            messages:{
                button:"Edit"
            }
        }
    },
    components:{
        Link,
        DateLabel
    },
    props:{article:{type:Object}},
    mounted(){
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
    },
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
