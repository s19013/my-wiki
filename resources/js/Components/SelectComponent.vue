<template>
    <div class="SelectComponent">
        <p>{{ label }}:</p>
        <!-- ただの配列 -->
        <div class="selecter">
            <select v-model="localSelected" >
                <option v-for="(element,index) in list" :key="index" :value="element.value">{{ element.label }}</option>
            </select>
        </div>
    </div>

</template>

<script>
export default{
    data() {
        return {
            localSelected:this.selected
        }
    },
    props:{
        selected:{
            type: [String, Number],
            default:""
        },
        label:{
            type:String,
            default:""
        },
        list:{
            type:Array,
            default:[{label:"",value:""}]
        }
    },
    methods: {
        serveLocalSelected(){return this.localSelected},
    },
}

</script>

<style scoped lang="scss">
.SelectComponent{
    display: flex;
    gap:0.5rem;
    .selecter{
        border: 1px solid #000000;
        select{
            // 何故か矢印が消えたので再度出現させる
            -moz-appearance: menulist;
            -webkit-appearance: menulist;
            cursor: pointer;
            padding: 0 1rem;
        }
    }
}
</style>
