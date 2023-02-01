<template>
    <div id="thin_ad">
    </div>
</template>

<script>
import postscribe from 'postscribe'
export default {
    data() {
      return {}
    },
    props:{
        type:{
            type   :String,
            default:"book"
        }
    },
    mounted() {
        postscribe('#thin_ad', `<script src="https://adm.shinobi.jp/o/6cab17ddf2e5c32076faafceb08d60f0"><\/script>`)
    }
}
</script>

<style lang="scss" scoped>
#thin_ad{
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
