<template>
    <div class="originalFooter">
        <a target="_blank" rel="noopener noreferrer" href="https://docs.google.com/document/d/e/2PACX-1vQRfqPmWcI2irs1HpRBOjA9lyo2CiIFWRBpWY2lmHnMM8gWZUEmng57BEs1t-VC5Bd_kSCHhmG9gmAA/pub">{{ messages.terms }}</a>
        <a target="_blank" rel="noopener noreferrer" href="https://docs.google.com/document/d/e/2PACX-1vQCW5pRoXeXHiZJ-vz8MImLVm-XTViLIdy1TxTBtsbAAzYb4MpPEaEFucHaWnpzDkI905s5AeW6rui3/pub">{{ messages.privacy }}</a>
        <a target="_blank" rel="noopener noreferrer" href="https://forms.gle/UMU57uzKa1r1E4zBA">{{ messages.enquiry }}</a>

        <v-dialog v-model="deleteDialogFlag">
            <template v-slot:activator="{ props }">
                <v-btn color="error" class="global_css_haveIconButton_Margin"  v-bind="props" flat>
                    <p>{{ messages.withdrawal }}</p>
                </v-btn>
            </template>
            <section class="global_css_Dialog">
                <h2>{{ messages.caution }}</h2>
                <p>{{ messages.message }}</p>
                <div class="control">
                    <v-btn class="back" :disabled="disabledFlag" :loading="disabledFlag" @click.stop="deleteDialogFlag=false">
                        <p>{{ messages.cancel }}</p>
                    </v-btn>

                    <Link :href="route('DeleteUser')" method="delete">
                        <v-btn flat :rounded="0" class="delete" color="error">
                            <p>{{ messages.withdrawal }}</p>
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
            japanese:{
                terms:"利用規約",
                privacy:"プライバシーポリシー",
                enquiry:"問い合わせ",
                withdrawal:"退会",
                caution:"本当に退会しますか",
                message:"メモ､ブックマーク､タグは削除されます(復元できません)",
                cancel:"戻る",
            },
            messages:{
                terms:"terms of service",
                privacy:"privacy policy",
                enquiry:"enquiry",
                withdrawal:"withdrawal",
                caution:"do you really want to leave",
                message:"All registered data will be deleted (cannot be restored).",
                cancel:"cancel",
            },
            deleteDialogFlag:false,
        }
    },
    components:{
        Link
    },
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
    },
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


