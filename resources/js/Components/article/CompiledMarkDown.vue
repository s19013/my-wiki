<template>
    <div class="CompiledMarkDown markdown-body" v-html="compileMarkDown()"></div>
</template>

<script>
import {marked} from 'marked';
// import githubMarkdownCss from 'github-markdown-css';
import githubMarkdownCss from 'github-markdown-css/github-markdown-light.css';
import sanitizeHtml from 'sanitize-html';

// エンターで改行できるように設定
marked.setOptions({
    "breaks": true,
    "gfm": true,
});
export default {
    props:{
        originalMarkDown:{
            type   :String,
            default:''
        },
    },
    methods: {
        compileMarkDown(){
        // 無毒化
        const sanitized = sanitizeHtml(this.originalMarkDown,{enforceHtmlBoundary: true})
        const beforeCompiled  = this.replaceMarkDownBefore(sanitized)
        const compiled = marked(beforeCompiled)
        return this.replaceMarkDownAfter(compiled)
        },
        // 変換前の処理
        replaceMarkDownBefore(arg){
            // codeタグ内では一部特殊文字をエスケープしない
            // gmで複数行をまたいだ(改行を無視した)検索になるはずだができてないので[\s|\S]で代用
            arg = arg.replace(/(?<=`[\s|\S]*)&lt;(?=[\s|\S]*`)/g , "<");
            arg = arg.replace(/(?<=`[\s|\S]*)&gt;(?=[\s|\S]*`)/g , ">");
            arg = arg.replace(/(?<=`[\s|\S]*)&amp;(?=[\s|\S]*`)/g , "&");

            // codeタグ以外では \n\n -> \n<br   />\n
            // 連続改行を実現させるため｡
            // markedで<br   /> -> <br>に変換される
            // ユーザーがcodeタグ内で意図的に<br>を書いても消されないようにする
            arg = arg.replace(/\n(?=\n)/g, "\n<br   />")

            // codeタグでは上記の<br   />を消す
            arg = arg.replace(/(?<=`[\s|\S]*)<br   \/>(?=[\s|\S]*`)/g ,"");

            return arg;
        },
        // 変換後の処理
        replaceMarkDownAfter(arg){
            // liタグ内ではpタグを消す
            // バグなのかたまにliタグ内にpタグで出力されて表示が崩れてしまう｡
            arg = arg.replace(/(?<=\<li\>)<p>/g ,"");
            arg = arg.replace(/<\/p>(?=[\s|\S]*\<\/li\>)/g ,"");

            return arg;
        }
    },
}
</script>

<!-- なぜかわからないがscopedをつけたら中のh1タグなどが反応しない -->
<style lang="scss" scoped>
.CompiledMarkDown{
    padding-bottom: 2rem;
    word-break   :break-word;
    overflow-wrap:normal;
    list-style-position:inside;
    background-color: #fcfcfc;
}
</style>
