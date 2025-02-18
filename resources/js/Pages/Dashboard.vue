<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import DashboardCards from "@/Components/DashboardCards.vue";
import LocationCharts from "@/Components/Charts/LocationCharts.vue";
import SalespersonCharts from "@/Components/SalespersonCharts.vue";
import CustomerCharts from "@/Components/Charts/CustomerCharts.vue";
import InventoryCharts from "@/Components/Charts/InventoryCharts.vue";
import MonthYearSelector from "@/Components/MonthYearSelector.vue";
import ManufacturingCharts from "@/Components/Charts/ManufacturingCharts.vue";
const props = defineProps({
    cards_data: Object,
    location_chart_data: Object,
    top_sales_by_location: Object,
    sales_by_salesperson: Object,
    top_sales_by_salesperson: Object,
    sales_by_customer: Object,
    top_sales_by_customer: Object,
    sales_by_mfg_code: Object,
    top_sales_by_mfg_code: Object,
    us_warehouse_inventory: Object,
    availableMonths: Array,
    currentMonth: String,
});
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
                />

                <LocationCharts
                    :location_chart_data="location_chart_data"
                    :top_sales_by_location="top_sales_by_location"
                    :month="currentMonth"
                />

                <SalespersonCharts
                    :sales_by_salesperson="sales_by_salesperson"
                    :top_sales_by_salesperson="top_sales_by_salesperson"
                    :month="currentMonth"
                />

                <CustomerCharts
                    :month="currentMonth"
                    :sales_by_customer="sales_by_customer"
                    :top_sales_by_customer="top_sales_by_customer"
                />

                <ManufacturingCharts
                    :sales_by_mfg_code="sales_by_mfg_code"
                    :top_sales_by_mfg_code="top_sales_by_mfg_code"
                    :month="currentMonth"
                />

                <InventoryCharts
                    :us_warehouse_inventory="us_warehouse_inventory"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
