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
        submit({
            bookMarkTitle,
            bookMarkUrl,
            tagList,
        }){
            this.$refs.BaseBookMarkLayout.switchDisabledFlag()
            axios.post('/BookMark/Store',{
                bookMarkId   :this.originalBookMark.id,
                bookMarkTitle:bookMarkTitle,
                bookMarkUrl  :bookMarkUrl,
                tagList      :tagList
            })
            .then((res)=>{this.$inertia.get('/BookMark/Search')})
            .catch((err)=>{
                this.$refs.BaseBookMarkLayout.switchDisabledFlag()
                console.log(err);
            })
        },
        deleteBookMark() {
            // 消す処理
            this.$refs.BaseBookMarkLayout.switchDisabledFlag()
            this.$inertia.delete('/BookMark/' + this.originalBookMark.id,{
                onError: (errors) => {
                    console.log( errors )
                    this.$refs.BaseBookMarkLayout.switchDisabledFlag()
                },
            })
        },
    },
    mounted() {

    },
}
</script>
