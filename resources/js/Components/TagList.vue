<template>
    <div class="tagList">
        <p><v-icon>mdi-tag</v-icon>{{text}}</p>
        <ul v-if="!disabled">
            <li v-for="(tag, i) in tagList"
                :key="tag.name"
            >
                <v-chip
                    v-if="!cannotDelete"
                    closable
                    @click:close="popTag(i)"
                >
                    {{ tag.name }}
                </v-chip>

                <!-- 観覧のときはタグをいじれないようにする -->
                <v-chip v-else>
                    {{ tag.name }}
                </v-chip>
            </li>
        </ul>
    </div>
</template>

<script>
export default{
    data() {
      return {
      }
    },
    props:{
        tagList:{
            type:Array,
            default:[]
        },
        text:{
            type:String,
            default:"つけたタグ"
        },
        cannotDelete:{
            type:Boolean,
            default:false
        },
        disabled:{
            type:Boolean,
            default:false
        }
    },
    methods: {
        popTag(i){
            this.$emit('popTag',i)
        }
    },
}
</script>

<style lang="scss" scoped>
.tagList{
    ul{
        display: flex;
        flex-wrap: wrap;
    }
    li{
        list-style:none;
        margin:5px;
    }
}
</style>
