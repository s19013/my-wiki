<template>
    <div class="SearchOption">
        <SelectComponent
            class="searchQuantity"
            ref="searchQuantity"
            :selected="oldSearchQuantity"
            :label="messages.searchQuantity"
            :list="searchQuantityLabelList"
        />

        <SelectComponent
            class="sort"
            ref="sort"
            :selected="sortSelected"
            :label="messages.sortLabel"
            :list="sortLabelList"
            :object="true"
        />
    </div>
</template>

<script>
import SelectComponent from "./SelectComponent.vue";
export default {
    data() {
        return {
            japanese: {
                searchQuantity: "検索数",
                sortLabel: "並び順",
            },
            messages: {
                searchQuantity: "search quantity",
                sortLabel: "sort",
            },
            searchQuantity: this.oldSearchQuantity,
            searchQuantityLabelList: [
                { label: "10", value: 10 },
                { label: "20", value: 20 },
                { label: "30", value: 30 },
                { label: "40", value: 40 },
                { label: "50", value: 50 },
            ],
            sortSelected: this.oldSortType,
        };
    },
    components: { SelectComponent },
    props: {
        oldSearchQuantity: {
            type: Number,
            default: 10,
        },
        oldSortType: {
            type: String,
            default: "updated_at_desc",
        },
        sortLabelList: {
            type: Array,
            default: [],
        },
    },
    methods: {
        serveSearchQuantity() {
            return this.$refs.searchQuantity.serveLocalSelected();
        },
        serveSort() {
            return this.$refs.sort.serveLocalSelected();
        },
    },
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja") {
                Object.assign(this.messages, this.japanese);
            }
        });
    },
};
</script>

<style scoped lang="scss">
.SelectComponent {
    margin-bottom: 1rem;
}
.SearchOption {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}
</style>
