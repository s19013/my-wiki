<template>
    <BaseBookMarkLayout
        ref="BaseBookMarkLayout"
        :title="$store.state.lang == 'ja' ? 'ブックマーク編集' : 'Edit Bookmark'"
        :pageTitle="$store.state.lang == 'ja' ? 'ブックマーク編集' : 'Edit Bookmark'"
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
                this.$inertia.get('/BookMark/Search')
            })
            .catch((errors)=>{
                this.$store.commit('switchGlobalLoading')
                this.$refs.BaseBookMarkLayout.setErrors(errors.response)
            })
        },
        deleteBookMark() {
            // 消す処理
            axios.delete('/api/bookmark/' + this.originalBookMark.id)
            .then((res)=>{
                this.$inertia.get('/BookMark/Search')
            })
            .catch((errors) => {
                this.$store.commit('switchGlobalLoading')
                console.log(errors);
            })
        },
    },
    mounted() {this.$store.commit('setGlobalLoading',false)},
}
</script>
