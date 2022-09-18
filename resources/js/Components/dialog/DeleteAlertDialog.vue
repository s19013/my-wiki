<template>
    <div class="deleteAlertDialog">
     <!-- ダイアログを呼び出すためのボタン -->

    <v-btn color="error" class="global_css_haveIconButton_Margin"  @click.stop="deleteDialogFlagSwitch()">
        <v-icon>mdi-trash-can</v-icon>
        <p>削除</p>
    </v-btn>

    <v-dialog
      v-model="deleteDialogFlag"
      persistent
    >
        <section class="global_css_Dialog">
            <h2>{{text}}</h2>
            <div class="control">
                <v-btn flat :rounded="0" @click.stop="deleteDialogFlagSwitch()" class="back">
                    <p>もどる</p>
                </v-btn>
                <!-- <button type="button" class="back" >
                </button> -->
                <v-btn color="error" flat :rounded="0" @click.stop="deleteTrigger()" class="delete">
                    <p>削除する</p>
                </v-btn>

                <!-- <button type="button" class="delete">
                </button> -->
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
        }
    },
    methods: {
        //切り替え
        deleteDialogFlagSwitch(){this.deleteDialogFlag = !this.deleteDialogFlag},
        //ダイアログ内の削除するボタンを押したことを親に伝える
        deleteTrigger(){this.$emit("deleteTrigger");}
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
                if (event.key === "Escape") {
                    this.deleteDialogFlag = false
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
}
@media (min-width: 601px){
    .control{
        display:grid;
        grid-template-columns:3fr 1.5fr 0.1fr 1.5fr;
        margin-top: 1rem;
        .back  {grid-column: 2/3;}
        .delete{grid-column: 4/5;}
    }
}
@media (max-width: 600px){
    .control{
        display:grid;
        grid-template-rows:1fr 1fr;
        gap: 1rem;
    }
}
</style>
