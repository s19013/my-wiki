<template>
    <div class="navMenu">
        <transition name="slide">
            <nav v-show="show">

                <button class="closeButton" @click.stop = "show = !show">
                    <v-icon> mdi-close-box</v-icon>
                    <p>閉じる</p>
                </button>
                <div>
                    <menuLabel
                        backgroundColor='#1a81c1'
                        textColor='#fafafa'
                        text="記事"
                        icon="mdi-note"
                    />


                    <menuButton
                        text="新規作成"
                        icon="mdi-plus"
                        :path="route('CreateArticle')"
                    />

                    <menuButton
                        text="検索"
                        icon="mdi-magnify"
                        :path="route('SearchArticle')"
                    />

                    <h2></h2>

                    <menuLabel
                        backgroundColor='#4015a6'
                        textColor='#fafafa'
                        text="ブックマーク"
                        icon="mdi-bookmark"
                    />

                    <menuButton
                        text="新規作成"
                        icon="mdi-plus"
                        :path="route('CreateBookMark')"
                    />

                    <menuButton
                        text="検索"
                        icon="mdi-magnify"
                        :path="route('SearchBookMark')"
                    />

                    <menuButton
                        class="logout"
                        textColor="#f0f8ff"
                        backgroundColor="#a80000"
                        text="ログアウト"
                        icon="mdi-logout"
                        :path="route('logout')"
                        method="post"
                    />

                </div>
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
    .logout {
        position: absolute;
        top     :90vh;
        width   : 100%;
        background-color:#830606;
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
