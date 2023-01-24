<template>
    <div id="ad">
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
        postscribe('#ad', `<div  class='admax-switch' data-admax-id='4c1dc2d6f04b64689024958f1f0b1823' style='display:inline-block;'></div><script type='text/javascript'>(admaxads = window.admaxads || []).push({admax_id: '4c1dc2d6f04b64689024958f1f0b1823',type: 'switch'});<\/script><script type='text/javascript' charset='utf-8' src='https://adm.shinobi.jp/st/t.js' async><\/script>`)
    }
}
</script>

<style lang="scss">
#ad{
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
