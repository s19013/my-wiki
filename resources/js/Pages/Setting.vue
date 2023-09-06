<template>
    <BaseLayout :title="messages.title" :pageTitle="messages.title">
        <v-container>
            <v-dialog v-model="dialogFlag">
                <template v-slot:activator="{ props }">
                    <v-btn
                        color="error"
                        class="global_css_haveIconButton_Margin"
                        v-bind="props"
                        flat
                    >
                        <p>{{ messages.withdrawal }}</p>
                    </v-btn>
                </template>
                <section class="global_css_Dialog">
                    <h2>{{ messages.caution }}</h2>
                    <p>{{ messages.message }}</p>
                    <div class="control">
                        <v-btn
                            class="back"
                            :disabled="disabledFlag"
                            :loading="disabledFlag"
                            @click.stop="dialogFlag = false"
                        >
                            <p>{{ messages.cancel }}</p>
                        </v-btn>

                        <Link :href="route('DeleteUser')" method="delete">
                            <v-btn
                                flat
                                :rounded="0"
                                class="delete"
                                color="error"
                            >
                                <p>{{ messages.withdrawal }}</p>
                            </v-btn>
                        </Link>
                    </div>
                </section>
            </v-dialog>
        </v-container>
    </BaseLayout>
</template>

<script>
import BaseLayout from "@/Layouts/BaseLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    data() {
        return {
            japanese: {
                title: "設定",
                withdrawal: "退会",
                caution: "本当に退会しますか",
                message: "メモ､ブックマーク､タグは削除されます(復元できません)",
                cancel: "戻る",
            },
            messages: {
                title: "Setting",
                withdrawal: "withdrawal",
                caution: "do you really want to leave",
                message:
                    "All registered data will be deleted (cannot be restored).",
                cancel: "cancel",
            },
            dialogFlag: false,
        };
    },
    props: {},
    components: {
        BaseLayout,
        Link,
    },
    methods: {},
    watch: {},
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja") {
                this.messages = this.japanese;
            }
        });
    },
};
</script>

<style scoped lang="scss">
.content {
    margin-bottom: 1.2rem;
}
.DateLabel {
    justify-content: flex-end;
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
    .submitButton {
        width: 100%;
        grid-row: 1;
        grid-column: 4/5;
    }
}
</style>
