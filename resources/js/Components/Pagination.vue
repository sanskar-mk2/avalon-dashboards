<template>
    <div class="w-full" v-if="links.length > 3">
        <div class="flex justify-between w-full">
            <template v-for="(link, key) in links">
                <div v-if="link.url === null" :key="key" class="btn btn-disabled" v-html="link.label" />
                <Link v-else :key="`link-${key}`" class="btn" :class="{ 'btn-active': link.active }" :href="link.url" v-html="link.label" />
            </template>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: {
        type: Array,
        required: true
    }
})
</script>