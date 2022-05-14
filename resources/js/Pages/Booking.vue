<template>
    <Navbar/>
    <Hero/>


    <div class="container mx-auto">
        <div class="m-2 bg-white rounded border p-3">

            <!--SOURCE-->
            <div class="mb-6">
                <label class="c-label">Source</label>
                <select class="c-select" v-model="ticket.source_id" v-on:change="getFare">
                    <option disabled value="">Select source station</option>
                    <option
                        v-for="{id, stn_id, stn_name} in stations"
                        :key="id"
                        :value="stn_id">
                        {{ stn_name }}
                    </option>
                </select>
                <!--                <div class="c-error" v-if="errors.ticket.source_id">
                                    {{ errors.ticket.source_id }}
                                </div>-->
            </div>

            <!--DESTINATION-->
            <div class="mb-6">
                <label class="c-label">Destination</label>
                <select class="c-select" v-model="ticket.destination_id" v-on:change="getFare">
                    <option disabled value="">Select source station</option>
                    <option
                        v-for="{id, stn_id, stn_name} in stations"
                        :key="id"
                        :value="stn_id">
                        {{ stn_name }}
                    </option>
                </select>
                <!--                <div class="c-error" v-if="errors.ticket.destination_id">
                                    {{ errors.ticket.destination_id }}
                                </div>-->
            </div>

            <!--TYPE AND QUANTITY-->
            <div class="mb-6 grid grid-cols-2 gap-12 content-center w-full">
                <div class="grid grid-cols-2 text-center content-center w-full">
                    <label class="mx-2 text-gray-900">
                        <input
                            type="radio"
                            name="type"
                            value="10"
                            v-model="ticket.pass_id"
                            v-on:change="getFare">
                        Single
                    </label>
                    <label class="mx-2 text-gray-900">
                        <input
                            type="radio"
                            name="type"
                            value="90"
                            v-model="ticket.pass_id"
                            v-on:change="getFare">
                        Return
                    </label>
                </div>
                <div class="grid grid-cols-5 text-center content-center w-full items-center">
                    <div class="col-span-2"
                         v-on:click="ticket.quantity < 6 ? ticket.quantity++ : ticket.quantity">
                        <i class="fas fa-plus-circle fa-xl mt-1"></i>
                    </div>
                    <p v-text="ticket.quantity" class="text-gray-600 text-3xl font-bold"
                       v-on:change="getFare"></p>
                    <div class="col-span-2"
                         v-on:click="ticket.quantity > 1 ? ticket.quantity-- : ticket.quantity">
                        <i class="fas fa-minus-circle fa-xl mt-1"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="border rounded-lg border-dashed py-5 border-3 border-blue-700 bg-white m-2">
        <!--TOTAL AMOUNT-->
        <div class="text-center pb-1 border-b border-dashed">
            <h5 class="text-gray-400">You have to pay</h5>
            <span class="text-gray-600 text-3xl font-bold text-center">₹
                {{ ticket.quantity * ticket.fare }}
            </span>
        </div>
        <!--OTHER DETAILS-->
        <div class="px-4 mt-1 pb-1 border-b border-dashed">
            <div class="grid grid-cols-2">
                <div class="text-gray-600">Journey Type</div>
                <div class="text-right font-bold text-gray-600">
                    {{ ticket.pass_id === "10" ? "Single Journey" : "Return Journey" }}
                </div>
                <div class="text-gray-600">Passengers</div>
                <div class="text-right font-bold text-gray-600">
                    {{ ticket.quantity }}
                    <i class="fa-solid fa-user mx-2"></i>
                </div>
            </div>
        </div>
        <!--DETAILED FARE-->
        <div class="px-4 mt-1">
            <div class="grid grid-cols-2">
                <div class="text-gray-600">Total Fare</div>
                <div class="text-right font-bold text-gray-600">₹
                    {{ ticket.fare }} X {{ ticket.quantity }}
                    =
                    ₹ {{ ticket.quantity * ticket.fare }}
                </div>
            </div>
        </div>
    </div>

    <div class="p-2">
        <button
            v-on:click="genOrder"
            type="submit"
            class="w-full py-3 bg-blue-500 border rounded-2xl text-gray-100 font-bold hover:bg-blue-700">
            {{ 'PROCEED TO PAY ₹ ' + ticket.quantity * ticket.fare }}
        </button>
    </div>


</template>

<script>
    import Navbar from "../shared/Navbar";
    import Hero from "../shared/Hero";
    import Button from "../shared/Button";
    import axios from "axios";
    export default {

        props: {
            stations: Array,
            token : Object,
            errors:Object
        },

        name: "Booking",
        components: {Hero, Navbar, Button},
        mounted() {
            console.log(this.token);
        },

        data() {
            return {
                ticket: {
                    source_id: '',
                    destination_id: '',
                    quantity: 1,
                    fare: 0,
                    pass_id: "10"
                }
            }
        },

        methods : {
            getFare: async function () {
                const response = await axios.post('api/get/fare', {
                    "source": this.ticket.source_id,
                    "destination": this.ticket.destination_id,
                    "pass_id": this.ticket.pass_id
                });
                let data = await response.data
                if (data.status) this.ticket.fare = data.fare
            },

            genOrder : async function(){
                var myHeaders = new Headers();
                myHeaders.append("Accept", "application/json");
                myHeaders.append("Content-Type", "application/json");
                var totalprice = this.ticket.quantity * this.ticket.fare;
                var raw = JSON.stringify({
                    "sale_or_no": this.token,
                    "src_stn_id": this.ticket.source_id,
                    "des_stn_id": this.ticket.destination_id,
                    "unit": this.ticket.quantity,
                    "unit_price": this.ticket.fare,
                    "total_price": totalprice,
                    "product_id": this.ticket.pass_id === "10" ? "1" : "2",
                    "pass_id": this.ticket.pass_id
                });

                var requestOptions = {
                    method: 'POST',
                    headers: myHeaders,
                    body: raw,
                    redirect: 'follow'
                };

              const res = await fetch("api/gen/order", requestOptions)
                  const data = await res.json();
                console.log(data);

                window.location.href = '/payment/'+this.token;
            },
        }


    }
</script>

<style scoped>

</style>
