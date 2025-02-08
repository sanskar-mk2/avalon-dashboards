<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>
<template>
    <div v-if="links.length > 3">
        <!-- Desktop pagination -->
        <div class="join w-full hidden sm:flex">
            <template v-for="(link, key) in links">
                <div
                    v-if="link.url === null"
                    :key="key"
                    class="join-item btn btn-disabled"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :key="`link-${key}`"
                    class="join-item btn"
                    :class="{ 'btn-active': link.active }"
                    :href="link.url"
                    :preserve-scroll="true"
                    v-html="link.label"
                />
            </template>
        </div>

        <!-- Mobile pagination -->
        <div class="join w-full flex sm:hidden">
            <Link
                v-if="links[0].url"
                :href="links[0].url"
                class="join-item btn btn-xs"
                :preserve-scroll="true"
                >«</Link
            >
            <div v-else class="join-item btn btn-xs btn-disabled">«</div>

            <div class="join-item btn btn-xs">
                Page
                {{
                    links.findIndex((link) => link.active) === -1
                        ? 1
                        : links.findIndex((link) => link.active)
                }}
            </div>

            <Link
                v-if="links[links.length - 1].url"
                :href="links[links.length - 1].url"
                class="join-item btn btn-xs"
                :preserve-scroll="true"
                >»</Link
            >
            <div v-else class="join-item btn btn-xs btn-disabled">»</div>
        </div>
    </div>
</template>
