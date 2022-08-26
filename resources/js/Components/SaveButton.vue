<template>
    <div class="saveButton">
        <v-btn color="#BBDEFB" @click.stop="click()" :disabled="disabled">
            <v-icon>mdi-content-save</v-icon>
            保存
        </v-btn>
    </div>
</template>

<script>
export default{
    data() {
        return {
        }
    },
    methods: {
        click(){this.$emit('click');}
    },
    props:['disabled'],
}
</script>

<style lang="scss" scoped>
@media (max-width: 600px){
    .v-btn{
        height: 80%;
        width : 80%;
    }
}
</style>
