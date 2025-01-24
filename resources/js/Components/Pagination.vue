<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: {
        type: Array,
        required: true
    }
})
</script>
<template>
    <div v-if="links.length > 3">
        <div class="join w-full">
            <template v-for="(link, key) in links">
                <div v-if="link.url === null" :key="key" class="join-item btn btn-disabled" v-html="link.label" />
                <Link v-else :key="`link-${key}`" class="join-item btn" :class="{ 'btn-active': link.active }" :href="link.url"
                    :preserve-scroll="true" v-html="link.label" />
            </template>
        </div>
    </div>
</template>
