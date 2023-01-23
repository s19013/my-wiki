<template>
    <div class="ads">
        <VueScriptInsert id="ad_internet" url="<div class='admax-ads' data-admax-id='8787785d6794e516f89b1720575abc65'></div><script type='text/javascript'>(admaxads = window.admaxads || []).push({admax_id: '8787785d6794e516f89b1720575abc65',type: 'banner'});</script><script type='text/javascript' charset='utf-8' src='https://adm.shinobi.jp/st/t.js' async></script>" />

        <VueScriptInsert id="ad_book" url="<div class='admax-ads' data-admax-id='f0b94995fc97d247aa3548225ce9e1da'></div><script type='text/javascript'>(admaxads = window.admaxads || []).push({admax_id: 'f0b94995fc97d247aa3548225ce9e1da',type: 'banner'});</script><script type='text/javascript' charset='utf-8' src='https://adm.shinobi.jp/st/t.js' async></script>" />

        <VueScriptInsert id="ad_travel" url="<div class='admax-ads' data-admax-id='520fa94c5ccb084910b95fcf87e7bfae'></div><script type='text/javascript'>(admaxads = window.admaxads || []).push({admax_id: '520fa94c5ccb084910b95fcf87e7bfae',type: 'banner'});</script><script type='text/javascript' charset='utf-8' src='https://adm.shinobi.jp/st/t.js' async></script>" />
    </div>
</template>

<script>
import {VueScriptInsert} from 'vue-script-insert'
export default {
    data() {
      return {}
    },
    components:{
        VueScriptInsert,
    }
}
</script>

<style lang="scss" scoped>
.ads{
    display: flex;
    align-items: center;
    justify-content: center;
    gap:1rem;
    #ad_internet #ad_book{
        display: inline-block;
        iframe{
            min-width:350px;
            min-height:120px;
        }
    }
}

@media (max-width: 900px){
    .ads{flex-flow: column;}
}
</style>
