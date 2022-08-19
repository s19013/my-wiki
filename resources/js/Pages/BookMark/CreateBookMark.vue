<template>
    <BaseBookMarkLayout
        ref="BaseBookMarkLayout"
        title="新規作成"
        pageTitle="新規作成"
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
            .catch((error) => {console.log(error);})
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
.articleContainer {margin: 0 20px;}
textarea {
        width: 100%;
        resize: none;
        background-color: #f6f6f6;
        padding: 20px;
}
.markdown{
    padding: 0 10px;
    word-break:break-word;
    overflow-wrap:normal;
}

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
.articleError{padding-top: 5px;}
.v-input__details{
    margin: 0;
    padding: 0;
    height: 0;
    width: 0;
}

</style>
