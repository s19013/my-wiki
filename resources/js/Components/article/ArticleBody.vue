<template>
    <div>
        <!-- タブ -->
        <v-row>
            <v-col>
                <ul class="tabLabel">
                    <li @click="changeTab()" :class="{active: activeTab === 1,notActive: activeTab !== 1 }">
                        本文
                    </li>
                    <li @click="changeTab()" :class="{active: activeTab === -1,notActive: activeTab !== -1 }">
                        変換後
                    </li>
                </ul>
            </v-col>
        </v-row>

        <!-- md入力欄  -->
        <div v-show="activeTab === 1">
            <v-textarea
                ref="textarea"
                filled
                no-resize
                rows="20"
                label="本文 [必須]"
                v-model = "body"
                @keydown.shift.ctrl.exact="changeTab"
                @keydown.shift.meta.exact="changeTab"
            ></v-textarea>
        </div>
        <div v-show="activeTab === -1" class="markdown" v-html="compiledMarkdown()"></div>
    </div>
</template>

<script>
import {marked} from 'marked';
export default {
    data() {
        return {
            activeTab :1,
            body      :this.originalArticleBody,
        }
    },
    props:{
        originalArticleBody:{
            type   :String,
            default:''
        },
    },
    methods: {
        compiledMarkdown() {return marked(this.body)},
        changeTab(){
            this.activeTab *= -1
            if (this.activeTab === 1) {this.focusToBody()}
        },
        serveBody(){return this.body},
        focusToBody(){ this.$nextTick(() => this.$refs.textarea.focus()) },

    },
}
</script>

<style scoped lang="scss">
textarea {
        width  : 100%;
        resize : none;
        // padding: 20px;
        background-color: #f6f6f6;
}
.markdown{
    margin:20px;
    word-break   :break-word;
    overflow-wrap:normal;
}

.tabLabel{
    li{
        display   : inline-block;
        list-style:none;
        border :black solid 1px;
        padding:10px 20px;
    }
    .active{
        font-weight: bold;
        cursor     : default;
    }

    .notActive{
        background: #919191;
        color : black;
        cursor: pointer;
    }
}
</style>
