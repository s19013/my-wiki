<template>
    <div id="ad">
        <!-- a8.net の広告 -->
    </div>
</template>

<script>
import postscribe from 'postscribe'
export default {
    mounted() {
        postscribe('#ad', `
        <script type="text/javascript">rakuten_affiliateId="0ea62065.34400275.0ea62066.204f04c0";rakuten_items="ranking";rakuten_genreId="0";rakuten_recommend="on";rakuten_design="slide";rakuten_size="468x60";rakuten_target="_blank";rakuten_border="on";rakuten_auto_mode="on";rakuten_adNetworkId="a8Net";rakuten_adNetworkUrl="https%3A%2F%2Frpx.a8.net%2Fsvt%2Fejp%3Fa8mat%3D3T4O7M%2BCNGMK2%2B2HOM%2BBS629%26rakuten%3Dy%26a8ejpredirect%3D";rakuten_pointbackId="a23031332501_3T4O7M_CNGMK2_2HOM_BS629";rakuten_mediaId="20011816";<\/script><script type="text/javascript" src="//xml.affiliate.rakuten.co.jp/widget/js/rakuten_widget.js"><\/script>
        <img border="0" width="1" height="1" src="https://www19.a8.net/0.gif?a8mat=3T4O7M+CNGMK2+2HOM+BS629" alt="">
        `)
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
