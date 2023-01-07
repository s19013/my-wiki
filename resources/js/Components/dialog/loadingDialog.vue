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
    props:['loadingFlag'],
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

