<template>
    <div class="navMenu">
        <transition name="slide">
            <nav v-show="show">
                <ol>
                    <li>
                        <button class="closeButton" @click.stop = "show = !show">
                            <v-icon> mdi-close-box</v-icon>
                            <p>閉じる</p>
                        </button>
                    </li>

                    <li>
                        <menuLabel
                            backgroundColor='#1a81c1'
                            textColor='#fafafa'
                            text="記事"
                            icon="mdi-note"
                        />
                    </li>

                    <li>
                        <menuButton
                            text="新規作成"
                            icon="mdi-plus"
                            :path="route('CreateArticle')"
                        />
                    </li>

                    <li>
                        <menuButton
                            text="検索"
                            icon="mdi-magnify"
                            :path="route('SearchArticle')"
                        />
                    </li>

                    <li>
                        <menuLabel
                            backgroundColor='#4015a6'
                            textColor='#fafafa'
                            text="ブックマーク"
                            icon="mdi-bookmark"
                        />
                    </li>

                    <li>
                        <menuButton
                            text="新規作成"
                            icon="mdi-plus"
                            :path="route('CreateBookMark')"
                        />
                    </li>

                    <li>
                        <menuButton
                            text="検索"
                            icon="mdi-magnify"
                            :path="route('SearchBookMark')"
                        />
                    </li>

                    <li>
                        <menuButton
                            textColor="#f0f8ff"
                            backgroundColor="#a80000"
                            text="ログアウト"
                            icon="mdi-logout"
                            :path="route('logout')"
                            method="post"
                        />
                    </li>
                </ol>
            </nav>
        </transition>
    </div>
</template>

<script>
import menuLabel from '@/Components/menuLabel.vue';
import menuButton from '@/Components/button/menuButton.vue';

export default{
    data() {
        return {
            show:false
        }
    },
    components:{
        menuLabel,
        menuButton,
    },
}
</script>

<style scoped lang="scss">

/* メニュー */
nav {
    background: rgb(234, 234, 234);
    z-index : 10;//これで10前のレイヤーへ
    height  : 100vh;
    width   : 40vw;
    position: fixed; /* ウィンドウを基準に画面に固定 */
    right   : 0;// 強制的に右端に置く
    top     :0;//ボタンと被らないように頭の位置を下げる

    ol{
        height: 100%;
        list-style-type:none;
        display: grid;
        grid-template-rows:
        auto
        2fr
        auto
        auto
        1fr
        auto
        2fr
        auto
        auto
        1fr
        auto
        20fr
        auto
        ;
        li:nth-child(1){grid-row: 1/2; }

        li:nth-child(2){grid-row: 3/4; }
        li:nth-child(3){grid-row: 4/5; }
        li:nth-child(4){grid-row: 6/7; }

        li:nth-child(5){grid-row: 8/9; }
        li:nth-child(6){grid-row: 9/10; }
        li:nth-child(7){grid-row: 11/12; }

        li:nth-child(8){grid-row: 13/14; }
    }
    .closeButton{
        background-color: hsl(0, 0%, 83%);
        padding:5px 0;
        width: 100%;
        display: grid;
        grid-template-columns:2fr 1fr 4fr 2fr;
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
}

// アニメ
// on
.slide-enter-active { transition: all .4s ease; }
.slide-enter-from { transform: translateX(100%); }

//off
.slide-leave-active { transition: all .4s ease; }
.slide-leave-to{ transform: translateX(100%); }

@media (max-width: 960px){
    nav{width: 70%;}
}
@media (max-width: 600px){
    .originalHead{grid-template-columns:1.5fr 2fr 1.5fr 0.1fr;}
    nav{width: 100%;}
}
</style>
