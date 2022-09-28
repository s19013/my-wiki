<template>
    <BaseBookMarkLayout
        ref="BaseBookMarkLayout"
        title="記事編集"
        pageTitle="記事編集"
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
            this.$refs.BaseBookMarkLayout.switchBookMarkSending()
            axios.post('/api/bookmark/update',{
                bookMarkId   :this.originalBookMark.id,
                bookMarkTitle:bookMarkTitle,
                bookMarkUrl  :bookMarkUrl,
                tagList      :tagList
            })
            .then((res)=>{this.$inertia.get('/BookMark/Search')})
            .catch((err)=>{console.log(err);})
            this.$refs.BaseBookMarkLayout.switchBookMarkSending()
        },
        deleteBookMark() {
            // 消す処理
            axios.delete('/api/bookmark/'+ this.originalBookMark.id)
            .then((res)=>{this.$inertia.get('/BookMark/Search')})
            .catch((error) => {console.log(error);})
        },
    },
    mounted() {

    },
}
</script>
