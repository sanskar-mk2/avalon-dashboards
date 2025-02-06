<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import DeleteModal from "@/Components/DeleteModal.vue";

const props = defineProps({
    locations: {
        type: Object,
        default: () => ({}),
    },
});

const showDeleteModal = ref(false);

const columns = [
    { key: 'location', label: 'Location' },
    { key: 'location_name', label: 'Location Name' },
    { key: 'location_abbreviation', label: 'Location Abbreviation' },
    { key: 'sales_count', label: 'Sales' },
    { key: 'open_orders_count', label: 'Open Orders' },
];

const breadcrumbs = [
    { label: 'Home', route: 'dashboard' },
    { label: 'Locations' }
];
</script>

<template>
    <PageLayout title="Locations" :breadcrumbs="breadcrumbs">
        <div class="flex justify-end gap-2 sm:gap-4 mb-4">
            <Link :href="route('locations.create')" class="btn btn-primary btn-sm sm:btn-md text-xs sm:text-sm">
            Upload CSV
            </Link>
            <button @click="showDeleteModal = true" class="btn btn-error btn-sm sm:btn-md text-xs sm:text-sm">
                Delete All
            </button>
        </div>

        <DataTable :data="locations" :columns="columns" route-name="locations.show"
            link-column="location_abbreviation" />

        <DeleteModal :show="showDeleteModal"
            message="Are you sure you want to delete all location records? This action cannot be undone."
            delete-route="locations.deleteAll" @close="showDeleteModal = false" />
    </PageLayout>
</template>
