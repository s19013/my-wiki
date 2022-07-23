<template>
    <div id="editor">
        <h1>aaa</h1>
        <textarea :value="article" @input="update"></textarea>
        <div v-html="compiledMarkdown"></div>
    </div>
</template>

<script>
import {marked} from 'marked'
export default {
    data() {
      return {
        activeTab:0,
        title:null,
        article: '# hello'
      }
    },
    computed: {
        compiledMarkdown: function() {
            return marked(this.article);
        }
    },
    methods: {
      update(e) {this.article = e.target.value;},
    }
}
</script>

<style>
body{
    all: initial;
}
html,
body,
#editor {
  margin: 0;
  height: 100%;
  font-family: "Helvetica Neue", Arial, sans-serif;
  color: #333;
}

textarea,
#editor div {
  display: inline-block;
  width: 49%;
  height: 100%;
  vertical-align: top;
  box-sizing: border-box;
  padding: 0 20px;
}

textarea {
  border: none;
  border-right: 1px solid #ccc;
  resize: none;
  outline: none;
  background-color: #f6f6f6;
  font-size: 14px;
  font-family: "Monaco", courier, monospace;
  padding: 20px;
}

code {
  color: #f66;
}

</style>
