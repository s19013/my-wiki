<template>
    <BaseLayout :title="'my-wiki ' + article.title" pageTitle="記事観覧">
        <div class="articleContainer">
                <!-- タイトルとボタン2つ -->
                <div class="head">
                    <DeleteAlertComponent
                        type="bookmark"
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
        <loadingDialog :loadingFlag="articleDeleting"></loadingDialog>
    </BaseLayout>
</template>

<script>
import CompiledMarkDown from '@/Components/article/CompiledMarkDown.vue';
import DeleteAlertComponent from '@/Components/dialog/DeleteAlertDialog.vue';
import loadingDialog from '@/Components/loading/loadingDialog.vue';
import TagList from '@/Components/TagList.vue';
import DateLabel from '@/Components/DateLabel.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue'
import { Link } from '@inertiajs/inertia-vue3';
import axios from 'axios'


export default{
    data() {
      return {
        //loding
        articleDeleting:false,
      }
    },
    props:['article','articleTagList'],
    components:{
        DeleteAlertComponent,
        loadingDialog,
        TagList,
        BaseLayout,
        Link,
        DateLabel,
        CompiledMarkDown,
    },
    methods: {
        deleteArticle() {
            this.articleDeleting = true
            // 消す処理
            axios.delete('/api/article/'+this.article.id)
            .then((res) => {
                //遷移
                this.$inertia.get('/Article/Search')
                this.articleDeleting = false
            })
            .catch((error) => {
                console.log(error);
                this.articleSending = false
            })
        },
    },
}
</script>

<style lang="scss" scoped>
.articleContainer {
    margin: 0 20px;
    margin-top: 2rem;
}
.title{
    padding: 2px;
    border:black solid 1px;
}

.head{
    display: grid;
    grid-template-columns:9fr auto auto;
    gap:2rem;
    margin-bottom: 1.5rem ;
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
</style>
