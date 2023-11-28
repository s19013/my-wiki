<template>
    <div class="content" data-testid="bookmarkContainer">
        <!-- 別タブで開くようにする -->
        <div class="others">
            <p>
                <span>{{ messages.count }}</span
                >:{{ bookMark.count }}
            </p>
            <DateLabel
                :createdAt="bookMark.created_at"
                :updatedAt="bookMark.updated_at"
            />
        </div>
        <div class="elements">
            <a
                :href="bookMark.url"
                target="_blank"
                rel="noopener noreferrer"
                @click="countup(bookMark.id)"
            >
                <h3>
                    <v-icon>mdi-arrow-top-left-bold-box-outline</v-icon>
                    {{ bookMark.title }}
                </h3>
            </a>
            <Link :href="'/BookMark/Edit/' + bookMark.id">
                <v-btn color="submit" elevation="2" size="small">
                    {{ messages.button }}
                </v-btn>
            </Link>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import DateLabel from "@/Components/DateLabel.vue";
export default {
    data() {
        return {
            japanese: {
                button: "編集",
                count: "閲覧数",
            },
            messages: {
                button: "Edit",
                count: "count",
            },
        };
    },
    components: {
        Link,
        DateLabel,
    },
    props: {
        bookMark: { type: Object },
    },
    methods: {
        // 今回は待たなくて良い
        countup(bookMarkId) {
            axios
                .get("/api/bookmark/countup/" + bookMarkId)
                .then((res) => {})
                .catch((errors) => {});
        },
    },
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
.others {
    span {
        font-weight: 500;
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

.elements {
    display: grid;
    grid-template-columns: 10fr 1fr;
    gap: 0.5rem;
    background-color: #e1e1e1;
    border: black solid 1px;
    padding: 5px;
    i {
        float: left;
    }
    h3 {
        // @media (min-width: 420px){font-size: 1.3rem;}
        font-size: 1.3rem;
        margin: auto 0;
        grid-column: 1/2;
        word-break: break-word;
        overflow-wrap: normal;
    }
    button {
        width: 100%;
        grid-column: 2/3;
    }
}
</style>
