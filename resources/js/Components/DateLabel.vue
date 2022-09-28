<template>
    <div class="DateLabel">
        <pre><span>作成日:</span>{{format(createdAt)}}</pre>
        <pre><span>編集日:</span>{{format(updatedAt)}}</pre>
    </div>
</template>

<script>

export default{
    props:['createdAt','updatedAt'],
    methods: {
        format(arg){
            return arg.replace(/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}).*/,
            '$1/$2/$3   $4:$5')
        },
    },
}
</script>

<style lang="scss" scoped>
    .DateLabel{
        display: flex;
        @media (max-width: 600px){display: block;}
        pre {
            font-family:  Nunito, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 1.1rem;
            font-weight: 450;
        }
        span{font-weight: 500;}
        pre:nth-child(1) {margin-right: 1rem;}
    }
</style>
