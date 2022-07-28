<template>
    <div class="head">
        <h2>{{title}}</h2>
        <v-btn @click="show = !show"> <v-icon> mdi-view-headline </v-icon> メニュー</v-btn>
        <transition name="slide">
            <nav v-if="show">
                <p>test</p>
            </nav>
        </transition>
    </div>
</template>

<script>
import { InertiaLink } from '@inertiajs/inertia-vue3'
export default{
    data() {
        return {
            show:false
        }
    },
    components:{
        InertiaLink,
    },
    props:['title']
}

</script>

<style lang="scss" scoped>
.head{
    background-color: rgb(127, 255, 174);
    height: 3vmax;
    margin-bottom:10px;
    display: grid;
    grid-template-columns:1fr 5fr 1fr;
    h2{
        grid-column: 2/3;
        text-align:center
    }
    button{
        margin: auto;
        height: 80%;
        width: 90%;
        grid-column: 3/4;
    }
}
    // nav {
    //     background: #0bd;
    //     position: absolute;
    //     z-index: 10;//これで10前のレイヤーへ
    //     right: 0; //初期の位置
    //     top:6vmin;
    //     overflow-x: hidden;
    //     text-align: center;
    //     width: 0;
    //     height: 100vh;
    //     transition: .2s; /* アニメ */

/* メニュー */
nav {
    background: rgb(234, 234, 234);
    z-index: 10;//これで10前のレイヤーへ
    height: 100vh;
    width: 40vw;
    // 強制的に右端に置く
    position: absolute;
    right: 0;
    //ボタンと被らないように頭の位置を下げる
    top:3vmax;
    }

// アニメ
// on
.slide-enter-active { transition: all .4s ease; }
.slide-enter-from { transform: translateX(100%); }

//off
.slide-leave-active { transition: all .4s ease; }
.slide-leave-to{ transform: translateX(100%); }
</style>
