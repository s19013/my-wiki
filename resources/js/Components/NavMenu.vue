<template>
    <div class="navMenu">
        <transition name="slide">
            <nav v-show="show">
                <ol>
                    <li>
                        <button class="closeButton" @click.stop = "show = !show">
                            <v-icon> mdi-close-box</v-icon>
                            <p>{{ messages.close }}</p>
                        </button>
                    </li>

                    <li>
                        <menuLabel
                            backgroundColor='#1a81c1'
                            textColor='#fafafa'
                            :text="messages.article"
                            icon="mdi-note"
                        />
                    </li>

                    <li>
                        <menuButton
                            :text="messages.createNew"
                            icon="mdi-plus"
                            :path="route('CreateArticle')"
                        />
                    </li>

                    <li>
                        <menuButton
                            :text="messages.search"
                            icon="mdi-magnify"
                            :path="route('SearchArticle')"
                        />
                    </li>

                    <li>
                        <menuLabel
                            backgroundColor='#4015a6'
                            textColor='#fafafa'
                            :text="messages.bookmark"
                            icon="mdi-bookmark"
                        />
                    </li>

                    <li>
                        <menuButton
                            :text="messages.createNew"
                            icon="mdi-plus"
                            :path="route('CreateBookMark')"
                        />
                    </li>

                    <li>
                        <menuButton
                            :text="messages.search"
                            icon="mdi-magnify"
                            :path="route('SearchBookMark')"
                        />
                    </li>

                    <li>
                        <menuLabel
                            backgroundColor='#5ac287'
                            textColor='#fafafa'
                            :text="messages.tag"
                            icon="mdi-tag"
                        />
                    </li>

                    <li>
                        <menuButton
                            :text="messages.edit"
                            icon="mdi-pencil-plus"
                            :path="route('searchInEditTag')"
                        />
                    </li>


                    <li>
                        <menuButton
                            textColor="#f0f8ff"
                            backgroundColor="#a80000"
                            :text="messages.logout"
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
            japanese:{
                close:"閉じる",
                article:"記事",
                bookmark:"ブックマーク",
                tag:"タグ",
                createNew:"新規作成",
                search:"検索",
                edit:"編集 検索",
                logout:"ログアウト",
            },
            messages:{
                close:"Close",
                article:"Article",
                bookmark:"Bookmark",
                tag:"Tag",
                createNew:"Create New",
                search:"Search",
                edit:"Edit Search",
                logout:"logout",
            },
            show:false
        }
    },
    components:{
        menuLabel,
        menuButton,
    },
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
    },
}
</script>

<style scoped lang="scss">

/* メニュー */
nav {
    background: rgb(234, 234, 234);
    z-index : 10;//これで10前のレイヤーへ
    height  : 100vh;
    width   : 45vw;
    position: fixed; /* ウィンドウを基準に画面に固定 */
    right   : 0;// 強制的に右端に置く
    top     :0;//ボタンと被らないように頭の位置を下げる
    @media (max-width: 960px){ width: 70%; }
    @media (max-width: 600px){
        width: 100%;
        .originalHead{grid-template-columns:1.5fr 2fr 1.5fr 0.1fr;}
    }

    ol{
        .menuButton{ margin-bottom: 0.5rem;}
        height: 100vh;
        overflow-y: auto;
        list-style-type:none;
        display: grid;
        grid-template-rows:
        auto
        1fr
        auto
        auto
        auto
        1fr
        auto
        auto
        auto
        1fr
        auto
        auto
        4fr
        auto
        ;
        li:nth-child(1){grid-row: 1/2; }

        li:nth-child(2){grid-row: 3/4; }
        li:nth-child(3){grid-row: 4/5; }
        li:nth-child(4){grid-row: 5/6; }

        li:nth-child(5){grid-row: 7/8; }
        li:nth-child(6){grid-row: 8/9; }
        li:nth-child(7){grid-row: 9/10; }

        li:nth-child(8){grid-row: 11/12; }
        li:nth-child(9){grid-row: 12/13; }

        li:nth-child(10){
            grid-row: 16/17;
            // ここだけマージンを消す
            .menuButton{ margin-bottom: 0;}
        }
    }
    .closeButton{
        background-color: hsl(0, 0%, 78%);
        padding:0.5rem 0;
        width: 100%;
        display: grid;
        grid-template-columns:2fr 1fr 4fr 2fr;
        i{
            grid-column: 2/3;
            margin     : auto;
        }
        p{
            font-weight: bold;
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



</style>
