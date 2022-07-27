<template>
    <v-dialog
        v-model="open"
        persistent
        scrollable
    >
    <v-card>
        <v-card-title class="text-h5">タグ</v-card-title>
        <ul>
            <li></li>
        </ul>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="green darken-1"
            text
            @click="dialog = false"
          >
            新規作成
          </v-btn>
        </v-card-actions>
    </v-card>
    </v-dialog>
</template>

<script>
export default{

}
</script>
