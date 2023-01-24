<template>
    <div>
        <!-- <div class="ad_bar ad">
            <VueScriptInsert id="ad_bar_internet" url="<div class='admax-switch' data-admax-id='4c1dc2d6f04b64689024958f1f0b1823' style='display:inline-block;'></div><script type='text/javascript'>(admaxads = window.admaxads || []).push({admax_id: '4c1dc2d6f04b64689024958f1f0b1823',type: 'switch'});</script><script type='text/javascript' charset='utf-8' src='https://adm.shinobi.jp/st/t.js' async></script>" />
        </div>

        <div class="ad_bar ad">
            <VueScriptInsert id="ad_bar_book" url="<div class='admax-switch' data-admax-id='6b9fed37e6c3984f691df90de1dc69e0' style='display:inline-block;'></div><script type='text/javascript'>(admaxads = window.admaxads || []).push({admax_id: '6b9fed37e6c3984f691df90de1dc69e0',type: 'switch'});</script><script type='text/javascript' charset='utf-8' src='https://adm.shinobi.jp/st/t.js' async></script>" />
        </div>

        <div class="ad_bar ad">
            <VueScriptInsert id="ad_bar_travel" url="<div class='admax-switch' data-admax-id='9f6e2988ae4ca57ec635c8863cd57ccb' style='display:inline-block;'></div><script type='text/javascript'>(admaxads = window.admaxads || []).push({admax_id: '9f6e2988ae4ca57ec635c8863cd57ccb',type: 'switch'});</script><script type='text/javascript' charset='utf-8' src='https://adm.shinobi.jp/st/t.js' async></script>" />
        </div>

        <div class="ad_sq ad">
            <VueScriptInsert id="ad_sq_book" url="<div class='admax-ads' data-admax-id='7b5482022e760e221d7ddb68f09fca5d' style='display:inline-block;'></div><script type='text/javascript'>(admaxads = window.admaxads || []).push({admax_id: '7b5482022e760e221d7ddb68f09fca5d',type: 'banner'});</script><script type='text/javascript' charset='utf-8' src='https://adm.shinobi.jp/st/t.js' async></script>" />
        </div> -->
        <BarAd></BarAd>
        <BarAd type="travel"></BarAd>
    </div>
</template>

<script>
import {VueScriptInsert} from 'vue-script-insert'
import BarAd from '@/Components/Ads/BarAd.vue';
export default {
    data() {
      return {}
    },
    components:{
        VueScriptInsert,
        BarAd
    }
}
</script>

<style lang="scss">
// .ad_bar iframe{
//     border: none;
//     width:750px;
//     height:110px;
//     @media (max-width: 750px){
//         width:350px;
//         height:120px;
//     }
// }

// .ad_sq iframe{
//     border: none;
//     width:320px;
//     height:270px;
// }
</style>
