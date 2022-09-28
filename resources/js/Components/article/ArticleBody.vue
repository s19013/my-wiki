<template>
    <div class="articleBody">
        <!-- タブ -->
        <p>ctrl + spaceで変換前､変換後切り替え</p>
        <v-row>
            <v-col>
                <ul class="tabLabel">
                    <li>
                        <!-- ここボタンタグに治す -->
                        <v-btn @click="changeTab()" flat :rounded="0" :disabled="activeTab === 1">
                            <p>本文</p>
                        </v-btn>
                    </li>
                    <li>
                        <v-btn @click="changeTab()" flat :rounded="0" :disabled="activeTab === -1">
                            <p>変換後</p>
                        </v-btn>
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
                label="本文 [必須] ※マークダウン記法で書いてください '\' で改行"
                v-model = "body"
            ></v-textarea>
        </div>

        <CompiledMarkDown v-show="activeTab === -1" :originalMarkDown="body"/>
    </div>
</template>

<script>
import {marked} from 'marked';
import CompiledMarkDown from '@/Components/article/CompiledMarkDown.vue';
export default {
    data() {
        return {
            activeTab :1,
            body      :this.originalArticleBody,
        }
    },
    components:{CompiledMarkDown},
    props:{
        originalArticleBody:{
            type   :String,
            default:''
        },
    },
    methods: {
        changeTab(){
            this.activeTab *= -1
            if (this.activeTab === 1) {this.focusToBody()}
        },
        serveBody(){return this.body},
        focusToBody(){ this.$nextTick(() => this.$refs.textarea.focus()) },
    },
    mounted() {
        //キーボード受付
        document.addEventListener('keydown', (event)=>{
            // タブ切り替え(アプリ内)
            if (event.ctrlKey || event.key === "Meta") {
                if(event.code === "Space"){this.changeTab()}
                return
            }
        })
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

.tabLabel{
    li{
        display   : inline-block;
        list-style:none;
        border :black solid 1px;
        button{width: 6rem;}
        p {font-size: larger;}
    }
}
.CompiledMarkDown{margin:1rem;}
</style>
