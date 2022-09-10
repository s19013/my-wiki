<template>
    <div class="flatLongButton" :class=haveIcon>
        <button
            type="button"
            @click.stop="clickTrigger"
            :style=[backgroundColorComp,backgroundColorBrightnessComp,backgroundColorDarknessComp,textColorComp,textColorBrightnessComp,textColorDarknessComp]>
            <v-icon>{{icon}}</v-icon>
            <p>{{text}}</p>
        </button>
    </div>
</template>

<script>
export default{
    props:{
        text:{
            type:String,
            default:null
        },
        icon:{
            type:String,
            default:null
        },
        backgroundColor:{
            type:Object,
            default:[0,0,85,1]//hsla型
        },
        textColor:{
            type:Object,
            default:[0,0,0,1]//hsla型
        },
    },
    methods: {
        clickTrigger(){this.$emit('clickTrigger');},
    },
    computed: {
        // アイコンがあるかどうか
        haveIcon(){
            if (this.icon !== null) {return "haveIconDisplay" }
            // else {return "noIconDisplay" }
        },
        // 文字色
        // 基礎
        textColorComp() {
            return {
                'color-h':this.textColor[0],
                'color-s':this.textColor[1] + "%",
                'color-l':this.textColor[2] + "%",
                'color-a':this.textColor[3],
            }
        },
        textColorBrightnessComp(){
            return {
                'color-brightness-s':this.textColor[1] - 10 + "%",
                'color-brightness-l':this.textColor[2] + 10 + "%"
            }
        },
        textColorDarknessComp(){
            return {
                'color-darkness-l':this.textColor[2] - 20 + "%"
            }
        },
        // 背景色
        // 基礎
        backgroundColorComp(){
            return {
                '--background-color-h':this.backgroundColor[0],
                '--background-color-s':this.backgroundColor[1] + "%",
                '--background-color-l':this.backgroundColor[2] + "%",
                '--background-color-a':this.backgroundColor[3],
            }
        },
        backgroundColorBrightnessComp(){
            return {
                '--background-color-brightness-s':this.backgroundColor[1] - 10 + "%",
                '--background-color-brightness-l':this.backgroundColor[2] + 10 + "%"
            }
        },
        backgroundColorDarknessComp(){
            return {
                '--background-color-darkness-l':this.backgroundColor[2] - 20 + "%"
            }
        }
    },
}
</script>

<style lang="scss" scoped>
.flatLongButton{
    font-size :1rem;
    color:hsla(
        var(--color-h),
        var(--color-s),
        var(--color-l),
        var(--color-a),
    );
    button{
        border-radius: 5px;
        background-color: hsla(
            var(--background-color-h),
            var(--background-color-s),
            var(--background-color-l),
            var(--background-color-a)
        );
        width: 100%;
        padding:0.4rem 0;
        transition: .1s;
        p{font-weight: bold;}
    }
    :hover {
        background-color: hsla(
            var(--background-color-h),
            var(--background-color-brightness-s),
            var(--background-color-brightness-l),
            var(--background-color-a)
        );
    }
    :active {
        background-color: hsla(
            var(--background-color-h),
            var(--background-color-brightness-s),
            var(--background-color-darkness-l),
            var(--background-color-a)
        );
    }
}

//アイコンがある時ようの表示の仕方
.haveIconDisplay{
    button{
        display: grid;
        grid-template-columns: 0.8fr 1fr  2fr 0.8fr;
        i{
            margin: auto;
            grid-column: 2/3;
        }
        p{
            margin: auto;
            grid-column: 3/4;
        }
    }
}

@media (max-width: 600px){
    .haveIconDisplay{
        button{
            display: grid;
            grid-template-columns: 1fr  2fr ;
            i{
                margin: auto;
                grid-column: 1/2;
            }
            p{
                margin: auto;
                grid-column: 2/3;
            }
        }
    }
}

</style>
