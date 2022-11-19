<template>
    <div class="DetailComponent">
        <details>
            <summary >{{summary}} : {{checkedLabel}}</summary>
            <div class="options">
                <template v-for="(element,index) of elements" :key="index">
                    <div class="option">
                        <input type="radio" :id="'option'+index"
                        :value="element.value" v-model="checked"   @click="changecheckedLabel(element.label)"/>
                        <label :for="'option'+index" @click="changecheckedLabel(element.label)">
                            {{element.label}}
                        </label>
                    </div>
                </template>
            </div>
            <!-- <input type="radio" id="option3" value="titleAndBody" v-model="checked" />
            <label for="option3">タイトルまたは本文(低速)</label> -->
        </details>
    </div>
</template>

<script>
export default {
    data() {
        return {
            checked:this.defaltChecked,
            checkedLabel:""
        }
    },
    components:{},
    props:{
        elements:{
            type    :Array,
            required: true
        },
        summary:{
            type    :String,
            required: true
        },
        defaltChecked:{
            type    :String,
            required: true
        }
    },
    methods: {
        serveChecked(){return this.checked},
        changecheckedLabel(label){this.checkedLabel = label}
    },
    mounted(){
        // findIndexだとうまくいかないのなんで!?

        // 配列の中からdefaltCheckedと同じ値を持つデータを探す
        // 一番最初のcheckedLabelを表示するため
        for (let i = 0; i < this.elements.length; i++) {
            if (this.elements[i].value == this.defaltChecked) {
                this.checkedLabel = this.elements[i].label
                break
            }
        }
    }
}
</script>

<style scoped lang="scss">
.options{
        display: flex;
        gap:1rem;
        .option{width:fit-content}
    }
input,label,summary{ cursor: pointer; }
</style>
