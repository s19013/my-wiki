<template>
    <div class="confirmationDialog" data-testid="confirmationDialog">
        <v-dialog v-model="dialogFlag" persistent>
            <section class="global_css_Dialog">
                <h2>{{ messages.message }}</h2>
                <div class="controll">
                    <v-btn class="cancel" @click.stop="dialogFlagSwitch()">
                        <p>{{ messages.cancel }}</p>
                    </v-btn>

                    <v-btn class="submit" color="error" @click.stop="submit()">
                        <p>{{ messages.submit }}</p>
                    </v-btn>
                </div>
            </section>
        </v-dialog>
    </div>
</template>

<script>
export default {
    data() {
        return {
            dialogFlag: false,
            messages: {
                buttonMessage: "confirmation",
                message: "really?",
                submit: "yes",
                cancel: "no",
            },
        };
    },
    props: {
        japanese: {
            type: Object,
            default: {
                buttonMessage: "確認",
                message: "良いですか?",
                submit: "はい",
                cancel: "いいえ",
            },
        },
        english: {
            type: Object,
            default: {
                buttonMessage: "confirmation",
                message: "really?",
                submit: "yes",
                cancel: "no",
            },
        },
    },
    methods: {
        //切り替え
        dialogFlagSwitch() {
            this.$store.commit("switchSomeDialogOpening");
            this.dialogFlag = !this.dialogFlag;
        },
        //ダイアログ内の削除するボタンを押したことを親に伝える
        submit() {
            this.dialogFlagSwitch();
            this.$emit("submit");
        },
        keyEvents(event) {
            //ダイアログが開いている時有効にする
            if (this.dialogFlag == true) {
                if (event.key === "Escape") {
                    this.dialogFlagSwitch();
                    return;
                }
            }
        },
    },
    mounted() {
        this.$nextTick(function () {
            // ビュー全体がレンダリングされた後にのみ実行されるコード
            if (this.$store.state.lang == "ja") {
                this.messages = this.japanese;
            } else {
                this.messages = this.english;
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

<style lang="scss" scoped>
.controll {
    p {
        text-align: center;
        margin: auto;
    }
    @media (min-width: 601px) {
        display: grid;
        grid-template-columns: 3fr 1.5fr 0.1fr 1.5fr;
        margin-top: 1rem;
        .cancel {
            grid-column: 2/3;
        }
        .submit {
            grid-column: 4/5;
        }
    }
    @media (max-width: 600px) {
        display: grid;
        gap: 1rem;
        grid-template-rows: 1fr 1fr;
    }
}
</style>
