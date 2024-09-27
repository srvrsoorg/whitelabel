<template>
    <div role="status" class="w-full animate-pulse my-2" v-for="n in count" :key="n">
        <div class="rounded-md w-full bg-gray-100" :class="[height]"></div>
    </div>
</template>

<script>
    export default{
        props: {
            count: {
                type: Number,
                required: true
            },
            height: {
                type: String,
                default: 'h-5'
            },
        }
    }
</script>