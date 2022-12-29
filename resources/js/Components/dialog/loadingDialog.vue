<template>
    <div>
        <v-dialog
            v-model="flag"
            persistent
        >
            <loading class="global_css_Dialog"></loading>
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
        flag: function(newValue, oldValue){
            flag = !flag
        }
    }
}

</script>
