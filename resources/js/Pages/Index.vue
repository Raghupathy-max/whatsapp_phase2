<template>
    <div class="container mx-auo">
        <div class="h-screen grid grid-rows-3">
            <div class="mx-auto flex items-center row-span-2">
                <div class="text-center">
                    <img :src="logo" class="h-24" alt="logo">
                    <Spinner/>
                </div>
            </div>
            <div class="mx-auto flex items-center">
                <img :src="coBrand" class="h-24" alt="logo">
            </div>
        </div>
    </div>
</template>

<script>

    import Spinner from "../shared/Spinner";


    export default {

        name: "Index",
        props: {
            token : Object,
        },
        components: {Spinner},

        data() {
            return {
                logo: '/img/logo.png',
                coBrand: '/img/atek_logo.png'
            }
        },



        mounted() {

            this.auth();
        },

        methods: {
             auth : async function(){
                 await this.late(3000);
                 var myHeaders = new Headers();
                 myHeaders.append("Accept", "application/json");
                 myHeaders.append("Content-Type", "application/json");

                 var raw = JSON.stringify({
                     "session_token": this.token
                 });

                 var requestOptions = {
                     method: 'POST',
                     headers: myHeaders,
                     body: raw,
                     redirect: 'follow'
                 };

               const res = await fetch("/api/atek/insert", requestOptions)

                  const data = await res.json();
                console.log(data);
                if(data.status){
                    window.location.href = '/gen/order/'+data.sale_or_no;
                }else{
                    alert("Invalid Session")
                }
            },
           late : function(ms) {
               let late = ms => new Promise(resolve => setTimeout(resolve, ms));
             return new Promise((resolve) => {
            setTimeout(resolve, ms);
             });
         },

        }
    }
</script>

<style scoped>

</style>
