<template>
    <div v-show="loadingFlag">
        <v-progress-circular
            :size=100
            color="primary"
            indeterminate
        ></v-progress-circular>
    </div>
</template>

<script>
export default{
    data() {
        return {
            flag:false
        }
    },
    props:['loadingFlag'],
    watch: {
        // propsをv-modelに渡しても､propsが変化した時にv-modelの値が変わらないらしい
        // なのでwatchを使って､コンポーネント内の変数を変化させる方法にした
        loadingFlag: function(newValue, oldValue){
            this.flag = !this.flag
        }
    }
}

</script>

<style scoped lang="scss">
div{
    background: #bdbdbdc8;
    z-index:20;
    width:100%;
    height: 100%;
    position: fixed;
    top:0px;
    left:0px;
    .v-progress-circular{
        position: absolute;
        // スマホだと割りとずれるのがきになる
        top:45%;
        left:46%;
        margin:auto;
    }
}
// どうにかして画面中央に置かねば positionで行けたはず
// 画面目一杯に表示しないと行けない
</style>

