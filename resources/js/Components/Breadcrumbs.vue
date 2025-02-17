<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    breadcrumbs: Array,
});
</script>

<template>
    <h2 class="text-lg sm:text-xl font-semibold leading-tight">
        <div class="breadcrumbs text-xs sm:text-sm text-base-content">
            <ul>
                <li v-for="(crumb, index) in breadcrumbs" :key="index">
                    <template v-if="crumb.route">
                        <Link :href="route(crumb.route)">{{
                            crumb.label
                        }}</Link>
                    </template>
                    <template v-else>
                        {{ crumb.label }}
                    </template>
                </li>
            </ul>
        </div>
    </h2>
</template>
