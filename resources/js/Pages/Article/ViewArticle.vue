<template>
    <BaseLayout :title="article.title" :pageTitle="messages.title">
        <div class="articleContainer">
            <!-- タイトルとボタン2つ -->
            <div class="head">
                <DeleteAlertComponent
                    ref="deleteAlert"
                    type="bookmark"
                    @deleteTrigger="deleteArticle"
                />
                <Link :href="'/Article/Edit/' + article.id">
                    <v-btn
                        class="editButton global_css_haveIconButton_Margin"
                        color="#BBDEFB"
                        @click="this.$store.commit('switchGlobalLoading')"
                    >
                        <v-icon>mdi-pencil-plus</v-icon>
                        <p>{{ messages.button }}</p>
                    </v-btn>
                </Link>
            </div>

            <DateLabel
                :createdAt="article.created_at"
                :updatedAt="article.updated_at"
            />
            <!-- タグ -->
            <TagList
                :tagList="articleTagList"
                :text="messages.tagList"
                :cannotDelete="true"
            />

            <h1 class="title">{{ article.title }}</h1>

            <!-- md表示 -->
            <CompiledMarkDown ref="compiled" />

            <loadingDialog />
        </div>
    </BaseLayout>
</template>

<script>
import DeleteAlertComponent from "@/Components/dialog/DeleteAlertDialog.vue";
import CompiledMarkDown from "@/Components/article/CompiledMarkDown.vue";
import TagList from "@/Components/TagList.vue";
import DateLabel from "@/Components/DateLabel.vue";
import loadingDialog from "@/Components/dialog/loadingDialog.vue";
import BaseLayout from "@/Layouts/BaseLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import axios from "axios";

export default {
    data() {
        return {
            japanese: {
                title: "記事観覧",
                button: "編集",
                tagList: "付けたタグ",
            },
            messages: {
                title: "Browse article",
                button: "Edit",
                tagList: "Attached Tag",
            },
        };
    },
    props: ["article", "articleTagList"],
    components: {
        DeleteAlertComponent,
        TagList,
        DateLabel,
        CompiledMarkDown,
        loadingDialog,
        BaseLayout,
        Link,
    },
    methods: {
        deleteArticle() {
            this.$store.commit("switchGlobalLoading");
            // 消す処理
            axios
                .delete("/api/article/" + this.article.id)
                .then((res) => {
                    this.$inertia.get("/Article/Search");
                })
                .catch((errors) => {
                    this.$store.commit("switchGlobalLoading");
                    console.log(errors);
                });
        },
        keyEvents(event) {
            // ダイアログが開いている時,読み込み中には呼ばせない
            if (
                this.$store.state.globalLoading === false &&
                this.$store.state.someDialogOpening === false
            ) {
                // 削除ダイアログ呼び出し
                if (event.key === "Delete") {
                    this.$refs.deleteAlert.deleteDialogFlagSwitch();
                    return;
                }

                if (event.ctrlKey || event.key === "Meta") {
                    // 送信
                    if (event.code === "Enter") {
                        this.$store.commit("switchGlobalLoading");
                        this.$inertia.get("/Article/Edit/" + this.article.id);
                    }
                    return;
                }
            }
        },
    },
    mounted() {
        //キーボード受付
        document.addEventListener("keydown", this.keyEvents);

        this.$store.commit("setGlobalLoading", false);

        // 変換したマークダウンを表示させとく
        this.$refs.compiled.compileMarkDown(this.article.body);

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

<style lang="scss" scoped>
.articleContainer {
    margin: 0 1rem;
    margin-top: 1rem;
    @media (max-width: 900px) {
        margin-top: 2rem;
    }
}
.title {
    padding: 2px;
    border: black solid 1px;
}

.head {
    display: grid;
    grid-template-columns: 9fr auto auto;
    gap: 2rem;
    .deleteAlertDialog {
        grid-column: 2/3;
    }
    .saveButton {
        margin-left: 1rem;
        grid-column: 3/4;
    }
}
.CompiledMarkDown {
    margin: 1rem 0;
    @media (max-width: 600px) {
        margin: 0.2rem;
    }
}
.DateLabel {
    margin: 0.5rem 0;
    justify-content: flex-start;
}
</style>
