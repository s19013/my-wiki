<template>
    <div class="tagList">
        <p><v-icon>mdi-tag</v-icon>{{text}}</p>
            <ul >
                    <li v-for="tag of filteredTagList()" :key="tag">{{tag.name}}</li>
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
        text:{
            type:String,
            default:"つけたタグ"
        },
    },
    methods: {
        // 記事やブックマークに何もタグがついてなかった場合{[id:null,name:null]}が帰ってくる
        // しかしこれをこのまま放っておくとバグのもとになる｡見た目がわるくなるので
        // filterをかけて消す
        filteredTagList(){
            return this.tagList.filter( function (tag) {return tag.id != null})
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
        border:black solid 1px;
        padding: 0 10px ;
        margin:5px;
        cursor: default;

    }
}
</style>
