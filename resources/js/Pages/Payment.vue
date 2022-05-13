<template>
    <Navbar/>
    <Hero/>
    <div class="d-flex p-2">
        <div class="d-flex p-2">
            <button v-on:click="genTkt" class="w-full py-3 bg-blue-500 border rounded-2xl text-gray-100 font-bold hover:bg-blue-700">SUCCESS</button>
            <br>
            <br>
            <button  class="w-full py-3 bg-blue-500 border rounded-2xl text-gray-100 font-bold hover:bg-blue-700">CANCEL</button>
        </div>

    </div>
</template>

<script>
    import Navbar from "../shared/Navbar";
    import Hero from "../shared/Hero";
    import Button from "../shared/Button";
    export default {
        name: "Payment",
        props: {
            token : Object,
        },
        components: {Button, Hero, Navbar},

        methods:{
            genTkt : async function () {
                var myHeaders = new Headers();
                myHeaders.append("Accept", "application/json");
                myHeaders.append("Content-Type", "application/json");

                var raw = JSON.stringify({
                    "sale_or_no": this.token
                });

                var requestOptions = {
                    method: 'POST',
                    headers: myHeaders,
                    body: raw,
                    redirect: 'follow'
                };

               const res = await fetch("http://127.0.0.1:8000/api/gen/ticket", requestOptions)
                const data = await res.json();
               console.log(data);

            }
        },
    }

</script>

<style scoped>

</style>
