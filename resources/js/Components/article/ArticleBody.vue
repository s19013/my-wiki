<template>
    <div class="articleBody">
        <!-- タブ -->
        <p>ctrl + spaceで変換前､変換後切り替え</p>
        <div class="tabLabel">
            <v-btn @click="changeTab()" flat :rounded="0" :class="[activeTab === 1 ? 'active' : '']" >
                <p>本文</p>
            </v-btn>
            <v-btn @click="changeTab()" flat :rounded="0" :class="[activeTab === -1 ? 'active' : '']">
                <p>変換後</p>
            </v-btn>
        </div>
        <!-- md入力欄  -->
        <div v-show="activeTab === 1">
            <v-textarea
                ref="textarea"
                filled
                no-resize
                rows="20"
                label="本文 [必須] ※マークダウン記法で書いてください '\' で改行"
                :disabled="disabledFlag"
                :loading ="disabledFlag"
                v-model = "body"
            ></v-textarea>
        </div>

        <CompiledMarkDown v-show="activeTab === -1" :originalMarkDown="body"/>
    </div>
</template>

<script>
import CompiledMarkDown from '@/Components/article/CompiledMarkDown.vue';
export default {
    data() {
        return {
            activeTab :1,
            body      :'',
        }
    },
    components:{CompiledMarkDown},
    props:{
        originalArticleBody:{
            type   :String,
            default:''
        },
        disabledFlag:{
            type   :Boolean,
            default:false
        }
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
        // とにかくこれを使って最後に処理することができる?
        this.$nextTick(() => {
            // レンダリング後の処理
            // props受け渡し mounted使わなくてもできたはずなんだけどな?
            this.body = this.originalArticleBody
        });
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
    display: flex;
    button{
        border :black solid 1px;
        width: 6rem;
    }
    p {font-size: larger;}
}

.active{
    background-color:#ffd4ae;
}
.CompiledMarkDown{margin:1rem;}
</style>
