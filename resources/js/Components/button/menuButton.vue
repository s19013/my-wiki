<template>
    <div class="menuButton"
    :style=[hBackgroundColorComp,sBackgroundColorComp,lBackgroundColorComp,lightBackgroundColorComp,textColorComp]>
        <Link :href="path" :method="method">
            <!-- <v-icon>{{icon}}</v-icon>
            <p> {{text}} </p> -->
            {{backgroundColorLComp}}
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
        backgroundColor:{default:[0,0,83]},//hsl型
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
        hBackgroundColorComp(){
            return {
                '--background-color-h':this.backgroundColor[0]
            }
        },
        sBackgroundColorComp(){
            return {
                '--background-color-s':this.backgroundColor[1] + "%"
            }
        },
        lBackgroundColorComp(){
            return {
                '--background-color-l':this.backgroundColor[2] + "%"
            }
        },
        lightBackgroundColorComp(){
            if (Number(this.backgroundColor[2])-20 > 0) {
                return { '--background-color-l':this.backgroundColor[2] + "%"}
            } else {return { '--background-color-l': 0}}
        }
    }
}
</script>

<style lang="scss" scoped>
    .menuButton{
        background-color: hsl(var(--background-color-h),var(--background-color-s),var(--background-color-l));
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
