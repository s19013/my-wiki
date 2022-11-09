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
    props:['originalBookMark','originalCheckedTagList'],
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
            axios.post('/api/bookmark/update',{
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
            axios.delete('/api/bookmark/'+ this.originalBookMark.id)
            .then((res)=>{this.$inertia.get('/BookMark/Search')})
            .catch((error) => {console.log(error);})
        },
    },
    mounted() {

    },
}
</script>
