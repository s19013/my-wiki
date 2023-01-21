<template>
    <div class="menuButton" :style=[textColorComp,backgroundColorComp]>
        <Link :href="path" :method="method">
            <v-btn flat :rounded="0">
                <v-icon v-if="icon !== null">{{icon}}</v-icon>
                <p>{{text}}</p>
            </v-btn>
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
        icon:{
            type   :String,
            default:null
        },
        path:{
            type   :String,
            default:null
        },
        backgroundColor:{
            type:String,
            default:"#d9d9d9"//#16進数 型
        },
        textColor:{
            type:String,
            default:"#000000"//#16進数 型
        },
        method:{
            type   :String,
            default:"get"
        }
    },
    components:{
        Link,
    },
    computed: {
        textColorComp() {
          return {'--color' : this.textColor,}
        },
        backgroundColorComp(){
            return {
                '--background-color':this.backgroundColor,
            }
        },
    }
}
</script>

<style lang="scss">
.menuButton{
    a{text-decoration: none;}
    button{
        width: 100%;
        background-color: var(--background-color);
        color:var(--color);
    }
    .v-btn{display: initial}
    .v-btn__content{
        display: grid;
        grid-template-columns:2fr 1fr 4fr 2fr;
    }
    i{
        grid-column: 2/3;
        margin     : auto;
    }
    p{
        text-align: center;
        grid-column: 3/4;
        margin: auto;
    }
}
</style>
