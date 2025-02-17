<script setup>
import { Link, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { getSalesColumns } from "@/Config/salesColumns";
import DashboardCards from "@/Components/DashboardCards.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import MonthYearSelector from "@/Components/MonthYearSelector.vue";

const props = defineProps({
    location: {
        type: Object,
        default: () => ({}),
    },
    sales: {
        type: Object,
        default: () => ({}),
    },
    cards_data: {
        type: Object,
        default: () => ({}),
    },
    availableMonths: {
        type: Array,
        default: () => ({}),
    },
    currentMonth: {
        type: String,
        default: "",
    },
});

const columns = getSalesColumns();

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Locations", route: "locations.index" },
    { label: props.location.location_abbreviation },
];
</script>
<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex w-full mr-4 justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight">
                    <div class="breadcrumbs text-sm text-base-content">
                        <ul>
                            <li>Home</li>
                        </ul>
                    </div>
                </h2>
                <MonthYearSelector
                    v-if="availableMonths && currentMonth"
                    :available-months="availableMonths"
                    :current-month="currentMonth"
                    :show-y-t-d="true"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <DashboardCards
                    :cards_data="cards_data"
                    :month="currentMonth"
                    :except="['inventory']"
                />
                <div
                    class="mt-4 overflow-hidden bg-base-100 shadow-sm sm:rounded-lg"
                >
                    <div class="p-4 sm:p-6">
                        <DataTable :data="sales" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
