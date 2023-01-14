<template>
    <div class="tagDeleteDialog">
        <v-dialog v-model="dialogFlag" persistent>
            <section class="global_css_Dialog">
                <h2>{{name}}を削除しますか?</h2>
                <p
                    v-show="errorMessages.messages.length>0"
                    v-for ="message of errorMessages.messages" :key="message"
                    class ="global_css_error"
                >
                    <v-icon>mdi-alert-circle-outline</v-icon>
                    {{message}}
                </p>
                <div class="control">
                    <v-btn class="back" @click.stop="dialogFlagSwitch(),resetErrorMessage()" :loading="loading" :disabled="loading">
                        <p>もどる</p>
                    </v-btn>

                    <v-btn class="delete" color="error" @click.stop="deleteTag()" :loading="loading" :disabled="loading">
                        <p>削除する</p>
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
            loading:false,
            id:0,
            name:"",
            dialogFlag:false,
            errorMessages:{
                messages:[]
            },
        }
    },
    methods: {
        //切り替え
        dialogFlagSwitch(){
            this.dialogFlag = !this.dialogFlag
            this.resetErrorMessage()
        },
        resetErrorMessage(){
            this.errorMessages = {messages:[]}
        },
        // セッター(今回はpropsを使わない)
        setter(id,name){
            this.id   = id
            this.name = name
        },
        // 削除処理
        async deleteTag(){
            this.loading = true
            await axios.delete('/api/tag/' + this.id)
            .then((res)=>{
                // ダイアログを閉じて親タグで再読み込みしてもらう
                this.dialogFlag = false

                // this.$emit("deleted");

                // このコンポーネントの中でイナーシャ使っても問題ないようだが､なんか不安なので親の方でやるかどうか迷ってる
                this.$inertia.get('/Tag/Edit/Search')

            })
            .catch((errors) => {
                // エラーメッセージ表示
                if (String(errors.response.status)[0] == 5) {
                    this.errorMessages = {
                        messages:['サーバー側でエラーが発生しました｡数秒待って再度送信してください']
                    }
                }
                else { this.errorMessages = errors.response.data.messages }
                this.loading = false
            })
        }
    },
    mounted() {
        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            //ダイアログが開いている時有効にする
            if(this.deleteDialogFlag == true){
                if (event.key === "Enter") {
                    this.deleteTag()
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
    margin-top: 1rem;
    p{
        text-align: center;
        margin: auto;
    }
    @media (min-width: 601px){
        display:grid;
        grid-template-columns:3fr 1.5fr 0.1fr 1.5fr;
        .back  {grid-column: 2/3;}
        .delete{grid-column: 4/5;}
    }
    @media (max-width: 600px){
        display:flex;
        flex-flow: column;
        gap: 1rem;
    }
}
</style>
