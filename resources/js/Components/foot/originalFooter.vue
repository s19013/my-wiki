<template>
    <div class="originalFooter">
        <a target="_blank" rel="noopener noreferrer" :href="messages.termsUrl">{{ messages.terms }}</a>
        <a target="_blank" rel="noopener noreferrer" :href="messages.privacyUrl">{{ messages.privacy }}</a>
        <a target="_blank" rel="noopener noreferrer" :href="messages.enquiryUrl">{{ messages.enquiry }}</a>
    </div>
</template>

<script>

export default{
    data() {
        return {
            japanese:{
                terms:"利用規約",
                termsUrl:"https://docs.google.com/document/d/e/2PACX-1vQRfqPmWcI2irs1HpRBOjA9lyo2CiIFWRBpWY2lmHnMM8gWZUEmng57BEs1t-VC5Bd_kSCHhmG9gmAA/pub",
                privacy:"プライバシーポリシー",
                privacyUrl:"https://docs.google.com/document/d/e/2PACX-1vQCW5pRoXeXHiZJ-vz8MImLVm-XTViLIdy1TxTBtsbAAzYb4MpPEaEFucHaWnpzDkI905s5AeW6rui3/pub",
                enquiry:"問い合わせ",
                enquiryUrl:"https://forms.gle/9tXeZcXmqH2zR6SP9",
            },
            messages:{
                terms:"terms of service",
                termsUrl:"https://docs.google.com/document/d/e/2PACX-1vSdKoL_dJ3sVwsBBdNh-lVetKD-WupAVq56CYTpTkpkcMHvTNfBruWBnqleHuYDPa7yv1CkdfCL79im/pub",
                privacy:"privacy policy",
                privacyUrl:"https://docs.google.com/document/d/e/2PACX-1vQ0WcXsD3kbYli0SqzaQ0yks_r5uuIgIClMLmT_4g6WLbx9zjcXeoAvxHcLmxbQC_OkrFcieFUaghU_/pub",
                enquiry:"enquiry",
                enquiryUrl:"https://forms.gle/Y4RPh3k3V9G3wnNp9",
            },

        }
    },
    components:{},
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
    },
}
</script>

<style lang="scss" scoped>
.originalFooter{
    padding:1rem 0;
    background-color: rgb(127, 255, 174);
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    a {color: black;}
    @media (max-width: 960px){
        flex-flow: column;
    }
}
</style>


