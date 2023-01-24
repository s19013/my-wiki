<template>
    <BaseLayout :title="messages.title" :pageTitle="messages.title">
        <v-container>
            <v-btn
            color="#BBDEFB" elevation="2" class="createButton" @click="openCreateDialog({type:'create'})"
            :disabled = "loading" :loading  = "loading">
                <v-icon>mdi-plus</v-icon>
                <p>{{ messages.createNew }}</p>
            </v-btn>
            <SearchField
                ref = "SearchField"
                :searchLabel   ="messages.search"
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
            <p>({{ messages.usedCount }})</p>
            <div v-show="!loading">
                <template v-for="tag of result.data" :key="tag.id">
                    <div class ="content">
                        <DateLabel :createdAt="tag.created_at" :updatedAt="tag.updated_at"/>
                        <div class="elements">
                            <h3>({{tag.count}})</h3>
                            <h2>{{tag.name}}</h2>
                            <v-btn color="error" elevation="2"
                            class="deleteButton" @click="openDeleteDialog(tag.id,tag.name)">
                                <v-icon>mdi-trash-can</v-icon>
                                <p>{{ messages.delete }}</p>
                            </v-btn>
                            <v-btn color="submit" elevation="2"
                            class="updateButton" @click="openUpdateDialog(tag.id,tag.name)">
                                <v-icon>mdi-pencil-plus</v-icon>
                                <p>{{ messages.edit }}</p>
                            </v-btn>
                        </div>
                    </div>
                </template>
            </div>
            <tagDeleteDialog ref = "tagDeleteDialog" @parentLoading="switchLoading()"/>
            <tagFormDialog   ref = "tagCreateDialog" type="create" @parentLoading="switchLoading()"/>
            <tagFormDialog   ref = "tagUpdateDialog" type="update" @parentLoading="switchLoading()"/>
        <v-pagination
            v-model="page"
            :length="result.last_page"
            :disabled = "loading"
        ></v-pagination>
        </v-container>
        <loadingDialog :loadingFlag="loading"/>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/BaseLayout.vue'
import loading from '@/Components/loading/loading.vue'
import SearchField from '@/Components/SearchField.vue'
import DateLabel from '@/Components/DateLabel.vue';
import tagDeleteDialog from '@/Components/useOnlyOnce/tagDeleteDialog.vue'
import tagFormDialog from '@/Components/useOnlyOnce/tagFormDialog.vue'
import loadingDialog from '@/Components/dialog/loadingDialog.vue';

export default{
    data() {
        return {
            japanese:{
                title:"タグ編集",
                createNew:"新規作成",
                search:"タグ検索",
                edit:"編集",
                delete:"削除",
                usedCount:"使用回数"
            },
            messages:{
                title:"Edit Tag",
                createNew:"Create New",
                search:"Search Tag",
                edit:"Edit",
                delete:"Delete",
                usedCount:"Used Count"
            },
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
        tagFormDialog,
        loadingDialog,
    },
    methods: {
        switchLoading(){this.loading = !this.loading},
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
        // 削除ダイアログ開く
        openDeleteDialog(id,name){
            this.$refs.tagDeleteDialog.setter(id,name)
            this.$refs.tagDeleteDialog.dialogFlagSwitch()
        },
        // 作成ダイアログ開く
        openCreateDialog(id,name){
            this.$refs.tagCreateDialog.setIdAndName(id,name)
            this.$refs.tagCreateDialog.dialogFlagSwitch()
        },
        // 更新ダイアログ開く
        openUpdateDialog(id,name){
            this.$refs.tagUpdateDialog.setIdAndName(id,name)
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
    .updateButton{
        width: 100%;
        grid-row: 1;
        grid-column: 4/5;
    }
}
</style>
