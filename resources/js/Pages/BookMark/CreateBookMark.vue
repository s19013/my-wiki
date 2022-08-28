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
            this.$refs.BaseBookMarkLayout.switchBookMarkSending()
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
            this.$refs.BaseBookMarkLayout.switchBookMarkSending()
        },
        deleteBookMark() {
            //遷移
            this.$inertia.get('/BookMark/Search')
        },
    },
}
</script>

<style lang="scss">
</style>
