<template>
    <div>
        <v-dialog
            v-model="loadingFlag"
            persistent
        >
        <loading></loading>
        </v-dialog>
    </div>
</template>

<script>
import loading from 'loading.vue';
export default{
    props:['loadingFlag'],
    components:{loading}
}

</script>
