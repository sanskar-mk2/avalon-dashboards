<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import DeleteModal from "@/Components/DeleteModal.vue";

const props = defineProps({
    inventories: {
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
    { key: "fiscal_period", label: "Period" },
    { key: "location", label: "Location" },
    { key: "item_no", label: "Item" },
    { key: "qty_on_hand", label: "Qty" },
    { key: "average_cost", label: "Avg Cost", to_format: true },
    { key: "quantity_committed", label: "Qty Comm", remove_decimals: true },
    {
        key: "quantity_open_order",
        label: "Qty Open Ord",
        remove_decimals: true,
    },
    {
        key: "quantity_backorder",
        label: "Qty Backorder",
        remove_decimals: true,
    },
    { key: "amount_on_hand", label: "Amount on hand", to_format: true },
];

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Inventories" },
];
</script>

<template>
    <PageLayout
        title="Inventories"
        :breadcrumbs="breadcrumbs"
        :available-months="availableMonths"
        :current-month="currentMonth"
    >
        <div class="flex justify-end gap-2 sm:gap-4 mb-4">
            <Link
                :href="route('inventories.create')"
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

        <DataTable :data="inventories" :columns="columns" />

        <DeleteModal
            :show="showDeleteModal"
            message="Are you sure you want to delete all inventory records? This action cannot be undone."
            delete-route="inventories.deleteAll"
            @close="showDeleteModal = false"
        />
    </PageLayout>
</template>
