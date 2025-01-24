<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { onUpdated, ref } from "vue";
import Pagination from "@/Components/Pagination.vue";

const showSuccess = ref(false);
const showError = ref(false);
const flashMessage = ref('');

const props = defineProps({
    flash: {
        type: Object,
        default: () => ({}),
    },
    open_orders: {
        type: Object,
        default: () => ({}),
    },
});

onUpdated(() => {
    if (props.flash.success) {
        showSuccess.value = true;
        flashMessage.value = props.flash.success;
        setTimeout(() => {
            showSuccess.value = false;
        }, 3000);
    }
    if (props.flash.error) {
        showError.value = true;
        flashMessage.value = props.flash.error;
        setTimeout(() => {
            showError.value = false;
        }, 3000);
    }
});
</script>

<template>

    <Head title="Open Orders" />

    <AuthenticatedLayout>
        <div class="toast toast-top toast-end" v-if="showSuccess || showError">
            <div class="alert alert-success" v-if="showSuccess">
                <span>{{ flashMessage }}</span>
            </div>
            <div class="alert alert-error" v-if="showError">
                <span>{{ flashMessage }}</span>
            </div>
        </div>

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Open Orders
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-base-100 shadow-sm sm:rounded-lg">
                    <div class="p-6 ">
                        <div class="flex justify-end gap-4 mb-4">
                            <Link :href="route('open_orders.create')" class="btn btn-primary">
                            Upload Open Order CSV
                            </Link>
                            <Link :href="route('open_orders.deleteAll')" method="delete" as="button"
                                class="btn btn-error" preserve-scroll>
                            Delete All Open Order Records
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Location</th>
                                        <th>Order No</th>
                                        <th>Backorder</th>
                                        <th>Order Date</th>
                                        <th>Order Type</th>
                                        <th>Customer No</th>
                                        <th>Customer Name</th>
                                        <th>Customer Class</th>
                                        <th>Brand</th>
                                        <th>Flag</th>
                                        <th>Salesperson</th>
                                        <th>Invoice No</th>
                                        <th>Invoice Date</th>
                                        <th>Item No</th>
                                        <th>Item Desc</th>
                                        <th>Item Division</th>
                                        <th>Inv Class</th>
                                        <th>Qty</th>
                                        <th>Ext Sales</th>
                                        <th>Ext Cost</th>
                                        <th>Period</th>
                                        <th>Order Status</th>
                                        <th>Advertising Source</th>
                                        <th>Finance Co Rate</th>
                                        <th>Price Matrix</th>
                                        <th>Price List Applied</th>
                                        <th>Price After Disc</th>
                                        <th>Ship To No</th>
                                        <th>Ship To Name</th>
                                        <th>Ship To City</th>
                                        <th>Ship To State</th>
                                        <th>Requested Ship Date</th>
                                        <th>Customer Desire Date</th>
                                        <th>Mfg Code</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="open_order in open_orders.data" :key="open_order.id">
                                        <td>{{ open_order.company }}</td>
                                        <td>{{ open_order.location }}</td>
                                        <td>{{ open_order.order_no }}</td>
                                        <td>{{ open_order.backorder }}</td>
                                        <td>{{ open_order.order_date }}</td>
                                        <td>{{ open_order.order_type }}</td>
                                        <td>{{ open_order.customer_no }}</td>
                                        <td>{{ open_order.customer_name }}</td>
                                        <td>{{ open_order.customer_class }}</td>
                                        <td>{{ open_order.brand }}</td>
                                        <td>{{ open_order.flag }}</td>
                                        <td>{{ open_order.salesperson }}</td>
                                        <td>{{ open_order.invoice_no }}</td>
                                        <td>{{ open_order.invoice_date }}</td>
                                        <td>{{ open_order.item_no }}</td>
                                        <td>{{ open_order.item_desc }}</td>
                                        <td>{{ open_order.item_division }}</td>
                                        <td>{{ open_order.inv_class }}</td>
                                        <td>{{ open_order.qty }}</td>
                                        <td>{{ open_order.ext_sales }}</td>
                                        <td>{{ open_order.ext_cost }}</td>
                                        <td>{{ open_order.period }}</td>
                                        <td>{{ open_order.order_status }}</td>
                                        <td>{{ open_order.advertising_source }}</td>
                                        <td>{{ open_order.finance_co_rate }}</td>
                                        <td>{{ open_order.price_matrix }}</td>
                                        <td>{{ open_order.price_list_applied }}</td>
                                        <td>{{ open_order.price_after_disc }}</td>
                                        <td>{{ open_order.ship_to_no }}</td>
                                        <td>{{ open_order.ship_to_name }}</td>
                                        <td>{{ open_order.ship_to_city }}</td>
                                        <td>{{ open_order.ship_to_state }}</td>
                                        <td>{{ open_order.requested_ship_date }}</td>
                                        <td>{{ open_order.customer_desire_date }}</td>
                                        <td>{{ open_order.mfg_code }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 flex justify-between w-full items-center">
                            <div class="text-base-content text-md">
                                Showing {{ open_orders.from }} to {{ open_orders.to }} of {{ open_orders.total }}
                                results
                            </div>
                            <Pagination :links="open_orders.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
