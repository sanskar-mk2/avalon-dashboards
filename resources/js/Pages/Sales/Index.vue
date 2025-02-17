<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, computed, h } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import DeleteModal from "@/Components/DeleteModal.vue";
import dayjs from "dayjs";
import { getSalesColumns } from "@/Config/salesColumns";

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
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const filter_object = computed(() => {
    // Check if filters exists and is an object
    if (!props.filters || typeof props.filters !== "object") {
        return null;
    }

    // Check if the filter property exists and is an object
    if (
        !Object.prototype.hasOwnProperty.call(props.filters, "filter") ||
        typeof props.filters.filter !== "object"
    ) {
        return null;
    }

    return props.filters.filter;
});

const showDeleteModal = ref(false);

const columns = getSalesColumns();

const breadcrumbs = computed(() => {
    const crumbs = [{ label: "Home", route: "dashboard" }];

    if (filter_object.value) {
        crumbs.push({
            label: "Sales",
            route: "sales.index",
        });

        let filters_text = "";
        // Add filter breadcrumbs
        Object.entries(filter_object.value).forEach(([key, value]) => {
            filters_text += `${
                key.charAt(0).toUpperCase() + key.slice(1)
            }(${value}), `;
        });
        crumbs.push({ label: filters_text.slice(0, -2) });
    } else {
        crumbs.push({ label: "Sales" });
    }

    return crumbs;
});

const showMonthSelector = computed(() => {
    return !filter_object.value;
});
</script>

<template>
    <PageLayout
        title="Sales"
        :breadcrumbs="breadcrumbs"
        :available-months="availableMonths"
        :current-month="currentMonth"
        :show-month-selector="showMonthSelector"
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
