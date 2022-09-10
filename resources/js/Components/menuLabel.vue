<template>
    <div class="menuLabel"
    :style=[backgroundColorComp,textColorComp]>
        <v-icon>{{icon}}</v-icon>
        <p>{{text}}</p>
    </div>
</template>

<script>
export default{
    props:{
        text:{
            type   :String,
            default:null
        },
        icon:{},
        backgroundColor:{default:"#ffffff"},
        textColor:{default:"#000000"}
    },
    computed: {
    // CSS変数を定義して、CSS側に渡したいVueコンポーネントの値を指定する
    // ※今回はpropsで受け取った値
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
    .menuLabel{
        display: grid;
        grid-template-columns:2fr 1fr 4fr 2fr;
        background-color: var(--background-color);
        cursor : default;
        padding:0.5rem;
        font-size :1.8rem;
        color:var(--color);
        i{
            grid-column: 2/3;
            margin     : auto;
        }
        p{
            font-weight: bold;
            text-align :center;
            grid-column: 3/4;
        }
    }
</style>
