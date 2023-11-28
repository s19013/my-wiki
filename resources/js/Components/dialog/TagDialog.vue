<template>
    <div class="TagDialog" data-testid="tagDialog">
        <!-- ダイアログを呼び出すためのボタン -->
        <div class="buttonAndList">
            <v-btn
                color="submit"
                size="small"
                class="global_css_haveIconButton_Margin"
                data-testid="tagDialogOpenButton"
                :disabled="disabled"
                @click.stop="openTagDialog()"
            >
                <v-icon>mdi-tag</v-icon>
                <p>{{ messages.tag }}</p>
            </v-btn>
            <TagList
                :tagList="checkedTagList"
                :text="text"
                :disabled="disabled"
                @popTag="popTag"
            />
        </div>

        <!-- v-modelがv-ifとかの代わりになっている -->
        <v-dialog v-model="tagDialogFlag" scrollable persistent>
            <section class="global_css_Dialog tagDialog">
                <div class="clooseButton" data-testid="tagDialogCloseButton">
                    <v-btn
                        color="#E57373"
                        size="small"
                        :disabled="disableFlag"
                        :loading="disableFlag"
                        elevation="2"
                        @click.stop="closeTagDialog()"
                    >
                        <v-icon>mdi-close-box</v-icon>
                        <p>{{ messages.close }}</p>
                    </v-btn>
                </div>

                <SearchField
                    ref="SearchField"
                    :searchLabel="messages.search"
                    :disabled="disableFlag"
                    :loadingFlag="disableFlag"
                    @triggerSearch="searchBranch"
                >
                </SearchField>

                <!-- 操作ボタン -->
                <div class="control">
                    <div class="existCheckbox" data-testid="existCheckbox">
                        <!-- この部分を既存チェックボックスという -->
                        <input
                            type="checkbox"
                            id="checked"
                            v-model="onlyCheckedFlag"
                        />
                        <label for="checked">{{ messages.checked }}</label>
                    </div>
                    <v-btn
                        data-testid="clearAllCheck"
                        variant="outlined"
                        color="primary"
                        size="small"
                        v-show="onlyCheckedFlag"
                        :disabled="disableFlag"
                        :loading="disableFlag"
                        @click.stop="clearAllCheck"
                    >
                        <p>{{ messages.uncheck }}</p>
                    </v-btn>
                </div>

                <!-- loadingアニメ -->
                <loading v-show="disableFlag"></loading>

                <!-- タグ一覧 -->
                <v-list
                    data-testid="tagDialogSearchResult"
                    class="overflow-y-auto mx-auto"
                    width="100%"
                    v-show="!disableFlag"
                    max-height="45vh"
                >
                    <v-list-item
                        v-for="tag of tagSearchResultList"
                        :key="tag.id"
                    >
                        <input
                            type="checkbox"
                            :id="tag.id"
                            v-model="checkedTagList"
                            :value="{ id: tag.id, name: tag.name }"
                        />
                        <label :for="tag.id">{{ tag.name }}</label>
                    </v-list-item>
                </v-list>

                <!--  -->
                <div v-if="!searchOnly" data-testid="createNewTag">
                    <v-btn
                        class="global_css_haveIconButton_Margin my-4 global_css_longButton"
                        color="submit"
                        size="small"
                        v-show="!createNewTagFlag"
                        :disabled="disableFlag"
                        :loading="disableFlag"
                        @click.stop="createNewTagFlagSwitch"
                    >
                        <v-icon>mdi-tag-plus</v-icon>
                        <p>{{ messages.make }}</p>
                    </v-btn>

                    <!-- 新規タグ作成 -->
                    <div class="areaCreateNewTag" v-show="createNewTagFlag">
                        <p
                            v-show="errorMessages.name.length > 0"
                            v-for="message of errorMessages.name"
                            :key="message"
                            class="global_css_error"
                        >
                            <v-icon>mdi-alert-circle-outline</v-icon>
                            {{ message }}
                        </p>

                        <v-form v-on:submit.prevent="createNewTag">
                            <v-text-field
                                data-testid="createNewTagInput"
                                v-model="newTag"
                                :label="messages.tagName"
                                outlined
                                hide-details="false"
                                :disabled="disableFlag"
                                :loading="disableFlag"
                            >
                            </v-text-field>
                        </v-form>

                        <v-btn
                            class="global_css_haveIconButton_Margin global_css_longButton"
                            color="#BBDEFB"
                            size="small"
                            elevation="2"
                            :disabled="disableFlag"
                            :loading="disableFlag"
                            @click.stop="createNewTag()"
                        >
                            <v-icon>mdi-content-save</v-icon>
                            <p>{{ messages.create }}</p>
                        </v-btn>
                    </div>
                </div>
            </section>
        </v-dialog>
    </div>
