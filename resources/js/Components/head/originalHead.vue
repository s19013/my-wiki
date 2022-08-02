<template>
    <div class="originalHead">
        <h2>{{title}}</h2>
        <v-btn @click.stop = "show = !show"> <v-icon> mdi-view-headline </v-icon> メニュー</v-btn>
        <transition name="slide">
            <nav v-show="show">
                <div>
                    <Link :href="route('CreateArticle')">
                        <h2 class="createAticle navButton"  >
                            <v-icon>mdi-note-plus</v-icon>
                            新規記事作成
                        </h2>
                    </Link>
                    <Link :href="route('CreateBookMark')">
                        <h2 class="createBookMark navButton">
                            <v-icon>mdi-bookmark-plus</v-icon>
                            新規ブックマーク作成
                        </h2>
                    </Link>
                </div>
            </nav>
        </transition>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
export default{
    data() {
        return {
            show:false
        }
    },
    components:{
        Link,
    },
    props:['title']
}

</script>

<style lang="scss" scoped>
.originalHead{
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
    a{
        margin: 0 5px ;
        text-decoration: none;
        color: rgb(248, 248, 248);
    }
    .navButton{
        display: grid;
        grid-template-columns:1fr 1fr 10fr 1fr;
    }
    i{grid-column: 2/3;}
    h2 {
        grid-column: 3/4;
        padding:10px;
    }
    .createAticle   { background-color:rgb(26, 130, 195) ;}
    .createBookMark { background-color:rgb(77, 26, 195) ;}
}

// アニメ
// on
.slide-enter-active { transition: all .4s ease; }
.slide-enter-from { transform: translateX(100%); }

//off
.slide-leave-active { transition: all .4s ease; }
.slide-leave-to{ transform: translateX(100%); }
</style>
