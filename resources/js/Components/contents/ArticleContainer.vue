<template>
    <div class ="content">
        <div class="others">
            <p><span>{{ messages.count }}</span>:{{ article.count }}</p>
            <DateLabel :createdAt="article.created_at" :updatedAt="article.updated_at"/>
        </div>
        <div class="elements">
            <Link :href="'/Article/View/' + article.id">
                <h3>{{article.title}}</h3>
            </Link>
            <Link :href="'/Article/Edit/' + article.id">
                <v-btn color="submit" elevation="2" size="small">
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
                button:"編集",
                count:"閲覧数"
            },
            messages:{
                button:"Edit",
                count:"count"
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
.others{
    span{font-weight: bold;}
    p{
        font-size: 0.8rem;
        text-align:right
    }
    .DateLabel{justify-content: flex-end;}
}

@media (min-width: 440px){
    .others{
        display: flex;
        justify-content: flex-end;
        word-break   :break-word;
        overflow-wrap:normal;
        gap: 0.6rem;
    }
}

.elements{
    display: grid;
    grid-template-columns:10fr 1fr;
    gap:0.5rem;
    background-color: #e1e1e1;
    border:black solid 1px;
    padding: 5px;
    h3{
        @media (min-width: 420px){font-size: 1.3rem;}
        font-size: 1.6rem;
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
