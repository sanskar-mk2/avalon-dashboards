<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import DashboardCards from "@/Components/DashboardCards.vue";
import LocationCharts from "@/Components/LocationCharts.vue";
import SalespersonCharts from "@/Components/SalespersonCharts.vue";
import CustomerCharts from "@/Components/CustomerCharts.vue";
import InventoryCharts from "@/Components/InventoryCharts.vue";

const props = defineProps({
    cards_data: Object,
    location_chart_data: Object,
    top_sales_by_location: Object,
    sales_by_salesperson: Object,
    top_sales_by_salesperson: Object,
    sales_by_customer: Object,
    top_sales_by_customer: Object,
    us_warehouse_inventory: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                <div class="breadcrumbs text-sm text-base-content">
                    <ul>
                        <li>Home</li>
                    </ul>
                </div>
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <DashboardCards :cards_data="cards_data" />

                <LocationCharts
                    :location_chart_data="location_chart_data"
                    :top_sales_by_location="top_sales_by_location"
                />

                <SalespersonCharts
                    :sales_by_salesperson="sales_by_salesperson"
                    :top_sales_by_salesperson="top_sales_by_salesperson"
                />

                <CustomerCharts
                    :sales_by_customer="sales_by_customer"
                    :top_sales_by_customer="top_sales_by_customer"
                />

                <InventoryCharts
                    :us_warehouse_inventory="us_warehouse_inventory"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
