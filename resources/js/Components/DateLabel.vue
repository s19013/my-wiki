<template>
    <div class="DateLabel" :style="textSizeComp">
        <p><span>作成日:</span>{{format(createdAt)}}</p>
        <p><span>編集日:</span>{{format(updatedAt)}}</p>
    </div>
</template>

<script>

export default{
    props:{
        createdAt:{
            type   :String,
            default:''
        },
        updatedAt:{
            type   :String,
            default:''
        },
        size:{
            type   :String,
            default:'0.8rem'
        }
    },
    methods: {
        format(arg){
            try {
                // これで国ごとに日時をあわせられる
                return new Date(arg).toLocaleString()
            } catch (error) {
                // toLocaleStringが聞かなかったら引数の情報を正規表現
                return arg.replace(/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}).*/,'$1/$2/$3   $4:$5:$6')
            }
        },
    },
    computed:{
        textSizeComp(){
            return {'--size' : this.size,}
        }
    }
}
</script>

<style lang="scss" scoped>
    .DateLabel{
        display: flex;
        gap: var(--size);
        @media (max-width: 300px){display: block;}
        p{
            font-size: var(--size);
            font-weight: 450;
        }
        span{font-weight: 500;}
    }
</style>
