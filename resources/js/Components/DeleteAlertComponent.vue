<template>
    <v-dialog
      v-model="open"
      persistent
      max-width="290"
    >
      <v-card>
        <v-card-title class="text-h5">
          この記事を削除しますか
        </v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="green darken-1"
            text
            @click="this.$emit('switch')"
          >
            もどる
          </v-btn>
          <v-btn
            color="red darken-1"
            text
            @click="this.$emit('switch')"
          >
            削除する
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
</template>

<script>
export default {
    data() {
        return {

        }
    },
    props:["open"]
}
</script>
