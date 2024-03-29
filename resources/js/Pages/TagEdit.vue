<template>
    <BaseLayout :title="messages.title" :pageTitle="messages.title">
        <v-container>
            <v-btn
                color="#BBDEFB"
                elevation="2"
                class="createButton"
                @click="openCreateDialog({ type: 'create' })"
            >
                <v-icon>mdi-plus</v-icon>
                <p>{{ messages.createNew }}</p>
            </v-btn>
            <SearchField
                ref="SearchField"
                :searchLabel="messages.search"
                :originalKeyWord="old.keyword"
                @triggerSearch="
                    search({
                        page: 1,
                        keyword: this.$refs.SearchField.serveKeywordToParent(),
                        searchQuantity:
                            this.$refs.SortAndQuantityOption.serveSearchQuantity(),
                        sortType: this.$refs.SortAndQuantityOption.serveSort(),
                    })
                "
            >
            </SearchField>

            <SortAndQuantityOption
                ref="SortAndQuantityOption"
                :oldSearchQuantity="Number(this.old.searchQuantity)"
                :oldSortType="this.old.sortType"
                :sortLabelList="this.messages.sort"
            />

            <div>
                <template v-for="tag of result.data" :key="tag.id">
                    <div class="content">
                        <div class="others">
                            <p>
                                <span>{{ messages.usedCount }}</span
                                >:{{ tag.count }}
                            </p>
                            <DateLabel
                                :createdAt="tag.created_at"
                                :updatedAt="tag.updated_at"
                            />
                        </div>
                        <div class="elements">
                            <h2>{{ tag.name }}</h2>
                            <v-btn
                                color="error"
                                elevation="2"
                                class="deleteButton"
                                @click="openDeleteDialog(tag.id, tag.name)"
                            >
                                <v-icon>mdi-trash-can</v-icon>
                                <p>{{ messages.delete }}</p>
                            </v-btn>
                            <v-btn
                                color="submit"
                                elevation="2"
                                class="updateButton"
                                @click="openUpdateDialog(tag.id, tag.name)"
                            >
                                <v-icon>mdi-pencil-plus</v-icon>
                                <p>{{ messages.edit }}</p>
                            </v-btn>
                        </div>
                    </div>
                </template>
            </div>

            <tagDeleteDialog ref="tagDeleteDialog" />
            <tagFormDialog
                ref="tagCreateDialog"
                type="create"
                @parentLoading="$store.commit('switchGlobalLoading')"
            />
            <tagFormDialog
                ref="tagUpdateDialog"
                type="update"
                @parentLoading="$store.commit('switchGlobalLoading')"
            />

            <PageController
                :page="page"
                :length="result.last_page"
                @clickPre="page -= 1"
                @clickNext="page += 1"
            />
            <v-pagination
                v-model="page"
                :length="result.last_page"
            ></v-pagination>
        </v-container>
        <loadingDialog />
    </BaseLayout>
</template>

<script>
import BaseLayout from "@/Layouts/BaseLayout.vue";
import SearchField from "@/Components/SearchField.vue";
import DateLabel from "@/Components/DateLabel.vue";
import tagDeleteDialog from "@/Components/useOnlyOnce/tagDeleteDialog.vue";
import tagFormDialog from "@/Components/useOnlyOnce/tagFormDialog.vue";
import loadingDialog from "@/Components/dialog/loadingDialog.vue";
import PageController from "@/Components/PageController.vue";
import SortAndQuantityOption from "@/Components/SortAndQuantity.vue";

