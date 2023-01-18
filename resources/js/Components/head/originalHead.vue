<template>
    <div>
        <div class="originalHead">
            <h2>{{pageTitle}}</h2>
            <v-btn @click.stop="$refs.NavMenu.show = true" class="global_css_haveIconButton_Margin">
                <v-icon>mdi-view-headline</v-icon>
                <p v-if="$store.state.lang == 'ja'">メニュー</p>
                <p v-else>menu</p>
            </v-btn>
        </div>

        <NavMenu ref="NavMenu"/>
    </div>
</template>

<script>
import NavMenu from '@/Components/NavMenu.vue';
export default{
    components:{
        NavMenu,
    },
    props:['pageTitle'],
}

</script>

<style lang="scss" scoped>
.originalHead{
    background-color: rgb(127, 255, 174);
    height        : 3rem;
    margin-bottom :0.2rem;
    display       : grid;
    gap:1rem;
    grid-template-columns:1fr 4fr 1fr 0.1fr;
    @media (max-width: 600px){
        grid-template-columns: auto 10rem;
        padding-right:0.5rem;
    }
    h2{
        grid-column: 2/3;
        text-align :center;
        margin:auto;
    }
    button{
        grid-column: 3/4;
        margin-top:auto;
        margin-bottom:auto;
    }
}
</style>


