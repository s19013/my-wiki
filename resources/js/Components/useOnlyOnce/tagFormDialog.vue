<template>
    <div class="tagUpdateDialog">
        <v-dialog v-model="dialogFlag" persistent>
            <div class="global_css_Dialog">
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

                <v-form v-on:submit.prevent="submit">
                    <v-text-field
                        v-model="name"
                        :label="messages.tagName"
                        outlined hide-details="false"
                    />
                    <v-btn color="#BBDEFB" class="global_css_haveIconButton_Margin submitButton"
                    @click.stop="submit()" :disabled = "loading" :loading  = "loading">
                        <v-icon>mdi-content-save</v-icon>
                        <p v-if="type == 'create'">{{ messages.create }}</p>
                        <p v-else>{{ messages.update }}</p>
                    </v-btn>
                </v-form>
            </div>
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
                create :'作成',
            },
            messages:{
                tagName:"tag name",
                close  :'close',
                update :'update',
                create :'create',
            },
            dialogFlag:false,
            loading:false,
            id:0,
            name:"",
            errorMessages:{name:[]},
        }
    },
    props:{
        type:{
            type:String,
            default:"update"
        },
    },
    methods: {
        //切り替え
        dialogFlagSwitch(){
            this.$store.commit('switchSomeDialogOpening')
            this.dialogFlag = !this.dialogFlag
        },
        resetErrorMessage(){this.errorMessages = {messages:[]}},
        // セッター(今回はpropsを使わない)
        setIdAndName(id,name){
            this.id   = id
            this.name = name
        },
        setErrorMessages(errors){
            // エラーメッセージ表示
            if (String(errors.response.status)[0] == 5) {
                this.errorMessages = {name:['サーバー側でエラーが発生しました｡数秒待って再度送信してください']}
            }
            else { this.errorMessages = errors.response.data.messages }
        },
        transition(){
            this.dialogFlag = false
            this.$store.commit('switchGlobalLoading')
            this.$inertia.get('/Tag/Edit/Search')
        },
        submit(){
            if (this.type == "create") {this.createTag()}
            else {this.updateTag()}
        },
        async createTag(){
            this.loading = true
            await axios.post('/api/tag/store',{name:this.name})
            .then((res)=>{this.transition()})
            .catch((errors) => {this.setErrorMessages(errors)})
            this.loading = false
        },
        async updateTag(){
            this.loading = true
            await axios.put('/api/tag/update',{
                id  :this.id,
                name:this.name
            })
            .then((res)=>{this.transition()})
            .catch((errors) => {this.setErrorMessages(errors)})
            this.loading = false
        },
        keyEvents(event){
            //ダイアログが開いている時有効にする
            if(this.dialogFlag == true && this.loading == false){
                if (event.key === "Escape") {
                    this.dialogFlagSwitch()
                    return
                }
            }
        }
    },
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
        //キーボード受付
        document.addEventListener('keydown', this.keyEvents)
    },
    beforeUnmount() {
        //キーボードによる動作の削除(副作用みたいエラーがでるため)
        document.removeEventListener("keydown", this.keyEvents);
    }
}
</script>

<style lang="scss" scoped>
.v-input {
    margin: 2rem 0
}
.submitButton{width: 100%;}
</style>
