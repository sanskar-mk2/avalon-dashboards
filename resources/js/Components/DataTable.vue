<script setup>
import { Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    columns: {
        type: Array,
        required: true
    },
    routeName: {
        type: String,
        default: null
    },
    linkColumn: {
        type: String,
        default: null
    }
});
</script>

<template>
    <div class="overflow-x-auto">
        <table class="table table-zebra text-xs sm:text-sm">
            <thead>
                <tr>
                    <th v-for="column in columns" :key="column.key">{{ column.label }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data.data" :key="item.id">
                    <td v-for="column in columns" :key="column.key">
                        <template v-if="column.key === linkColumn && routeName">
                            <Link class="link" :href="route(routeName, item.id)">
                                {{ item[column.key] }}
                            </Link>
                        </template>
                        <template v-else>
                            {{ item[column.key] }}
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-2 flex justify-between w-full items-center">
        <div class="text-base-content text-xs sm:text-sm">
            Showing {{ data.from }} to {{ data.to }} of {{ data.total }} results
        </div>
        <Pagination :links="data.links" />
    </div>
</template> 