<template>
    <BaseLayout title="タグ編集" pageTitle="タグ編集">
        <v-container>
            <SearchField
                ref = "SearchField"
                searchLabel   ="タグ検索"
                :loadingFlag  ="loading"
                :orignalKeyWord="old.keyword"
                @triggerSearch="search({
                    page:1,
                    keyword:this.$refs.SearchField.serveKeywordToParent(),
                })"
                >
            </SearchField>

            <!-- loadingアニメ -->
            <loading v-show="loading"></loading>
            <p>(使用回数)</p>
            <div v-show="!loading">
                <template v-for="tag of result.data" :key="tag.id">
                    <div class ="content">
                        <DateLabel :createdAt="tag.created_at" :updatedAt="tag.updated_at"/>
                        <div class="elements">
                            <h3>({{tag.count}})</h3>
                            <h2>{{tag.name}}</h2>
                            <v-btn color="error" elevation="2"
                            class="deleteButton" @click="openDeleteDialog(tag.id,tag.name)">
                                削除
                            </v-btn>
                            <v-btn color="submit" elevation="2"
                            class="submitButton" @click="openUpdateDialog(tag.id,tag.name)">
                                編集
                            </v-btn>
                        </div>
                    </div>
                </template>
            </div>
            <tagDeleteDialog ref = "tagDeleteDialog"/>
            <tagUpdateDialog ref = "tagUpdateDialog"/>
        <v-pagination
            v-model="page"
            :length="result.last_page"
        ></v-pagination>
        </v-container>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'
import loading from '@/Components/loading/loading.vue'
import SearchField from '@/Components/SearchField.vue'
import DateLabel from '@/Components/DateLabel.vue';
import tagDeleteDialog from '@/Components/useOnlyOnce/tagDeleteDialog.vue'
import tagUpdateDialog from '@/Components/useOnlyOnce/tagUpdateDialog.vue'

export default{
    data() {
        return {
            page: this.result.current_page,
            loading:false,
        }
    },
    props:{
        result:{
            type:Object
        },
        old:{
            type:Object
        }
    },
    components:{
        BaseLayout,
        loading,
        SearchField,
        DateLabel,
        tagDeleteDialog,
        tagUpdateDialog,
    },
    methods: {
        // 検索用
        search({page,keyword}){
            this.loading     = true
            this.$inertia.get('/Tag/Edit/Search' ,{
                page    : page,
                keyword : keyword,
                onError:(errors) => {
                    console.log(errors)
                    this.loading = false
                }
            })
        },
        // 削除
        openDeleteDialog(id,name){
            this.$refs.tagDeleteDialog.setter(id,name)
            this.$refs.tagDeleteDialog.dialogFlagSwitch()
        },
        // 更新
        openUpdateDialog(id,name){
            this.$refs.tagUpdateDialog.setter(id,name)
            this.$refs.tagUpdateDialog.dialogFlagSwitch()
        },
    },
    watch: {
    // @input="pagination"でできるはずなのにできないのでwatchで対応
    // ページネーションのボタン類を押した場合の処理
    // 厳密にはページネーションのボタン類を押すとpageの値が変化するのでそれをwatchしてページネーションを起動
        page:function(newValue,oldValue){
            console.log(newValue);
            this.search({
                page    : newValue,
                keyword : this.old.keyword,
            });
        }
    },
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
    },
}
</script>

<style scoped lang="scss">
.content{margin-bottom: 1.2rem;}
.DateLabel{ justify-content: flex-end; }
.elements{
    display: grid;
    grid-template-rows:1fr;
    grid-template-columns:1fr 10fr 1fr 1fr;
    gap:0.5rem;
    background-color: #e1e1e1;
    border:black solid 1px;
    padding: 5px;
    h3{
        margin: auto 0;
        grid-row: 1;
        grid-column: 1/2;
    }
    h2{
        margin: auto 0;
        grid-row: 1;
        grid-column: 2/3;
        word-break   :break-word;
        overflow-wrap:normal;
    }
    .deleteButton{
        width: 100%;
        grid-row: 1;
        grid-column: 3/4;
    }
    .submitButton{
        width: 100%;
        grid-row: 1;
        grid-column: 4/5;
    }
}
</style>


