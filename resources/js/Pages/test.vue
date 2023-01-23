<template>
    <div>
        <VueScriptInsert id="testAd" url="<div class='admax-ads' data-admax-id='aa46f1fa6e6dd94f77e476728a59f9a1'></div><script type='text/javascript'>(admaxads = window.admaxads || []).push({admax_id: 'aa46f1fa6e6dd94f77e476728a59f9a1',type: 'banner'});</script><script type='text/javascript' charset='utf-8' src='https://adm.shinobi.jp/st/t.js' async></script>" />
        <SearchField></SearchField>
        <p>aaa</p>
    </div>
</template>

<script>
import {VueScriptInsert} from 'vue-script-insert'
import SearchField from '@/Components/SearchField.vue';
export default {
    data() {
      return {

      }
    },
    components:{
      SearchField,
      VueScriptInsert
    }
}
</script>

<style>
.admax-ads{
    display:inline-block;width:300px;height:250px;
}
</style>
