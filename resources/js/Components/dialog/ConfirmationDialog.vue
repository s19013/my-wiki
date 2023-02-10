<template>
    <div class="deleteAlertDialog">
        <v-dialog v-model="deleteDialogFlag">
            <section class="global_css_Dialog">
                <h2>{{messages.message}}</h2>
                <div class="control">
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
            deleteDialogFlag:false,
            messages:{
                buttonMessage:"confirmation",
                message:"really?",
                submit:'yes',
                cancel:'no',
            },
        }
    },
    props:{
        japanese:{
            type   :Object,
            default:{
                buttonMessage:"確認",
                message:"良いですか?",
                submit:'はい',
                cancel:'いいえ',
            }
        },
        english:{
            type   :Object,
            default:{
                buttonMessage:"confirmation",
                message:"really?",
                submit:'yes',
                cancel:'no',
            }
        },
    },
    methods: {
        //切り替え
        dialogFlagSwitch(){this.deleteDialogFlag = !this.deleteDialogFlag},
        //ダイアログ内の削除するボタンを押したことを親に伝える
        submit(){
            this.dialogFlagSwitch()
            this.$emit("submit");
        }
    },
    mounted() {
        this.$nextTick(function () {
            // ビュー全体がレンダリングされた後にのみ実行されるコード
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
            else {this.messages = this.english}
        })

        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            //ダイアログが開いている時有効にする
            if(this.deleteDialogFlag == true){
                if (event.key === "Enter") {
                    this.submit()
                    return
                }
                if (event.key === "Escape" || event.key === "Backspace") {
                    this.dialogFlagSwitch()
                    return
                }
            }
        })
    },
}
</script>

<style lang="scss" scoped>
.control {
    p{
        text-align: center;
        margin: auto;
    }
    @media (min-width: 601px){
        display:grid;
        grid-template-columns:3fr 1.5fr 0.1fr 1.5fr;
        margin-top: 1rem;
        .cancel  {grid-column: 2/3;}
        .submit{grid-column: 4/5;}
    }
    @media (max-width: 600px){
        display:grid;
        gap: 1rem;
        grid-template-rows:1fr 1fr;
    }
}
</style>
