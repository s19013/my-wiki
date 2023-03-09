<template>
    <div class="SelectComponent">
        <p>{{ label }}:</p>
        <!-- ただの配列 -->
        <div class="selecter">
            <select v-model="selected" v-if="object==false">
                <option v-for="(element,index) in list" :key="index" :value="element">{{ element }}</option>
            </select>
            <!-- オブジェクトが入っている配列 -->
            <template v-else>
                <select v-model="selected" >
                    <option v-for="(element,index) in list" :key="index" :value="element">{{ element.label }}</option>
                </select>
            </template>
        </div>
    </div>
</template>

<script>
export default{
    data() {
        return {
            selected:""
        }
    },
    props:{
        label:{
            type:String,
            default:""
        },
        list:{
            type:Array,
            default:[{label:"",value:""}]
        },
        object:{
            type:Boolean,
            default:false
        },
    },
    methods: {
        serveSelected(){return this.selected},
        // タイミングの問題でpropsでうまく初期値が設定されないため
        setSelected(selected){this.selected = selected}
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
