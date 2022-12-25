<template>
    <div class="originalFooter">
        <a target="_blank" rel="noopener noreferrer" href="https://docs.google.com/document/d/e/2PACX-1vQRfqPmWcI2irs1HpRBOjA9lyo2CiIFWRBpWY2lmHnMM8gWZUEmng57BEs1t-VC5Bd_kSCHhmG9gmAA/pub">利用規約</a>
        <a target="_blank" rel="noopener noreferrer" href="https://docs.google.com/document/d/e/2PACX-1vQCW5pRoXeXHiZJ-vz8MImLVm-XTViLIdy1TxTBtsbAAzYb4MpPEaEFucHaWnpzDkI905s5AeW6rui3/pub">プライバシーポリシー</a>
        <a target="_blank" rel="noopener noreferrer" href="https://forms.gle/UMU57uzKa1r1E4zBA">問い合わせ</a>

        <v-dialog v-model="deleteDialogFlag">
            <template v-slot:activator="{ props }">
                <v-btn color="error" class="global_css_haveIconButton_Margin"  v-bind="props" flat>
                    <p>退会</p>
                </v-btn>
            </template>
            <section class="global_css_Dialog">
                <h2>本当に退会しますか</h2>
                <p>メモ､ブックマーク､タグは削除され(復元できません)</p>
                <div class="control">
                    <v-btn class="back" :disabled="disabledFlag" :loading="disabledFlag" @click.stop="deleteDialogFlag=false">
                        <p>もどる</p>
                    </v-btn>

                    <Link :href="route('DeleteUser')" method="delete">
                        <v-btn flat :rounded="0" class="delete" color="error">
                            <p>削除する</p>
                        </v-btn>
                    </Link>
                </div>
            </section>
        </v-dialog>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
export default{
    data() {
        return {
            deleteDialogFlag:false
        }
    },
    components:{
        Link
    }
}
</script>

<style lang="scss" scoped>
.originalFooter{
    background-color: rgb(127, 255, 174);
    height        : 3rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    a {color: black;}
}
</style>


