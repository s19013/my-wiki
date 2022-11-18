<template>
    <div class="searchField">
        <v-form v-on:submit.prevent ="triggerSearch()">
            <v-text-field
                v-model="keyword"
                :label ="searchLabel"
                outlined hide-details="false"
                :loading = "loadingFlag"
                :disabled = "loadingFlag"
                clearable
            ></v-text-field>
            <v-btn color="submit"
                class="global_css_haveIconButton_Margin"
                elevation="2"
                :loading = "loadingFlag"
                :disabled = "loadingFlag"
                @click.stop="triggerSearch()">
                <v-icon>mdi-magnify</v-icon>
                <p>検索</p>
            </v-btn>
        </v-form>
    </div>
</template>

<script>
export default{
    data() {
        return {
            keyword:this.orignalKeyWord
        }
    },
    props:{
        orignalKeyWord:{
            type:String,
            default:""
        },
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
        margin:0 1rem;
        width:90%;
    }
}

</style>
