<template>
    <div class="deleteAlertDialog">
     <!-- ダイアログを呼び出すためのボタン -->

    <v-btn color="error"
        class="global_css_haveIconButton_Margin"  @click.stop="deleteDialogFlagSwitch()"
        :disabled="disabledFlag" :loading="disabledFlag"
    >
        <v-icon>mdi-trash-can</v-icon>
        <p>削除</p>
    </v-btn>

        <v-dialog v-model="deleteDialogFlag">
            <section class="global_css_Dialog">
                <h2>{{text}}</h2>
                <div class="control">
                    <v-btn class="back" :disabled="disabledFlag" :loading="disabledFlag" @click.stop="deleteDialogFlagSwitch()">
                        <p>もどる</p>
                    </v-btn>

                    <v-btn class="delete" color="error" :disabled="disabledFlag" :loading="disabledFlag" @click.stop="deleteTrigger()">
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
            deleteDialogFlag:false,
            text:null,
        }
    },
    props:{
        type:{
            type   :String,
            default:"article"
        },
        disabledFlag:{
            type   :Boolean,
            default:false
        }
    },
    methods: {
        //切り替え
        deleteDialogFlagSwitch(){this.deleteDialogFlag = !this.deleteDialogFlag},
        //ダイアログ内の削除するボタンを押したことを親に伝える
        deleteTrigger(){
            this.deleteDialogFlagSwitch()
            this.$emit("deleteTrigger");
        }
    },
    mounted() {
        //textの切り替え
        if(this.type.toLowerCase() == 'article'){ this.text = "この記事を削除しますか" }
        else {this.text = "このブックマークを削除しますか"}

        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            //ダイアログが開いている時有効にする
            if(this.deleteDialogFlag == true){
                if (event.key === "Enter") {
                    this.deleteTrigger()
                    return
                }
                if (event.key === "Escape" || event.key === "Backspace") {
                    this.deleteDialogFlagSwitch()
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
        .back  {grid-column: 2/3;}
        .delete{grid-column: 4/5;}
    }
    @media (max-width: 600px){
        display:grid;
        gap: 1rem;
        grid-template-rows:1fr 1fr;
    }
}
</style>
