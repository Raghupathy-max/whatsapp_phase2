<template>
    <div class="relative rounded-lg m-2 bg-white">
        <div class="grid w-full content-center p-7" >
            <img :src="logo" alt="..." class="h-24 mx-auto">
        </div>
    </div>
</template>

<script>
    export default {
        name: "Hero",
        data() {
            return {
                logo: '/img/logo.png'
            }
        }
    }
</script>

<style scoped>

</style>