export default {
    data() {
        return {
            japanese: {
                title: "タグ編集",
                createNew: "新規作成",
                search: "タグ検索",
                edit: "編集",
                delete: "削除",
                usedCount: "使用回数",
                sort: [
                    {
                        label: "更新日 新 → 古",
                        value: "updated_at_desc",
                    },
                    {
                        label: "更新日 古 → 新",
                        value: "updated_at_asc",
                    },
                    {
                        label: "作成日 新 → 古",
                        value: "created_at_desc",
                    },
                    {
                        label: "作成日 古 → 新",
                        value: "created_at_asc",
                    },
                    {
                        label: "タグ名 あ → ん",
                        value: "name_asc",
                    },
                    {
                        label: "タグ名 ん → あ",
                        value: "name_desc",
                    },
                    {
                        label: "使用回数 多 → 少",
                        value: "count_desc",
                    },
                    {
                        label: "使用回数 少 → 多",
                        value: "count_asc",
                    },
                ],
            },
            messages: {
                title: "Edit Tag",
                createNew: "Create New",
                search: "Search Tag",
                edit: "Edit",
                delete: "Delete",
                usedCount: "Used Count",
                sort: [
                    {
                        label: "Updated Date new → old",
                        value: "updated_at_desc",
                    },
                    {
                        label: "Updated Date old → new",
                        value: "updated_at_asc",
                    },
                    {
                        label: "Created Date new → old",
                        value: "created_at_desc",
                    },
                    {
                        label: "Created Date old → new",
                        value: "created_at_asc",
                    },
                    {
                        label: "Tag Name A → Z",
                        value: "title_asc",
                    },
                    {
                        label: "Tag Name Z → A",
                        value: "title_desc",
                    },
                    {
                        label: "Used Count Most → Less",
                        value: "count_desc",
                    },
                    {
                        label: "Used Count Less → Most",
                        value: "count_asc",
                    },
                ],
            },
            page: this.result.current_page,
        };
    },
    props: {
        result: {
            type: Object,
        },
        old: {
            type: Object,
        },
    },
    components: {
        BaseLayout,
        SearchField,
        DateLabel,
        tagDeleteDialog,
        tagFormDialog,
        loadingDialog,
        PageController,
        SortAndQuantityOption,
    },
    methods: {
        // 検索用
        search({ page, keyword, searchQuantity, sortType }) {
            this.$store.commit("switchGlobalLoading");
            this.$inertia.get("/Tag/Edit/Search", {
                page: page,
                keyword: keyword,
                searchQuantity: searchQuantity,
                sortType: sortType,
                onError: (errors) => {
                    console.log(errors);
                    this.$store.commit("switchGlobalLoading");
                },
            });
        },
        // 削除ダイアログ開く
        openDeleteDialog(id, name) {
            this.$refs.tagDeleteDialog.setter(id, name);
            this.$refs.tagDeleteDialog.dialogFlagSwitch();
        },
        // 作成ダイアログ開く
        openCreateDialog(id, name) {
            this.$refs.tagCreateDialog.setIdAndName(id, name);
            this.$refs.tagCreateDialog.dialogFlagSwitch();
        },
        // 更新ダイアログ開く
        openUpdateDialog(id, name) {
            this.$refs.tagUpdateDialog.setIdAndName(id, name);
            this.$refs.tagUpdateDialog.dialogFlagSwitch();
        },
        keyEvents(event) {
            if (
                this.$store.state.globalLoading === false &&
                this.$store.state.someDialogOpening === false
            ) {
                if (event.ctrlKey || event.key === "Meta") {
                    // 送信
                    if (event.code === "Enter") {
                        this.search({
                            page: 1,
                            keyword:
                                this.$refs.SearchField.serveKeywordToParent(),
                            searchTarget: this.searchTarget,
                            searchQuantity:
                                this.$refs.SortAndQuantityOption.serveSearchQuantity(),
                            sortType:
                                this.$refs.SortAndQuantityOption.serveSort(),
                        });
                        return;
                    }

                    // 新規作成
                    if (
                        (event.ctrlKey || event.key === "Meta") &&
                        event.altKey &&
                        event.code === "KeyN"
                    ) {
                        event.preventDefault();
                        this.$refs.tagCreateDialog.dialogFlagSwitch();
                    }

                    // ページめくり
                    if (event.key === "ArrowRight") {
                        this.pageIncrease();
                        return;
                    }

                    // ページめくり
                    if (event.key === "ArrowLeft") {
                        this.pageDecrease();
                        return;
                    }
                }
            }
        },
        pageIncrease() {
            if (this.page < this.result.last_page) {
                this.page += 1;
            }
        },
        pageDecrease() {
            if (this.page > 1) {
                this.page -= 1;
            }
        },
    },
    watch: {
        // @input="pagination"でできるはずなのにできないのでwatchで対応
        // ページネーションのボタン類を押した場合の処理
        // 厳密にはページネーションのボタン類を押すとpageの値が変化するのでそれをwatchしてページネーションを起動
        page: function (newValue, oldValue) {
            console.log(newValue);
            this.search({
                page: newValue,
                keyword: this.old.keyword,
                searchQuantity: this.old.searchQuantity,
                sortType: this.old.sortType,
            });
        },
    },
    mounted() {
        //キーボード受付
        document.addEventListener("keydown", this.keyEvents);

        this.$store.commit("setGlobalLoading", false);
        this.$store.commit("setSomeDialogOpening", false);
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja") {
                this.messages = this.japanese;
            }
        });
    },
    beforeUnmount() {
        //キーボードによる動作の削除(副作用みたいエラーがでるため)
        document.removeEventListener("keydown", this.keyEvents);
    },
};
</script>

<style scoped lang="scss">
.content {
    margin-bottom: 1.2rem;
}
.elements {
    display: grid;
    grid-template-rows: 1fr;
    grid-template-columns: 1fr 10fr 1fr 1fr;
    gap: 0.5rem;
    background-color: #e1e1e1;
    border: black solid 1px;
    padding: 5px;
    h3 {
        margin: auto 0;
        grid-row: 1;
        grid-column: 1/2;
    }
    h2 {
        margin: auto 0;
        grid-row: 1;
        grid-column: 2/3;
        word-break: break-word;
        overflow-wrap: normal;
    }
    .deleteButton {
        width: 100%;
        grid-row: 1;
        grid-column: 3/4;
    }
    .updateButton {
        width: 100%;
        grid-row: 1;
        grid-column: 4/5;
    }
}
.others {
    span {
        font-weight: bold;
    }
    p {
        font-size: 0.8rem;
        text-align: right;
    }
    .DateLabel {
        justify-content: flex-end;
    }
}

@media (min-width: 440px) {
    .others {
        display: flex;
        justify-content: flex-end;
        word-break: break-word;
        overflow-wrap: normal;
        gap: 0.6rem;
    }
}
</style>
