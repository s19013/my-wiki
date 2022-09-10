<template>
    <div class="Button" :class=[sizeComp,shadowComp]>
        <button
            type="button"
            @click.stop="clickTrigger"
            :style=[backgroundColorComp,textColorComp,roundingCornersComp,shadowPropertyComp,shadowColorComp]>
            <v-icon v-if="icon !== null">{{icon}}</v-icon>
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
        size:{
            type:String,
            default:"normal"
        },
        //角を丸めるか
        haveRoundingCorners:{
            type:Boolean,
            default:false
        },
        cornerRadius:{
            type:String,
            default:"5px" // pxでも,remでも %でもok
        },
        //影をつけるか
        haveShadow:{
            type:Boolean,
            default:false
        },
        shadowProperty:{
            type:Array,
            default:["0","2px","3px","0px"]
        },
        shadowColor:{
            type:Array,
            default:[0,0,0,0.3]
        },
        backgroundColor:{
            type:Array,
            default:[0,0,98,1]//hsla型
        },
        textColor:{
            type:Array,
            default:[0,0,0,1]//hsla型
        },
    },
    methods: {
        clickTrigger(){this.$emit('clickTrigger');},
    },
    computed: {
        // アイコンがあるかどうか
        // haveIcon(){
        //     if (this.icon !== null) {return "haveIconDisplay" }
        // },
        //大きさ
        sizeComp(){
            // アイコンがあるかどうか
            if (this.icon !== null) {
                switch(this.size){
                    case "maximum" : return "haveIconMaximum"
                    break
                    case "large" : return  "haveIconLarge"
                    break
                    case "normal" : return "haveIconNormal"
                    break
                    case "small" : return  "haveIconSmall"
                    break
                }
            } else {
                switch(this.size){
                    case "maximum" : return "maximum"
                    break
                    case "large" : return  "large"
                    break
                    case "normal" : return "normal"
                    break
                    case "small" : return  "small"
                    break
                }
            }
        },
        //角を丸くするか
        roundingCornersComp(){
            if (this.haveRoundingCorners === true) {return {'--border-radius': this.cornerRadius}}
            else{ return {'--border-radius':"0px"}}
        },
        //影をつけるか
        shadowComp(){
            if (this.haveShadow) { return "haveShadow" }
        },
        shadowPropertyComp(){
            return {
                '--shadow-offset-x':this.shadowProperty[0],
                '--shadow-offset-y':this.shadowProperty[1],
                '--shadow-blur-radius'  :this.shadowProperty[2],
                '--shadow-spread-radius':this.shadowProperty[3],
            }
        },
        shadowColorComp(){
            return {
                '--shadow-color-h':this.shadowColor[0],
                '--shadow-color-s':this.shadowColor[1] + "%",
                '--shadow-color-l':this.shadowColor[2] + "%",
                '--shadow-color-a':this.shadowColor[3],
            }
        },
        // 文字色
        textColorComp() {
            return {
                '--color-h':this.textColor[0],
                '--color-s':this.textColor[1] + "%",
                '--color-l':this.textColor[2] + "%",
                '--color-a':this.textColor[3],
                '--color-brightness-s':this.textColor[1] + 10 + "%",
                '--color-brightness-l':this.textColor[2] + 15 + "%",
                '--color-darkness-s'  :this.textColor[1] - 20 + "%",
                '--color-darkness-l'  :this.textColor[2] - 20 + "%",
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
                '--background-color-brightness-s':this.backgroundColor[1] + 10 + "%",
                '--background-color-brightness-l':this.backgroundColor[2] + 15 + "%",
                '--background-color-darkness-s'  :this.backgroundColor[1] - 30 + "%",
                '--background-color-darkness-l'  :this.backgroundColor[2] - 30 + "%"
            }
        },
    },
}
</script>

<style lang="scss" scoped>
.Button{
    font-size :1rem;
    color:hsla(
        var(--color-h),
        var(--color-s),
        var(--color-l),
        var(--color-a),
    );
    button{
        border-radius: var(--border-radius);
        background-color: hsla(
            var(--background-color-h),
            var(--background-color-s),
            var(--background-color-l),
            var(--background-color-a)
        );
        transition: .1s;
        p{
            margin: auto;
            font-weight: bold;
        }
    }
    button:hover {
        background-color: hsla(
            var(--background-color-h),
            var(--background-color-brightness-s),
            var(--background-color-brightness-l),
            var(--background-color-a)
        );
    }
    button:active {
        background-color: hsla(
            var(--background-color-h),
            var(--background-color-darkness-s),
            var(--background-color-darkness-l),
            var(--background-color-a)
        );
        box-shadow:  none;
    }

}
//影表示
.haveShadow{
    button{
        box-shadow: var(--shadow-offset-x) var(--shadow-offset-y) var(--shadow-blur-radius) var(--shadow-spread-radius) hsla(var(--shadow-color-h),var(--shadow-color-s),var(--shadow-color-l),var(--shadow-color-a));
    }
}

// 表示やパディングなど
:is(.haveIconMaximum,.haveIconLarge,.haveIconNormal,.haveIconSmall) i{margin: auto;}
.haveIconMaximum,.haveIconLarge{
    button{
        display: grid;
        grid-template-columns: 0.8fr 1fr  2fr 0.8fr;
        i{ grid-column: 2/3; }
        p{grid-column: 3/4;}
    }
}


:is(.haveIconMaximum,.maximum) button{width: 100%;}
:is(.haveIconLarge,.large) button{width: 15rem;}

:is(.haveIconMaximum,.maximum,.haveIconLarge,.large) button{padding-top: 0.4rem; padding-bottom: 0.4rem;}
:is(.haveIconNormal,.normal) button{
    padding-top   : 0.4rem;
    padding-bottom: 0.4rem;
    padding-left  : 1rem;
    padding-right : 1rem;
}
:is(.haveIconSmall,.small) button{
    padding-left: 0.1rem;
    padding-right: 0.2rem;
}

:is(.haveIconNormal,.haveIconSmall) button{ display: flex; }
.haveIconNormal { button{gap: 10px;} }
.haveIconSmall  { button{gap: 2px ;} }

@media (max-width: 600px){
    .haveIconMaximum,.haveIconLarge{
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

