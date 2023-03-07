<template>
    <div class="DateLabel">
        <p><span>{{ messages.createdDate }}:</span>{{format(createdAt)}}</p>
        <p><span>{{ messages.updatedDate }}:</span>{{format(updatedAt)}}</p>
    </div>
</template>

<script>

export default{
    data() {
        return {
            japanese:{
                createdDate:'作成日',
                updatedDate:'編集日'
            },
            messages:{
                createdDate:'created',
                updatedDate:'edited'
            }
        }
    },
    props:{
        createdAt:{
            type   :String,
            default:''
        },
        updatedAt:{
            type   :String,
            default:''
        },
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
    mounted() {
        this.$nextTick(function () {
            if (this.$store.state.lang == "ja"){this.messages = this.japanese}
        })
    },
}
</script>

<style lang="scss" scoped>
    .DateLabel{
        display: flex;
        gap: 0.6rem;
        p{
            font-size: 0.8rem;
            font-weight: 450;
        }
        span{font-weight: 500;}
    }
</style>
