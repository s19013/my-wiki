<template>
    <div>
        <div class="originalHead">
            <h2>{{pageTitle}}</h2>
            <longButton
                text="メニュー"
                icon="mdi-view-headline"
                backgroundColor='#d4d4d4'
                textColor='#000000'
                @clickTrigger= "$refs.NavMenu.show = true"
            />
        </div>
        <NavMenu ref="NavMenu"/>
    </div>
</template>

<script>
import NavMenu from '@/Components/NavMenu.vue';
import longButton from '@/Components/atomic/longButton.vue';
export default{
    components:{
        NavMenu,
        longButton
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
    width: 100%;
    h2{
        grid-column: 2/3;
        text-align :center;
        margin:auto;
    }
    .longButton{
        grid-column: 3/4;
        margin:auto;
        width: 100%;
        // margin: 0.2rem 0;

    }
}
</style>


