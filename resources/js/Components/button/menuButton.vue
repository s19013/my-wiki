<template>
    <div class="menuButton"
    :style=[backgroundColorComp,textColorComp]>
        <Link :href="path" :method="method">
            <v-icon>{{icon}}</v-icon>
            <p> {{text}} </p>
        </Link>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
export default{
    props:{
        text:{
            type   :String,
            default:null
        },
        icon:{},
        path:{},
        backgroundColor:{default:"#ffffff"},
        textColor:{default:"#000000"},
        method:{
            type   :String,
            default:"get"
        }
    },
    components:{
        Link
    },
    computed: {
        textColorComp() {
          return {'--color' : this.textColor,}
        },
        backgroundColorComp(){
            return {'--background-color':this.backgroundColor}
        }
    }
}
</script>

<style lang="scss" scoped>
    .menuButton{
        background-color: var(--background-color);
        margin-bottom:0.8rem;
        font-size :1.3rem;
        color:var(--color);
        a{
            display: grid;
            grid-template-columns:2fr 1fr 4fr 2fr;
            cursor: pointer;
            color : var(--color);
            text-decoration: none;
        }
        i{
            grid-column: 2/3;
            margin     : auto;
        }
        p{
            font-weight: bold;
            text-align :center;
            grid-column: 3/4;
            margin     : auto;
        }
    }
@media (max-width: 600px){
    .menuButton{
        padding: 0.5rem 0;
    }
}
</style>
