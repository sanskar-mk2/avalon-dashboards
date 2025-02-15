<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import DeleteModal from "@/Components/DeleteModal.vue";

const props = defineProps({
    open_orders: {
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
    { key: "location", label: "Location" },
    { key: "order_no", label: "Order No" },
    { key: "order_date", label: "Order Date" },
    { key: "customer_no", label: "Cust No" },
    { key: "customer_name", label: "Cust Name" },
    { key: "item_no", label: "Item No" },
    { key: "item_desc", label: "Item Desc" },
    { key: "qty", label: "Qty", remove_decimals: true },
    { key: "ext_sales", label: "Sales", to_format: true },
    { key: "ext_cost", label: "Cost", to_format: true },
    { key: "period", label: "Period" },
    { key: "mfg_code", label: "Mfg Code" },
    { key: "requested_ship_date", label: "Req Ship Date" },
];

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Open Orders" },
];
</script>

<template>
    <PageLayout
        title="Open Orders"
        :breadcrumbs="breadcrumbs"
        :available-months="availableMonths"
        :current-month="currentMonth"
    >
        <div class="flex justify-end gap-2 sm:gap-4 mb-4">
            <Link
                :href="route('open_orders.create')"
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

        <DataTable :data="open_orders" :columns="columns" />

        <DeleteModal
            :show="showDeleteModal"
            message="Are you sure you want to delete all open order records? This action cannot be undone."
            delete-route="open_orders.deleteAll"
            @close="showDeleteModal = false"
        />
    </PageLayout>
</template>
