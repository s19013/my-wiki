<template>
    <BaseBookMarkLayout
        ref="BaseBookMarkLayout"
        title="記事編集"
        pageTitle="記事編集"
        :originalBookMarkTitle   ="originalBookMark.title"
        :originalBookMarkUrl     ="originalBookMark.url"
        :originalCheckedTagList  ="originalCheckedTagList"
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
            axios.post('/api/bookmark/delete',{bookMarkId:this.originalBookMark.id})
            .then((res)=>{this.$inertia.get('/BookMark/Search')})
            .catch((error) => {console.log(error);})
        },
    },
    mounted() {

    },
}
</script>

<style lang="scss" scoped>
.articleContainer {margin: 0 20px;}

.tabLabel{
    li{
        display: inline-block;
        list-style:none;
        border:black solid 1px;
        padding:10px 20px;
    }
    .active{
        font-weight: bold;
        cursor: default;
    }

    .notActive{
        background: #919191;
        color: black;
        cursor: pointer;
    }
}

.head{margin-top: 10px;}
.bookMarkError{padding-top: 5px;}
.v-input__details{
    margin: 0;
    padding: 0;
    height: 0;
    width: 0;
}

</style>
