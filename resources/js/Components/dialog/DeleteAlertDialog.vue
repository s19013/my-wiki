<template>
    <div class="deleteAlertDialog" data-testid="deleteAlertDialog">
        <!-- ダイアログを呼び出すためのボタン -->

        <v-btn
            color="error"
            class="global_css_haveIconButton_Margin"
            @click.stop="deleteDialogFlagSwitch()"
        >
            <v-icon>mdi-trash-can</v-icon>
            <p>{{ messages.delete }}</p>
        </v-btn>

        <v-dialog v-model="deleteDialogFlag" persistent>
            <section class="global_css_Dialog">
                <h2>{{ messages.message }}</h2>
                <div class="control">
                    <v-btn class="back" @click.stop="deleteDialogFlagSwitch()">
                        <p>{{ messages.cancel }}</p>
                    </v-btn>

                    <v-btn
                        class="delete"
                        color="error"
                        @click.stop="deleteTrigger()"
                    >
                        <p>{{ messages.delete }}</p>
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
            deleteDialogFlag: false,
            japanese: {
                message: "削除しますか",
                delete: "削除",
                cancel: "戻る",
            },
            messages: {
                message: "Do you want to delete",
                delete: "delete",
                cancel: "cancel",
            },
        };
    },
    props: {
        message: {
            type: String,
            default: "削除しますか",
        },
    },
    methods: {
        //切り替え
        deleteDialogFlagSwitch() {
            this.$store.commit("switchSomeDialogOpening");
            this.deleteDialogFlag = !this.deleteDialogFlag;
        },
        //ダイアログ内の削除するボタンを押したことを親に伝える
        deleteTrigger() {
            this.deleteDialogFlagSwitch();
            this.$emit("deleteTrigger");
        },
        keyEvents(event) {
            //ダイアログが開いている時有効にする
            if (this.deleteDialogFlag == true) {
                if (event.key === "Escape") {
                    this.deleteDialogFlagSwitch();
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
.control {
    p {
        text-align: center;
        margin: auto;
    }
    @media (min-width: 601px) {
        display: grid;
        grid-template-columns: 3fr 1.5fr 0.1fr 1.5fr;
        margin-top: 1rem;
        .back {
            grid-column: 2/3;
        }
        .delete {
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
