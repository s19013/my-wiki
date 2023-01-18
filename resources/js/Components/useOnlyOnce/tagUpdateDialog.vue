<template>
    <div class="tagUpdateDialog">
        <v-dialog v-model="dialogFlag" persistent>
            <section class="global_css_Dialog">
                <v-btn
                        color="#E57373"
                        size="small"
                        :disabled = "loading"
                        :loading  = "loading"
                        elevation="2"
                        @click.stop="dialogFlagSwitch(),resetErrorMessage()">
                        <v-icon>mdi-close-box</v-icon>
                        <p>{{ messages.close }}</p>
                </v-btn>
                <p
                    v-show="errorMessages.name.length>0"
                    v-for ="messages of errorMessages.name" :key="messages"
                    class ="global_css_error"
                >
                    <v-icon>mdi-alert-circle-outline</v-icon>
                    {{messages}}
                </p>

                <v-text-field
                    v-model="name"
                    :label="messages.tagName"
                    outlined hide-details="false"
                />
                <v-btn color="#BBDEFB" class="global_css_haveIconButton_Margin submitButton"
                @click.stop="updateTag()" :disabled = "loading" :loading  = "loading">
                    <v-icon>mdi-content-save</v-icon>
                    <p>{{ messages.update }}</p>
                </v-btn>
            </section>
        </v-dialog>
    </div>
</template>

<script>
export default {
    data() {
        return {
            japanese:{
                tagName:"タグ名",
                close  :'閉じる',
                update :'更新',
            },
            messages:{
                tagName:"tag name",
                close  :'close',
                update :'update',
            },
            dialogFlag:false,
            loading:false,
            id:0,
            name:"",
            errorMessages:{name:[]},
        }
    },
    methods: {
        //切り替え
        dialogFlagSwitch(){this.dialogFlag = !this.dialogFlag},
        resetErrorMessage(){
            this.errorMessages = {messages:[]}
        },
        // セッター(今回はpropsを使わない)
        setter(id,name){
            this.id   = id
            this.name = name
        },
        // 削除処理
        async updateTag(){
            this.loading = true
            await axios.put('/api/tag/update',{
                id  :this.id,
                name:this.name
            })
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
                        name:['サーバー側でエラーが発生しました｡数秒待って再度送信してください']
                    }
                }
                else { this.errorMessages = errors.response.data.messages }
                console.log(this.errorMessages.name);
            })
            this.loading = false
        }
    },
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            //ダイアログが開いている時有効にする
            if(this.dialogFlag == true && this.loading == false){
                // 送信
                if (event.ctrlKey || event.key === "Meta") {
                    if(event.code === "Enter"){this.updateTag()}
                    return
                }
                if (event.key === "Escape") {
                    this.dialogFlagSwitch()
                    return
                }
            }
        })
    },
}
</script>

<style lang="scss" scoped>
.v-input {
    margin: 2rem 0
}
.submitButton{width: 100%;}
</style>
