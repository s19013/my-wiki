<template>
    <div class="SearchOption">

        <SelectComponent
            class="searchQuantity"
            ref="searchQuantity"
            :selected="oldSearchQuantity"
            :label="messages.searchQuantity"
            :list="[2,5,10,20,30,40,50]"
         />

         <SelectComponent
            class="sort"
            ref="sort"
            :selected="sortSelected"
            :label="messages.sortLabel"
            :list="messages.sort"
            :object="true"

         />
    </div>
</template>

<script>
import SelectComponent from './SelectComponent.vue'
export default{
    data() {
        return {
            japanese:{
                searchQuantity:"検索数",
                sortLabel:"並び順",
                sort:[
                    {
                        label:"更新日 新 → 古",
                        value:"updated_at_desc"
                    },
                    {
                        label:"更新日 古 → 新",
                        value:"updated_at_asc"
                    },
                    {
                        label:"作成日 新 → 古",
                        value:"created_at_desc"
                    },
                    {
                        label:"作成日 古 → 新",
                        value:"created_at_asc"
                    },
                    {
                        label:"タイトル あ → ん",
                        value:"title_asc"
                    },
                    {
                        label:"タイトル ん → あ",
                        value:"title_desc"
                    },
                    {
                        label:"閲覧数 多 → 少",
                        value:"count_desc"
                    },
                    {
                        label:"閲覧数 少 → 多",
                        value:"count_asc"
                    },
                    {
                        label:"ランダム",
                        value:"random"
                    },
                ]
            },
            messages:{
                searchQuantity:"search quantity",
                sortLabel:"sort",
                sort:[
                    {
                        label:"Updated Date new → old",
                        value:"updated_at_desc"
                    },
                    {
                        label:"Updated Date old → new",
                        value:"updated_at_asc"
                    },
                    {
                        label:"Created Date new → old",
                        value:"created_at_desc"
                    },
                    {
                        label:"Created Date old → new",
                        value:"created_at_asc"
                    },
                    {
                        label:"Title A → Z",
                        value:"title_asc"
                    },
                    {
                        label:"Title Z → A",
                        value:"title_desc"
                    },
                    {
                        label:"Views Most → Less",
                        value:"count_desc"
                    },
                    {
                        label:"Views Less → Most",
                        value:"count_asc"
                    },
                    {
                        label:"Random",
                        value:"random"
                    },
                ]
            },
            searchQuantity:this.oldSearchQuantity, //後でoldにする
            sortSelected:{label:"updated desc",value:"updated_at_desc"},
        }
    },
    components:{SelectComponent},
    props:{
        oldSearchQuantity:{
            type: Number,
            default:10
        },
        oldSortType:{
            type:String,
            default:"updated_at_desc"
        }
    },
    methods: {
        serveSearchQuantity(){return this.$refs.searchQuantity.serveSelected()},
        serveSort(){return (this.$refs.sort.serveSelected()).value},
        setSort(){
            // 引数はvalueだけだが{label:,value:}の形でないとmodelが反応しないため{label:,value:}にして返す
            return this.messages.sort.find(element => element.value == this.oldSortType);
        }
    },
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){Object.assign(this.messages,this.japanese)}
            this.sortSelected = this.setSort()

            // タイミングの問題でpropsからではなくsetterで初期値を入れる
            this.$refs.searchQuantity.setSelected(this.oldSearchQuantity)
            this.$refs.sort.setSelected(this.sortSelected)
        })
    },
}

</script>

<style scoped lang="scss">
.SelectComponent{
    margin-bottom:1rem;
}
.SearchOption{
    display: flex;
    flex-wrap: wrap;
    gap:1rem;
}
</style>
