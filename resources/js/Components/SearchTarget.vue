<template>
    <div class="SearchTarget">
        <p v-if="this.$store.state.lang == 'ja'">検索対象</p>
        <p v-else>Search Target</p>
        <div class="options">
            <div class="option" v-for="(item, i) in radioItems" :key="i">
                <input
                    type="radio"
                    :id="'searchTarget_' + item.label"
                    :value="item.value"
                    v-model="target"
                />
                <label
                    :for="'searchTarget_' + item.label"
                    v-bind:class="{ selected: target == item.value }"
                >
                    {{ item.label }}
                </label>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            target: this.radioDefault,
        };
    },
    props: {
        radioItems: {
            type: Object,
            default: [
                {
                    value: "value",
                    label: "label",
                },
            ],
            required: true,
        },
        radioDefault: {
            type: String,
            default: "",
            required: true,
        },
    },
    methods: {
        //親にチェックリストを渡す
        serveTarget() {
            return this.target;
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
.SearchTarget {
    .options {
        display: flex;
        gap: 1rem;
        .option {
            width: fit-content;
            input {
                margin-right: 0.2rem;
            }
        }
    }
}
.selected {
    font-weight: bold;
}
</style>
