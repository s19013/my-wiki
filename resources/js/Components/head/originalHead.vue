<template>
    <div>
        <div class="originalHead">
            <h2>{{pageTitle}}</h2>
            <v-btn @click.stop="$refs.NavMenu.show = true" class="global_css_haveIconButton_Margin">
                <v-icon>mdi-view-headline</v-icon>
                <p>メニュー</p>
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
    props:['pageTitle']
}

</script>

<style lang="scss" scoped>
.originalHead{
    background-color: rgb(127, 255, 174);
    height        : 3rem;
    margin-bottom :0.2rem;
    display       : grid;
    grid-template-columns:1fr 4fr 1fr 0.1fr;
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

@media (max-width: 600px){
    .originalHead {
        grid-template-columns: 1.5fr 2fr 1.5fr .1fr;
    }
}
</style>


