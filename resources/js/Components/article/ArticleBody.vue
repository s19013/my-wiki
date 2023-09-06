<template>
    <div class="articleBody">
        <!-- タブ -->
        <p>{{ messages.shotcutMessage }}</p>
        <div class="tabLabel">
            <v-btn
                @click="changeTab()"
                flat
                :rounded="0"
                :class="[activeTab === 1 ? 'active' : '']"
            >
                <p>{{ messages.beforeConversion }}</p>
            </v-btn>
            <v-btn
                @click="changeTab()"
                flat
                :rounded="0"
                :class="[activeTab === -1 ? 'active' : '']"
            >
                <p>{{ messages.afterConversion }}</p>
            </v-btn>
        </div>
        <!-- md入力欄  -->
        <div v-show="activeTab === 1">
            <v-textarea
                ref="textarea"
                filled
                no-resize
                rows="20"
                :label="messages.bodylabel"
                v-model="body"
                @keydown.tab.prevent.exact="addTabSpace()"
            ></v-textarea>
        </div>

        <CompiledMarkDown v-show="activeTab === -1" :originalMarkDown="body" />
    </div>
</template>

<script>
import CompiledMarkDown from "@/Components/article/CompiledMarkDown.vue";
export default {
    data() {
        return {
            japanese: {
                shotcutMessage: "'ctrl + space'で変換前､変換後切り替え",
                beforeConversion: "本文",
                afterConversion: "変換後",
                bodylabel: "本文 ※マークダウン記法対応",
            },
            messages: {
                shotcutMessage:
                    "Switch before and after conversion with 'ctrl + space'",
                beforeConversion: "text",
                afterConversion: "conversiond",
                bodylabel: "Text * Supports markdown notation",
            },
            activeTab: 1,
            body: "",
        };
    },
    components: { CompiledMarkDown },
    props: {
        originalArticleBody: {
            type: String,
            default: "",
        },
    },
    methods: {
        changeTab() {
            this.activeTab *= -1;
            if (this.activeTab === 1) {
                this.focusToBody();
            }
        },
        serveBody() {
            return this.body;
        },
        addTabSpace() {
            //テキストエリアと挿入する文字列を取得
            // var area = document.querySelector('textarea');
            //カーソルの位置を基準に前後を分割して、その間に文字列を挿入
            // var forward  = area.value.substr(0, area.selectionStart)
            // var backward = area.value.substr(area.selectionStart)

            // area.value = forward + "\t" + backward
            // area.selectionEnd = forward.length +1;

            // !!execCommandは今後廃止される けど､沢山調べても代替案はなかったからみつかるまでこれで!!
            document.execCommand("insertText", false, "\t");
        },
        focusToBody() {
            this.$nextTick(() => this.$refs.textarea.focus());
        },
        keyEvents(event) {
            // タブ切り替え(アプリ内)
            if (event.ctrlKey || event.key === "Meta") {
                if (event.code === "Space") {
                    this.changeTab();
                }
                return;
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
        // とにかくこれを使って最後に処理することができる?
        this.$nextTick(() => {
            // レンダリング後の処理
            // props受け渡し mounted使わなくてもできたはずなんだけどな?
            this.body = this.originalArticleBody;
        });
    },
    beforeUnmount() {
        //キーボードによる動作の削除(副作用みたいエラーがでるため)
        document.removeEventListener("keydown", this.keyEvents);
    },
};
</script>

<style scoped lang="scss">
textarea {
    width: 100%;
    resize: none;
    // padding: 20px;
    background-color: #f6f6f6;
}

.tabLabel {
    display: flex;
    button {
        border: black solid 1px;
        min-width: 6rem;
    }
    p {
        font-size: larger;
    }
}

.active {
    background-color: #ffd4ae;
}
.CompiledMarkDown {
    margin: 1rem;
}
</style>
