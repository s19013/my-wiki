<template>
    <BaseBookMarkLayout
        ref  ="BaseBookMarkLayout"
        title="新規作成"
        pageTitle      ="新規作成"
        @triggerSubmit = "submit"
        @triggerDeleteBookMark = "deleteBookMark"
    >
    </BaseBookMarkLayout>
</template>

<script>
import BaseBookMarkLayout from '@/Layouts/BaseBookMarkLayout.vue'

export default {
    data() {
      return {}
    },
    components:{BaseBookMarkLayout},
    methods: {
        submit({
            bookMarkTitle,
            bookMarkUrl,
            tagList,
        }){
            this.$refs.BaseBookMarkLayout.switchDisabledFlag()
            axios.post('/api/bookmark/store',{
                bookMarkTitle:bookMarkTitle,
                bookMarkUrl  :bookMarkUrl,
                tagList      :tagList,
            })
            .then((res)=>{this.$inertia.get('/BookMark/Search')})
            .catch((error) => {
                if (error.response.status == 400){this.$refs.BaseBookMarkLayout.alreadyExistErrorFlag = true}
                console.log(error);
            })
            this.$refs.BaseBookMarkLayout.switchDisabledFlag()
        },
        deleteBookMark() {
            //遷移
            this.$refs.BaseBookMarkLayout.switchDisabledFlag()
            this.$inertia.get('/BookMark/Search')
        },
    },
}
</script>

<style lang="scss">
</style>