</template>

<script>
import loading from "@/Components/loading/loading.vue";
import SearchField from "@/Components/SearchField.vue";
import TagList from "@/Components/TagList.vue";

import MakeListTools from "@/tools/MakeListTools.js";
import { sortArrayByName } from "@/tools/SortOption.js";

const makeListTools = new MakeListTools();
export default {
    data() {
        return {
            japanese: {
                tag: "タグ",
                close: "閉じる",
                search: "タグ検索",
                checked: "チェックを付けたタグだけを表示",
                uncheck: "チェックをすべてはずす",
                make: "新規作成",
                tagName: "タグ名",
                create: "作成",
            },
            messages: {
                tag: "tag",
                close: "close",
                search: "Tag search",
                checked: "Show only checked tags",
                uncheck: "uncheck all",
                make: "Create New",
                tagName: "タグ名",
                create: "create",
            },
            newTag: "",

            // flag
            onlyCheckedFlag: false,
            createNewTagFlag: false,
            tagDialogFlag: false,
            isFirstSearchFlag: true,

            //loding
            disableFlag: false,

            // errorFlag
            errorMessages: { name: [] },

            // tagList
            checkedTagList: [],
            tagSearchResultList: [],
            tagCacheList: [], //全件検索のキャッシュ
            allTagCacheList: [], //全件検索のキャッシュ
        };
    },
    props: {
        originalCheckedTagList: {
            //更新や閲覧画面で既にチェックがついているタグを受け取るため
            type: Array,
            default: [],
        },
        searchOnly: {
            //記事検索などでは新規作成を表示させないようにするため
            type: Boolean,
            default: false,
        },
        text: {
            type: String,
            default: "つけたタグ",
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    components: {
        loading,
        SearchField,
        TagList,
    },
    methods: {
        // 新規タグ作成
        async createNewTag() {
            //ローディングアニメ開始
            this.disableFlag = true;
            await axios
                .post("/api/tag/store", { name: this.newTag })
                .then((res) => {
                    //検索欄をリセット
                    this.$refs.SearchField.resetKeyword();
                    this.resetError();

                    // 読み込み直し
                    this.isFirstSearchFlag = true;
                    this.searchAllTag();

                    // 入力欄を消す
                    this.createNewTagFlag = false;
                    this.newTag = null;
                })
                .catch((errors) => {
                    this.errorMessages = errors.response.data.messages;
                });
            this.disableFlag = false;
        },
        // タグ検索
        searchBranch() {
            if (this.$refs.SearchField.serveKeywordToParent() == "") {
                //初期ローディング以外の全件検索だったらキャッシュを使う
                if (this.isFirstSearchFlag == false) {
                    this.tagSearchResultList = this.allTagCacheList;
                    this.tagCacheList = this.allTagCacheList;
                }
                // 初期ローディング､更新後の全件検索
                else {
                    this.searchAllTag();
                }
            }
            // 他の検索
            else {
                this.searchTag();
            }
        },
        // 全件検索
        async searchAllTag() {
            //ローディングアニメ開始
            this.disableFlag = true;

            //既存チェックボックスのチェックを外す
            this.onlyCheckedFlag = false;

            //配列,キャッシュ初期化
            this.tagSearchResultList = [];
            this.tagCacheList = []; //キャッシュをクリアするのは既存チェックボックスを外す時に出てくるバグを防ぐため

            await axios
                .post("/api/tag/search", { keyword: "" })
                .then((res) => {
                    for (const tag of res.data) {
                        this.tagSearchResultList.push({
                            id: tag.id,
                            name: tag.name,
                        });
                    }
                    //キャッシュにコピー
                    this.allTagCacheList = [...this.tagSearchResultList];
                    this.tagCacheList = [...this.tagSearchResultList];
                })
                .catch((err) => {
                    console.log(err);
                });

            //ローディングアニメ解除
            this.disableFlag = false;

            //初期ローディングフラグを切る
            this.isFirstSearchFlag = false;
        },
        // タグ検索
        async searchTag() {
            //ローディングアニメ開始
            this.disableFlag = true;

            //既存チェックボックスのチェックを外す
            this.onlyCheckedFlag = false;

            //配列,キャッシュ初期化
            this.tagSearchResultList = [];
            this.tagCacheList = []; //キャッシュをクリアするのは既存チェックボックスを外す時に出てくるバグを防ぐため
            await axios
                .post("/api/tag/search", {
                    keyword: this.$refs.SearchField.serveKeywordToParent(),
                })
                .then((res) => {
                    for (const tag of res.data) {
                        this.tagSearchResultList.push({
                            id: tag.id,
                            name: tag.name,
                        });
                    }
                    //キャッシュにコピー
                    this.tagCacheList = [...this.tagSearchResultList];
                })
                .catch((err) => {
                    console.log(err);
                });

            //ローディングアニメ解除
            this.disableFlag = false;
        },
        // タグ削除
        popTag(i) {
            this.checkedTagList.splice(i, 1);
        },
        //チェック全消し
        clearAllCheck() {
            this.checkedTagList = [];
        },
        // エラーをリセット
        resetError() {
            this.errorMessages = { name: [] };
        },
        // 切り替え
        createNewTagFlagSwitch() {
            this.createNewTagFlag = !this.createNewTagFlag;
        },
        tagDialogFlagSwithch() {
            this.$store.commit("switchSomeDialogOpening");
            this.tagDialogFlag = !this.tagDialogFlag;
        },
        //
        openTagDialog() {
            this.tagDialogFlagSwithch();
            this.searchAllTag();
        },
        closeTagDialog() {
            this.tagDialogFlagSwithch();
            // 新規登録の入力欄を消す
            this.createNewTagFlag = false;
            this.newTag = "";

            //既存チェックのチェックを外す
            this.onlyCheckedFlag = false;

            // チェックをつけたタグをソード
            this.checkedTagList = this.checkedTagList.sort(sortArrayByName);

            this.resetError();

            this.$emit("closedTagDialog", this.checkedTagList);
        },
        //親にチェックリストを渡す
        serveCheckedTagList() {
            return makeListTools.tagIdList(this.checkedTagList);
        },
        keyEvents(event) {
            //ダイアログが開いている時有効にする
            if (this.tagDialogFlag == true) {
                if (event.key === "Escape") {
                    this.tagDialogFlagSwithch();
                    return;
                }
            }
        },
    },
    watch: {
        onlyCheckedFlag: function () {
            //チェックをつけた場合
            if (this.onlyCheckedFlag == true) {
                //チェックがついているタグだけを表示
                this.checkedTagList.sort(sortArrayByName);
                this.tagSearchResultList = this.checkedTagList;
            } else if (
                this.onlyCheckedFlag == false &&
                this.tagDialogFlag == true
            ) {
                //全タグのキャッシュに置き換える
                //参照元を変えるだけなので読み込みが早い
                this.tagSearchResultList = this.tagCacheList;
            }
        },
    },
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja") {
                this.messages = this.japanese;
            }
        });
        //originalCheckedTagListの中が完全に空ではなかったら代入
        if (this.originalCheckedTagList.length != 0) {
            // idがnullのデータ(ユーザーがget通信でurlを変にいじったら起きる)の時の処理
            for (
                let index = 0;
                index < this.originalCheckedTagList.length;
                index++
            ) {
                if (this.originalCheckedTagList[index].id == null) {
                    this.originalCheckedTagList.splice(index);
                }
            }

            this.checkedTagList = this.originalCheckedTagList;
        }

        //キーボード受付
        document.addEventListener("keydown", this.keyEvents);
    },
    beforeUnmount() {
        //キーボードによる動作の削除(副作用みたいエラーがでるため)
        document.removeEventListener("keydown", this.keyEvents);
    },
};
</script>

