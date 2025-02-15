<script setup>
import { Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    routeName: {
        type: String,
        default: null,
    },
    linkColumn: {
        type: String,
        default: null,
    },
});

const numberFormatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
});

const formatValue = (value) => {
    if (!value) return "-";
    if (value == 0) return "-";
    return value;
};

const formatNumberCurrency = (value) => {
    value = formatValue(value);
    if (value == "-") return "-";
    return numberFormatter.format(value);
};

const removeDecimals = (value) => {
    value = formatValue(value);
    if (value == "-") return "-";
    return Math.floor(value);
};
</script>

<template>
    <div class="overflow-x-auto">
        <table class="table table-zebra text-xs sm:text-sm">
            <thead>
                <tr>
                    <th v-for="column in columns" :key="column.key">
                        {{ column.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data.data" :key="item.id">
                    <td v-for="column in columns" :key="column.key">
                        <template v-if="column.key === linkColumn && routeName">
                            <Link
                                class="link"
                                :href="route(routeName, item.id)"
                            >
                                {{ item[column.key] || "-" }}
                            </Link>
                        </template>
                        <template v-else>
                            <template v-if="column.to_format">
                                {{ formatNumberCurrency(item[column.key]) }}
                            </template>
                            <template v-else-if="column.remove_decimals">
                                {{ removeDecimals(item[column.key]) }}
                            </template>
                            <template v-else>
                                {{ formatValue(item[column.key]) }}
                            </template>
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
