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
});

const showDeleteModal = ref(false);

const columns = [
    { key: 'company_no', label: 'Company No.' },
    { key: 'fiscal_period', label: 'Fiscal Period' },
    { key: 'location', label: 'Location' },
    { key: 'item_no', label: 'Item No.' },
    { key: 'qty_on_hand', label: 'Qty on Hand' },
    { key: 'average_cost', label: 'Average Cost' },
    { key: 'quantity_committed', label: 'Qty Committed' },
    { key: 'quantity_open_order', label: 'Qty Open Order' },
    { key: 'quantity_backorder', label: 'Qty Backorder' },
    { key: 'board_material', label: 'Board Material' },
    { key: 'board_thickness', label: 'Board Thickness' },
    { key: 'laminate_material', label: 'Laminate Material' },
    { key: 'laminate_color', label: 'Laminate Color' },
    { key: 'as_of_date', label: 'As of Date' },
];

const breadcrumbs = [
    { label: 'Home', route: 'dashboard' },
    { label: 'Inventories' }
];
</script>

<template>
    <PageLayout title="Inventories" :breadcrumbs="breadcrumbs">
        <div class="flex justify-end gap-2 sm:gap-4 mb-4">
            <Link :href="route('inventories.create')" class="btn btn-primary btn-sm sm:btn-md text-xs sm:text-sm">
            Upload CSV
            </Link>
            <button @click="showDeleteModal = true" class="btn btn-error btn-sm sm:btn-md text-xs sm:text-sm">
                Delete All
            </button>
        </div>

        <DataTable :data="inventories" :columns="columns" />

        <DeleteModal :show="showDeleteModal"
            message="Are you sure you want to delete all inventory records? This action cannot be undone."
            delete-route="inventories.deleteAll" @close="showDeleteModal = false" />
    </PageLayout>
</template>