<style lang="scss" scoped>
.tagDialog {
    label {
        margin-left: 0.5rem;
        width: 100%;
    }
    .clooseButton {
        margin-bottom: 0.5rem;
    }
    .existCheckbox {
        label {
            font-size: 1.1rem;
        }
        margin: 0.5rem 0;
    }
    .v-list {
        padding: 0;
        .v-list-item {
            padding: 0 0.5rem;
        }
    }
    .areaCreateNewTag {
        margin: 1rem 0 0.5rem 0;
        button {
            margin-top: 0.8rem;
        }
    }
}

@media (min-width: 601px) {
    .control {
        display: grid;
        grid-template-rows: 1fr;
        grid-template-columns: 1fr 0.1fr 1fr;
        .existCheckbox {
            grid-row: 1;
            grid-column: 1/2;
        }
        .v-btn {
            grid-row: 1;
            grid-column: 3/4;
        }
    }
    .v-list-item {
        font-size: 1.2rem;
    }
    .buttonAndList {
        display: grid;
        grid-template-rows: auto auto;
        grid-template-columns: 5fr 1fr;
        .tagList {
            grid-row: 1/3;
            grid-column: 1/2;
        }
        button {
            grid-row: 1/3;
            grid-column: 2/3;
        }
    }
}

@media (max-width: 600px) {
    .tagDialog {
        .control .v-btn {
            margin: 0.5rem 0;
        }
        .v-list-item {
            font-size: 1.4rem;
        }
    }
    .buttonAndList {
        display: grid;
        grid-template-rows: auto auto;
        .tagList {
            grid-row: 2/3;
        }
        button {
            margin-bottom: 1rem;
            grid-row: 1/2;
        }
    }
}
</style>
