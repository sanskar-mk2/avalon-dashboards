<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import DeleteModal from "@/Components/DeleteModal.vue";

const props = defineProps({
    account_receivables: {
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
    { key: "customer_no", label: "Customer No." },
    {
        key: "customer_name",
        label: "Customer Name",
        custom_value: (model) => {
            if (model?.customer_name != null) {
                return {
                    component: Link,
                    props: {
                        href: route("customers.show", model.customer_name),
                        class: "link",
                    },
                    children: model.customer_name,
                };
            }
            return model?.customer_name ?? "-";
        },
    },
    { key: "balance_due_amount", label: "Balance Due", to_format: true },
    { key: "balance_age_1", label: "Current", to_format: true },
    { key: "balance_age_2", label: "31-60 Days", to_format: true },
    { key: "balance_age_3", label: "61-90 Days", to_format: true },
    { key: "balance_age_4", label: "91-120 Days", to_format: true },
    { key: "balance_age_5", label: "121-150 Days", to_format: true },
    { key: "balance_age_6", label: "151+ Days", to_format: true },
    { key: "credit_manager", label: "Credit Manager" },
    {
        key: "location",
        label: "Location",
        custom_value: (model) => {
            if (model.location_model != null) {
                return {
                    component: Link,
                    props: {
                        href: route("locations.show", model.location_model.id),
                        class: "link",
                    },
                    children: model.location_model.location_abbreviation,
                };
            }
            return model.location;
        },
    },
];

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Account Receivables" },
];
</script>

<template>
    <PageLayout
        title="Account Receivables"
        :breadcrumbs="breadcrumbs"
        :available-months="availableMonths"
        :current-month="currentMonth"
    >
        <div class="flex justify-end gap-2 sm:gap-4 mb-4">
            <Link
                :href="route('account_receivables.create')"
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

        <DataTable :data="account_receivables" :columns="columns" />

        <DeleteModal
            :show="showDeleteModal"
            message="Are you sure you want to delete all account receivable records? This action cannot be undone."
            delete-route="account_receivables.deleteAll"
            @close="showDeleteModal = false"
        />
    </PageLayout>
</template>
