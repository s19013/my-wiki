<template>
    <div>
        <v-btn color="error" @click.stop="click">
            <v-icon>mdi-trash-can</v-icon>
            削除
        </v-btn>
    </div>
</template>

<script>
export default{
    methods: {
        click(){this.$emit('click');}
    },
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
