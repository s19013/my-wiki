<template>
    <div class="navMenu" data-testid="naveMenu">
        <transition name="slide">
            <nav v-show="show">
                <ol>
                    <li>
                        <button class="closeButton" @click.stop="show = !show">
                            <v-icon> mdi-close-box</v-icon>
                            <p>{{ messages.close }}</p>
                        </button>
                    </li>

                    <li>
                        <menuLabel
                            backgroundColor="#1a81c1"
                            textColor="#fafafa"
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
                            backgroundColor="#4015a6"
                            textColor="#fafafa"
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
                        <menuButton
                            backgroundColor="#5ac287"
                            textColor="#fafafa"
                            :text="messages.tag"
                            icon="mdi-tag"
                            :path="route('searchInEditTag')"
                        />
                    </li>

                    <li>
                        <menuButton
                            textColor="#f0f8ff"
                            backgroundColor="#464646"
                            :text="messages.setting"
                            icon="mdi-cog"
                            :path="route('setting')"
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
import menuLabel from "@/Components/menuLabel.vue";
import menuButton from "@/Components/button/menuButton.vue";

export default {
    data() {
        return {
            japanese: {
                close: "閉じる",
                article: "記事",
                bookmark: "ブックマーク",
                tag: "タグ",
                createNew: "新規作成",
                search: "検索",
                edit: "編集 検索",
                setting: "設定",
                logout: "ログアウト",
            },
            messages: {
                close: "Close",
                article: "Article",
                bookmark: "Bookmark",
                tag: "Tag",
                createNew: "Create New",
                search: "Search",
                edit: "Edit Search",
                setting: "Setting",
                logout: "logout",
            },
            show: false,
        };
    },
    components: {
        menuLabel,
        menuButton,
    },
    methods: {
        keyEvents(event) {
            if (
                this.$store.state.globalLoading === false &&
                this.$store.state.someDialogOpening === false
            ) {
                // メニュー呼び出し
                if (
                    (event.ctrlKey || event.key === "Meta") &&
                    event.altKey &&
                    event.code === "KeyM"
                ) {
                    event.preventDefault();
                    this.show = !this.show;
                    return;
                }

                // 記事作成画面
                if (
                    (event.ctrlKey || event.key === "Meta") &&
                    event.altKey &&
                    event.code === "KeyA"
                ) {
                    event.preventDefault();
                    this.$inertia.get(route("CreateArticle"));
                    return;
                }

                // 記事検索画面
                if (event.altKey && event.shiftKey && event.code === "KeyA") {
                    event.preventDefault();
                    this.$inertia.get(route("SearchArticle"));
                    return;
                }

                // ブックマーク作成画面
                if (
                    (event.ctrlKey || event.key === "Meta") &&
                    event.altKey &&
                    event.code === "KeyB"
                ) {
                    event.preventDefault();
                    this.$inertia.get(route("CreateBookMark"));
                    return;
                }

                // ブックマーク検索画面
                if (event.altKey && event.shiftKey && event.code === "KeyB") {
                    event.preventDefault();
                    this.$inertia.get(route("SearchBookMark"));
                    return;
                }

                // タグ検索画面
                if (event.altKey && event.shiftKey && event.code === "KeyT") {
                    event.preventDefault();
                    this.$inertia.get(route("searchInEditTag"));
                    return;
                }

                // 設定画面
                if (event.altKey && event.shiftKey && event.code === "KeyS") {
                    event.preventDefault();
                    this.$inertia.get(route("setting"));
                    return;
                }
            }
        },
    },
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja") {
                this.messages = this.japanese;
            }
        });
        //キーボード受付
        document.addEventListener("keydown", this.keyEvents);
    },
    beforeUnmount() {
        //キーボードによる動作の削除(副作用みたいエラーがでるため)
        document.removeEventListener("keydown", this.keyEvents);
    },
};
</script>

<style scoped lang="scss">
/* メニュー */
nav {
    background: rgb(234, 234, 234);
    z-index: 10; //これで10前のレイヤーへ
    height: 100vh;
    width: 45vw;
    position: fixed; /* ウィンドウを基準に画面に固定 */
    right: 0; // 強制的に右端に置く
    top: 0; //ボタンと被らないように頭の位置を下げる
    @media (max-width: 960px) {
        width: 70%;
    }
    @media (max-width: 600px) {
        width: 100%;
        .originalHead {
            grid-template-columns: 1.5fr 2fr 1.5fr 0.1fr;
        }
    }

    ol {
        height: 100vh;
        overflow-y: auto;
        list-style-type: none;
        display: flex;
        flex-flow: column;
        .menuLabel {
            margin-top: 1.5rem;
            padding: 0.5rem 0;
            font-size: 1.4rem;
        }
        .menuButton {
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        li:nth-child(8) {
            .menuButton {
                margin-top: 1.4rem;
            }
        }
        li:nth-child(9) {
            .menuButton {
                margin-top: 1.4rem;
            }
        }

        li:nth-child(10) {
            // ここだけ下とぴったりくっつける
            width: 100%;
            position: absolute;
            bottom: 0px;
            .menuButton {
                margin-bottom: 0;
            }
        }
    }
    .closeButton {
        background-color: hsl(0, 0%, 78%);
        padding: 0.5rem 0;
        width: 100%;
        display: grid;
        grid-template-columns: 2fr 1fr 4fr 2fr;
        i {
            grid-column: 2/3;
            margin: auto;
        }
        p {
            font-weight: bold;
            text-align: center;
            grid-column: 3/4;
            margin: auto;
        }
    }
}

// アニメ
// on
.slide-enter-active {
    transition: all 0.4s ease;
}
.slide-enter-from {
    transform: translateX(100%);
}

//off
.slide-leave-active {
    transition: all 0.4s ease;
}
.slide-leave-to {
    transform: translateX(100%);
}
</style>
