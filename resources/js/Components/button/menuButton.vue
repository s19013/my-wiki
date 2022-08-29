<template>
    <div class="menuButton"
    :style="backgroundColorComp">
        <Link :href="path" :method="method">
            <p :style="textColorComp"><v-icon>{{icon}}</v-icon></p>
            <h3 :style="textColorComp"> {{text}} </h3>
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
        a{
            display: grid;
            grid-template-columns:2fr 1fr 4fr 2fr;
            cursor: pointer;
            color : var(--color);
            text-decoration: none;
        }
        p{
            font-size :1.4rem;
            color:var(--color);
            grid-column: 2/3;
            margin     : auto;
        }

        h3{
            color:var(--color);
            text-align :center;
            grid-column: 3/4;
            margin     : auto;
        }
    }
</style>
