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
        async submit({
            bookMarkTitle,
            bookMarkUrl,
            tagList,
        }){
            this.$refs.BaseBookMarkLayout.switchDisabledFlag()
            await axios.post('/api/bookmark/store',{
                bookMarkTitle :bookMarkTitle,
                bookMarkUrl   :bookMarkUrl,
                tagList       :tagList,
                timezone      :Intl.DateTimeFormat().resolvedOptions().timeZone
            })
            .then((res)=>{
                this.$refs.BaseBookMarkLayout.switchDisabledFlag()
                this.$inertia.get('/BookMark/Search')
            })
            .catch((errors) => {
                this.$refs.BaseBookMarkLayout.switchDisabledFlag()
                this.$refs.BaseBookMarkLayout.setErrors(errors.response)
            })
        },
        deleteBookMark() {
            //遷移だけで良い
            this.$inertia.get('/BookMark/Search')
        },
    },
}
</script>

<style lang="scss">
</style>
