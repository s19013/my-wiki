<template>
    <Head title="Dashboard"/>

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        You're logged in!
                    </div>
                    <button @click="submit">push</button><br>
                    <button @click="postSubmit">postSubmit</button><br>
                    {{message}}
                </div>
            </div>

        </div>
    </BreezeAuthenticatedLayout>
</template>
<!-- <script setup> -->

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
export default {
        data() {
            return {
                message:'aa'
            }
        },
        components:{
            Head,
            BreezeAuthenticatedLayout
        },
        methods: {
            async submit(){
                await axios.get('api/test')
                .then((res) =>{
                    console.log(res);
                    this.message = res.data.message
                });
            },
            async postSubmit(){
                await axios.post('api/postTest',{
                    message:"post ok"
                },{
                    withCredentials: true
                })
                .then((res) =>{
                    this.message = res.data.message
                })
            }
        },
    }
</script>
