<template>
    <div>
        <div class="originalHead">
            <h2>{{pageTitle}}</h2>
            <v-btn @click.stop = "show = !show"> <v-icon> mdi-view-headline </v-icon> メニュー</v-btn>
        </div>
        <transition name="slide">
            <nav v-show="show">
                <h4 class="navButton closeButton" @click.stop = "show = !show"> <v-icon> mdi-close-box</v-icon>閉じる</h4>
                <div>
                    <h2 class="titleAticle navButton"> <v-icon>mdi-note</v-icon> 記事 </h2>

                    <Link :href="route('CreateArticle')">
                        <h3 class="navButton">
                            <v-icon>mdi-plus</v-icon>
                            新規作成
                        </h3>
                    </Link>

                    <Link :href="route('SearchArticle')">
                        <h3 class="navButton">
                            <v-icon>mdi-magnify</v-icon>
                            検索
                        </h3>
                    </Link>

                    <h2></h2>

                    <h2 class="titleBookMark navButton"> <v-icon>mdi-bookmark</v-icon> ブックマーク </h2>

                    <Link :href="route('CreateBookMark')">
                        <h3 class="navButton">
                            <v-icon>mdi-plus</v-icon>
                            新規作成
                        </h3>
                    </Link>

                    <Link :href="route('SearchBookMark')">
                        <h3 class="navButton">
                            <v-icon>mdi-magnify</v-icon>
                            検索
                        </h3>
                    </Link>

                    <Link :href="route('logout')" method="post">
                        <h3 class="navButton logout">
                            <v-icon>mdi-logout</v-icon>
                            Log Out
                        </h3>
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
    props:['pageTitle']
}

</script>

<style lang="scss" scoped>
.originalHead{
    background-color: rgb(127, 255, 174);
    height        : 6vh;
    margin-bottom :10px;
    display       : grid;
    grid-template-columns:1fr 5fr 1fr;
    h2{
        grid-column: 2/3;
        text-align :center
    }
    button{
        margin: auto;
        height: 80%;
        width : 90%;
        grid-column: 3/4;
    }
}

/* メニュー */
nav {
    background: rgb(234, 234, 234);
    z-index : 10;//これで10前のレイヤーへ
    height  : 100vh;
    width   : 40vw;
    position: fixed; /* ウィンドウを基準に画面に固定 */
    right   : 0;// 強制的に右端に置く
    top     :0;//ボタンと被らないように頭の位置を下げる
    a{
        cursor: pointer;
        color : rgb(0, 0, 0);
        text-decoration: none;
    }
    .navButton{
        display: grid;
        grid-template-columns:2fr 1fr 4fr 2fr;
    }
    i{
        grid-column: 2/3;
        margin     : 0 0 0 auto;
    }

    h2,h3,h4{
        text-align :center;
        grid-column: 3/4;
    }

    h2 {
        color  : rgb(250, 250, 250);
        cursor : default;
        padding:10px;
    }
    h3 {
        background-color: rgb(212, 212, 212);
        padding:5px 0;
        margin :0 0 10px 0;
    }
    h4 {
        background-color: rgb(212, 212, 212);
        padding:5px 0;
        margin :0 0 10px 0;
        cursor : pointer;
    }
    .titleAticle   { background-color:rgb(26, 130, 195) ;}
    .titleBookMark { background-color:rgb(64, 21, 166) ;}
    .logout {
        position: absolute;
        top     :85vh;
        width   : 100%;
        background-color:rgb(131, 6, 6) ;
        color:aliceblue
    }
}

// アニメ
// on
.slide-enter-active { transition: all .4s ease; }
.slide-enter-from { transform: translateX(100%); }

//off
.slide-leave-active { transition: all .4s ease; }
.slide-leave-to{ transform: translateX(100%); }
</style>
