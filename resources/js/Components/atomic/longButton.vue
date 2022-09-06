<template>
    <div class="longButton">
        <v-btn
            @click.stop="clickTrigger"
            :style=[backgroundColorComp,textColorComp]
        >
            <p class="icon"><v-icon>{{icon}}</v-icon></p>
            <p class="text">{{text}}</p>
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

<style lang="scss" scoped>
button{width: 100%;}
.normalButton{
    font-size :1rem;
    color:var(--color);
}
@media (max-width: 600px){
    .v-btn{height: 2.2rem;}
}
</style>
