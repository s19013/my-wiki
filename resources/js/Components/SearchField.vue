<template>
    <div>
        <v-form v-on:submit.prevent ="triggerSearch()">
            <v-text-field
                v-model="keyword"
                :label ="searchLabel"
                outlined hide-details="false"
                clearable
            ></v-text-field>
            <v-btn color="submit"
                elevation="2"
                :disabled = "loadingFlag"
                @click.stop="triggerSearch()">
                <v-icon>mdi-magnify</v-icon>
                検索
            </v-btn>
        </v-form>
    </div>
</template>

<script>
export default{
    data() {
        return {
            keyword:""
        }
    },
    props:{
        searchLabel:{
            type:String,
            default:"検索"
        },
        loadingFlag:{
            type:Boolean,
            default:false,
        }
    },
    methods: {
        triggerSearch(){this.$emit('triggerSearch')},
        serveKeywordToParent(){return this.keyword},
        resetKeyword(){this.keyword=""}
    },
}

</script>

<style scoped lang="scss">
form{
    display:grid;
    grid-template-columns:5fr 1fr;
    margin-top: 10px;
    margin-bottom: 20px;
    .v-input{grid-column: 1/2;}
    .v-btn  {
        grid-column: 2/3;
        margin-left: 1rem;
        width:90%;
    }
}

</style>
