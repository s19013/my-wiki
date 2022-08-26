<template>
    <div>
        <!-- タブ -->
        <v-row>
            <v-col>
                <ul class="tabLabel">
                    <li @click="changeTab(0)" :class="{active: activeTab === 0,notActive: activeTab !== 0 }">
                        本文
                    </li>
                    <li @click="changeTab(1)" :class="{active: activeTab === 1,notActive: activeTab !== 1 }">
                        変換後
                    </li>
                </ul>
            </v-col>
        </v-row>

        <!-- md入力欄  -->
        <div v-show="activeTab === 0">
            <v-textarea
                filled
                auto-grow
                label="本文 [必須]"
                v-model = "body"
            ></v-textarea>
        </div>
        <div v-show="activeTab === 1" class="markdown" v-html="compiledMarkdown()"></div>
    </div>
</template>

<script>
import {marked} from 'marked';
export default {
    data() {
        return {
            activeTab     :0,
            body   :this.originalArticleBody,
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
        changeTab(num){this.activeTab = num},
        serveBodyToParent(){return this.body}
    },
}
</script>

<style scoped lang="scss">
textarea {
        width  : 100%;
        resize : none;
        padding: 20px;
        background-color: #f6f6f6;
}
.markdown{
    padding      : 0 10px;
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
