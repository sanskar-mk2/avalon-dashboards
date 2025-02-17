<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import PageLayout from "@/Components/PageLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import DeleteModal from "@/Components/DeleteModal.vue";
import dayjs from "dayjs";

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
    { key: "order_no", label: "Order No" },
    {
        key: "order_date",
        label: "Order Date",
        custom_value: (model) => model.order_date.replace(/-/g, "\u2011"),
    },
    { key: "customer_no", label: "Cust No" },
    {
        key: "customer_name",
        label: "Customer Name",
        custom_value: (model) => {
            return {
                component: Link,
                props: {
                    href: route("customers.show", model.customer_name),
                    class: "link",
                },
                children: model.customer_name,
            };
        },
    },
    {
        key: "salesperson",
        label: "Salesperson",
        custom_value: (model) => {
            if (model.salesperson_model != null) {
                return {
                    component: Link,
                    props: {
                        href: route(
                            "salespeople.show",
                            model.salesperson_model.id
                        ),
                        class: "link",
                    },
                    children: model.salesperson_model.salesman_name,
                };
            }
            return model.salesperson;
        },
    },
    { key: "item_no", label: "Item No" },
    { key: "item_desc", label: "Item Desc", ellipsis_after: 10 },
    { key: "qty", label: "Qty", remove_decimals: true },
    { key: "ext_sales", label: "Ext Sales", to_format: true },
    { key: "ext_cost", label: "Ext Cost", to_format: true },
    {
        key: "ext_profit",
        label: "GP %",
        custom_value: (model) => {
            const gp =
                ((model.ext_sales - model.ext_cost) / model.ext_sales) * 100;
            return isNaN(gp) ? "-" : gp.toFixed(2) + "%";
        },
    },
    {
        key: "period",
        label: "Period",
        custom_value: (model) => {
            return dayjs(model.period).format("MMM\u2011YY");
        },
    },
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
