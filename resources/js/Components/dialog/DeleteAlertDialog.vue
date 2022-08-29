<template>
    <div class="deleteAlertDialog">
     <!-- ダイアログを呼び出すためのボタン -->
    <DeleteButton @click.stop="deleteDialogFlagSwitch"/>

    <v-dialog
      v-model="deleteDialogFlag"
      persistent
    >
        <section class="Dialog">
            <h2>{{text}}</h2>
            <div class="control">
                <p class="back"  @click.stop="deleteDialogFlagSwitch()" >もどる</p>
                <p class="error delete" @click.stop="deleteTrigger()" >削除する</p>
            </div>
        </section>
    </v-dialog>
    </div>
</template>

<script>
import DeleteButton from '@/Components/button/DeleteButton.vue';
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
    components:{
        DeleteButton
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
            console.log(event);
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
.Dialog{
    p{
        cursor: pointer;

    }
}
@media (min-width: 601px){
    .control{
        display:grid;
        grid-template-columns:3fr 1.5fr 0.1fr 1.5fr;
        margin-top: 1rem;
        .back{grid-column: 2/3;}
        .delete{grid-column: 4/5;}
        p{
            text-align: center;
            margin: auto;
        }
    }
}
@media (max-width: 600px){
    p{margin-top: 1rem;}
}
</style>
