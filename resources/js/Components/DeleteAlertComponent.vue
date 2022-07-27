<template>
    <div>
     <!-- ダイアログを呼び出すためのボタン -->
    <v-btn color="error" @click.stop="deleteDialogFlagSwitch"> 削除 </v-btn>

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
export default {
    data() {
        return {
            deleteDialogFlag:false,
        }
    },
    methods: {
        deleteDialogFlagSwitch(){this.deleteDialogFlag = !this.deleteDialogFlag},
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
