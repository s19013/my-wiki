<template>
    <BaseLayout :title="'my-wiki ' + article.title" pageTitle="記事観覧">
        <div class="articleContainer">
                <!-- タイトルとボタン2つ -->
                <div class="head">
                    <DeleteAlertComponent
                        type="bookmark"
                        :disabledFlag="disabledFlag"
                        @deleteTrigger="deleteArticle"
                    />
                    <Link :href="'/Article/Edit/' + article.id">
                        <v-btn class="editButton global_css_haveIconButton_Margin" color="#BBDEFB">
                            <v-icon>mdi-pencil-plus</v-icon>
                            <p>編集</p>
                        </v-btn>
                    </Link>
                </div>

                <DateLabel :createdAt="article.created_at" :updatedAt="article.updated_at"/>
                <!-- タグ -->
                <TagList :tagList="articleTagList"/>

                <h1 class="title">{{article.title}}</h1>

                <!-- md表示 -->
                <CompiledMarkDown :originalMarkDown="article.body"/>
        </div>
    </BaseLayout>
</template>

<script>
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import CompiledMarkDown from '@/Components/article/CompiledMarkDown.vue';
import TagList from '@/Components/TagList.vue';
import DateLabel from '@/Components/DateLabel.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'
import { Link } from '@inertiajs/inertia-vue3';
import axios from 'axios'


export default{
    data() {
      return {
        //loding
        disabledFlag:false,
      }
    },
    props:['article','articleTagList'],
    components:{
        DeleteAlertComponent,
        TagList,
        BaseLayout,
        Link,
        DateLabel,
        CompiledMarkDown,
    },
    methods: {
        deleteArticle() {
            this.disabledFlag = true
            // 消す処理
            // axios.delete('/api/article/'+this.article.id)
            // axios.delete('/Article/'+this.article.id)
            // .then((res) => {
            //     //遷移
            //     this.$inertia.get('/Article/Search')
            //     this.disabledFlag = false
            // })
            // .catch((error) => {
            //     console.log(error);
            //     this.articleSending = false
            // })
            this.$inertia.delete('/Article/' + this.article.id,{
                onSuccess: () => { },
                onError: (errors) => {console.log( errors )},
            })

        },
    },
}
</script>

<style lang="scss" scoped>
.articleContainer {
    margin: 0 1rem;
    margin-top: 1rem;
    @media (max-width: 900px){margin-top: 2rem;}
}
.title{
    padding: 2px;
    border:black solid 1px;
}

.head{
    display: grid;
    grid-template-columns:9fr auto auto;
    gap:2rem;
    .deleteAlertDialog{
        grid-column: 2/3;
    }
    .saveButton{
        margin-left:1rem ;
        grid-column: 3/4;
    }
}
.CompiledMarkDown{
    margin:1rem 0;
    @media (max-width: 600px){ margin:0.2rem; }
}
.DateLabel{
    margin: 0.5rem 0;
    justify-content: flex-end;
}
</style>
