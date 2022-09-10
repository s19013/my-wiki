<template>
    <div>
        <v-dialog
            v-model="loadingFlag"
            persistent
        >
            <loading class="global_css_Dialog"></loading>
        </v-dialog>
    </div>
</template>

<script>
import loading from '@/Components/loading/loading.vue'
export default{
    props:['loadingFlag'],
    components:{loading}
}

</script>
