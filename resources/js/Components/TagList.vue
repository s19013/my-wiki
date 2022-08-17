<template>
    <div class="tagList">
        <p><v-icon>mdi-tag</v-icon> つけたタグ</p>
            <ul >
                    <li v-for="tag of tagList" :key="tag">{{tag.name}}</li>
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
            default:null
        },
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
        border:black solid 1px;
        padding: 0 10px ;
        margin:5px;
        cursor: default;

    }
}
</style>
