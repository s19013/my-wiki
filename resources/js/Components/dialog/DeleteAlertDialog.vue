<template>
    <div class="deleteAlertDialog">
     <!-- ダイアログを呼び出すためのボタン -->
    <DeleteButton @click="deleteDialogFlagSwitch"/>

    <v-dialog
      v-model="deleteDialogFlag"
      persistent
    >
        <section class="Dialog">
            <h2>{{text}}</h2>
            <v-row>
                <v-col cols=""></v-col>
                <v-col cols="2">
                    <p @click.stop="deleteDialogFlagSwitch()">もどる</p>
                </v-col>
                <v-col cols="2">
                    <p class="error" @click.stop="deleteTrigger()">削除する</p>
                </v-col>
            </v-row>
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
    },
}
</script>

<style lang="scss" scoped>
.Dialog{
    .v-col{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    p{
        cursor: pointer;
    }
}
</style>
