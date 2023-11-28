<template>
    <div class="PageController">
        <v-btn
            color="#205979"
            @click="clickPre()"
            :disabled="page <= 1"
            class="pre"
            data-testid="preButton"
        >
            <v-icon>mdi-arrow-left-bold</v-icon>
            {{ messages.pre }}
        </v-btn>
        <v-btn
            color="#a5d7f3"
            @click="clickNext()"
            :disabled="page >= length"
            class="next"
            data-testid="nextButton"
        >
            {{ messages.next }}
            <v-icon>mdi-arrow-right-bold</v-icon>
        </v-btn>
    </div>
</template>

<script>
export default {
    props: {
        page: {
            type: Number,
        },
        length: {
            type: Number,
        },
    },
    data() {
        return {
            messages: {
                next: "next",
                pre: "previous",
            },
            japanese: {
                next: "次",
                pre: "前",
            },
        };
    },
    methods: {
        clickPre() {
            this.$emit("clickPre");
        },
        clickNext() {
            this.$emit("clickNext");
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

<style lang="scss" scoped>
.PageController {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin: 2rem 0;
    .v-btn {
        width: 100%;
        height: 3rem;
        font-size: 1.5rem;
    }
    .pre {
        color: rgb(255, 255, 255);
    }
    .next {
        color: rgb(0, 0, 0);
    }
}
</style>
