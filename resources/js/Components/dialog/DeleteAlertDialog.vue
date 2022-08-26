<template>
    <div class="deleteAlertDialog">
     <!-- ダイアログを呼び出すためのボタン -->
    <DeleteButton @click="deleteDialogFlagSwitch"/>

    <v-dialog
      v-model="deleteDialogFlag"
      persistent
    >
        <section class="Dialog">
            <h2>この記事を削除しますか</h2>
            <v-row>
                <v-col cols=""></v-col>
                <v-col cols="2">
                    <p @click.stop="deleteDialogFlagSwitch()">もどる</p>
                </v-col>
                <v-col cols="2">
                    <p class="error" @click.stop="deleteAricle()">削除する</p>
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
        }
    },
    components:{
        DeleteButton
    },
    methods: {
        //切り替え
        deleteDialogFlagSwitch(){this.deleteDialogFlag = !this.deleteDialogFlag},
        //ダイアログ内の削除するボタンを押したことを親に伝える
        deleteAricle(){this.$emit("deleteAricleTrigger");}
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
