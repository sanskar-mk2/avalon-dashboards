<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import DeleteModal from "@/Components/DeleteModal.vue";

const props = defineProps({
    sales: {
        type: Object,
        default: () => ({}),
    },
    availableMonths: {
        type: Array,
        default: () => [],
    },
    currentMonth: {
        type: String,
        required: true,
    },
});

const showDeleteModal = ref(false);

const columns = [
    { key: "company", label: "Company" },
    { key: "location", label: "Location" },
    { key: "order_no", label: "Order No" },
    { key: "backorder", label: "Backorder" },
    { key: "order_date", label: "Order Date" },
    { key: "order_type", label: "Order Type" },
    { key: "customer_no", label: "Customer No" },
    { key: "customer_name", label: "Customer Name" },
    { key: "customer_class", label: "Customer Class" },
    { key: "brand", label: "Brand" },
    { key: "flag", label: "Flag" },
    { key: "salesperson", label: "Salesperson" },
    { key: "invoice_no", label: "Invoice No" },
    { key: "invoice_date", label: "Invoice Date" },
    { key: "item_no", label: "Item No" },
    { key: "item_desc", label: "Item Desc" },
    { key: "item_division", label: "Item Division" },
    { key: "inv_class", label: "Inv Class" },
    { key: "qty", label: "Qty" },
    { key: "ext_sales", label: "Ext Sales" },
    { key: "ext_cost", label: "Ext Cost" },
    { key: "period", label: "Period" },
    { key: "order_status", label: "Order Status" },
    { key: "advertising_source", label: "Advertising Source" },
    { key: "finance_co_rate", label: "Finance Co Rate" },
    { key: "price_matrix", label: "Price Matrix" },
    { key: "price_list_applied", label: "Price List Applied" },
    { key: "price_after_disc", label: "Price After Disc" },
    { key: "ship_to_no", label: "Ship To No" },
    { key: "ship_to_name", label: "Ship To Name" },
    { key: "ship_to_city", label: "Ship To City" },
    { key: "ship_to_state", label: "Ship To State" },
    { key: "requested_ship_date", label: "Requested Ship Date" },
    { key: "customer_desire_date", label: "Customer Desire Date" },
    { key: "mfg_code", label: "Mfg Code" },
];

const breadcrumbs = [{ label: "Home", route: "dashboard" }, { label: "Sales" }];
</script>

<template>
    <PageLayout
        title="Sales"
        :breadcrumbs="breadcrumbs"
        :available-months="availableMonths"
        :current-month="currentMonth"
    >
        <div class="flex justify-end gap-2 sm:gap-4 mb-4">
            <Link
                :href="route('sales.create')"
                class="btn btn-primary btn-sm sm:btn-md text-xs sm:text-sm"
            >
                Upload CSV
            </Link>
            <button
                @click="showDeleteModal = true"
                class="btn btn-error btn-sm sm:btn-md text-xs sm:text-sm"
            >
                Delete All
            </button>
        </div>

        <DataTable :data="sales" :columns="columns" />

        <DeleteModal
            :show="showDeleteModal"
            message="Are you sure you want to delete all sales records? This action cannot be undone."
            delete-route="sales.deleteAll"
            @close="showDeleteModal = false"
        />
    </PageLayout>
</template>
