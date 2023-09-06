<template>
    <div>
        <div class="originalHead">
            <h2>{{ pageTitle }}</h2>
            <v-btn
                @click.stop="$refs.NavMenu.show = true"
                class="global_css_haveIconButton_Margin"
            >
                <v-icon>mdi-view-headline</v-icon>
                <p v-if="$store.state.lang == 'ja'">メニュー</p>
                <p v-else>menu</p>
            </v-btn>
        </div>

        <NavMenu ref="NavMenu" />
    </div>
</template>

<script>
import NavMenu from "@/Components/NavMenu.vue";
export default {
    components: {
        NavMenu,
    },
    props: ["pageTitle"],
};
</script>

<style lang="scss" scoped>
.originalHead {
    background-color: rgb(127, 255, 174);
    height: 3rem;
    padding-bottom: 0.2rem;
    display: grid;
    grid-template-columns: 20% 60% 18% auto;
    @media (max-width: 900px) {
        grid-template-columns: 0 70% 28% auto;
    }
    h2 {
        grid-column: 2/3;
        text-align: center;
        margin-top: auto;
        margin-bottom: auto;
    }
    button {
        grid-column: 3/4;
        width: 100%;
        margin-top: auto;
        margin-bottom: auto;
    }
}
</style>
