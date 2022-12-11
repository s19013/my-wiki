<template>
    <div class="CompiledMarkDown markdown-body" v-html="compileMarkDown()"></div>
</template>

<script>
import {marked} from 'marked';
import githubMarkdownCss from 'github-markdown-css';
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
    methods: {compileMarkDown(){
        const sanitized = sanitizeHtml(this.originalMarkDown)
        const replaced  = sanitized.replaceAll(/\n(?=\n)/g, "\n<br>\n")
        return marked(replaced)
    }},
}
</script>

<!-- なぜかわからないがscopedをつけたら中のh1タグなどが反応しない -->
<style lang="scss" scoped>
.CompiledMarkDown{
    padding-bottom: 2rem;
    word-break   :break-word;
    overflow-wrap:normal;
    list-style-position:inside;
//     h1,h2,h3,h4,h5{ margin:0.5rem 0; }
//     ul,ol{ margin:0.5rem 0; }
//     th,td {
//         padding:0.2rem;
//         border: solid 1px;
//     }
//     p{
//         code{
//             background-color: #e5e5e5;
//             color: #000000;
//             padding: 0 0.3rem;
//             margin: 0 0.3rem;
//         }
//     }
//     pre{
//         background-color: #364549;
//         color: #e3e3e3;
//         padding: 0.8rem;
//         // スクロールバー表示
//         overflow: auto;
//     }
//     table {
//         border-collapse:  collapse; /* セルの線を重ねる */
//         margin:0.5rem 0;
//     }
}
</style>
