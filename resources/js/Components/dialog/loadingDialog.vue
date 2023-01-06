<template>
    <div>
        <v-dialog
            v-model="flag"
            persistent
        >
            <loading></loading>
        </v-dialog>
    </div>
</template>

<script>
import loading from '@/Components/loading/loading.vue'
export default{
    data() {
        return {
            flag:false
        }
    },
    props:['loadingFlag'],
    components:{loading},
    watch: {
        // propsをv-modelに渡しても､propsが変化した時にv-modelの値が変わらないらしい
        // なのでwatchを使って､コンポーネント内の変数を変化させる方法にした
        loadingFlag: function(newValue, oldValue){
            this.flag = !this.flag
            console.log(this.flag);
        }
    }
}

</script>
