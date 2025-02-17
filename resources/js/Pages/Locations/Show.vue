<script setup>
import { Link, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { getSalesColumns } from "@/Config/salesColumns";
import DashboardCards from "@/Components/DashboardCards.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import MonthYearSelector from "@/Components/MonthYearSelector.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";
import SalespersonCharts from "@/Components/SalespersonCharts.vue";
import CustomerCharts from "@/Components/CustomerCharts.vue";

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
    sales_by_salesperson: {
        type: Object,
        default: () => ({}),
    },
    top_sales_by_salesperson: {
        type: Object,
        default: () => ({}),
    },
    sales_by_customer: {
        type: Object,
        default: () => ({}),
    },
    top_sales_by_customer: {
        type: Object,
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
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
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

                <SalespersonCharts
                    :sales_by_salesperson="sales_by_salesperson"
                    :top_sales_by_salesperson="top_sales_by_salesperson"
                    :month="currentMonth"
                    :additional_filters="{
                        'filter[location]': location.location,
                    }"
                />

                <CustomerCharts
                    :month="currentMonth"
                    :sales_by_customer="sales_by_customer"
                    :top_sales_by_customer="top_sales_by_customer"
                    :additional_filters="{
                        'filter[location]': location.location,
                    }"
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
