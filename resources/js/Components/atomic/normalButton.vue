<template>
    <div class="normalButton">
        <v-btn
            @click.stop="clickTrigger"
            :style=[backgroundColorComp,textColorComp]
        >
            <v-icon>{{icon}}</v-icon>
            <p>{{text}}</p>
        </v-btn>
    </div>
</template>

<script>
export default{
    props:{
        text:{type:String},
        icon:{},
        backgroundColor:{default:"#ffffff"},
        textColor:{default:"#000000"},
    },
    methods: {
        clickTrigger(){this.$emit('clickTrigger');}
    },
    computed: {
        textColorComp() {
          return {'--color' : this.textColor,}
        },
        backgroundColorComp(){
            return {'--background-color':this.backgroundColor}
        }
    }
}
</script>

<style lang="scss">
.normalButton{
    font-size :1rem;
    color:var(--color);
}
@media (max-width: 600px){
    .v-btn{height: 2.2rem;}
}
</style>
