<template>
    <BaseBookMarkLayout
        ref="BaseBookMarkLayout"
        title="ブックマーク編集"
        pageTitle="ブックマーク編集"
        :originalBookMark        ="originalBookMark"
        :originalCheckedTagList  ="originalCheckedTagList"
        :edit="true"
        @triggerSubmit           = "submit"
        @triggerDeleteBookMark   = "deleteBookMark"
        >
    </BaseBookMarkLayout>
</template>

<script>
import BaseBookMarkLayout from '@/Layouts/BaseBookMarkLayout.vue'

export default {
    data() {
      return {}
    },
    props:{
        'originalBookMark':{
            type:Object,
            required: true,
        },
        'originalCheckedTagList':{
            type:Array,
            required: true,
        }
    },
    components:{
        BaseBookMarkLayout
    },
    methods: {
        async submit({
            bookMarkTitle,
            bookMarkUrl,
            tagList,
        }){
            await axios.put('/api/bookmark/update',{
                bookMarkId    :this.originalBookMark.id,
                bookMarkTitle :bookMarkTitle,
                bookMarkUrl   :bookMarkUrl,
                tagList       :tagList,
                timezone      :Intl.DateTimeFormat().resolvedOptions().timeZone
            })
            .then((res)=>{
                this.$refs.BaseBookMarkLayout.switchDisabledFlag()
                this.$inertia.get('/BookMark/Search')
            })
            .catch((errors)=>{
                this.$refs.BaseBookMarkLayout.switchDisabledFlag()
                this.$refs.BaseBookMarkLayout.setErrors(errors.response)
            })
        },
        deleteBookMark() {
            // 消す処理
            axios.delete('/api/bookmark/' + this.originalBookMark.id)
            .then((res)=>{
                this.$refs.BaseBookMarkLayout.switchDisabledFlag()
                this.$inertia.get('/BookMark/Search')
            })
            .catch((errors) => {
                this.$refs.BaseBookMarkLayout.switchDisabledFlag()
                console.log(errors);
            })
        },
    },
    mounted() {

    },
}
</script>
