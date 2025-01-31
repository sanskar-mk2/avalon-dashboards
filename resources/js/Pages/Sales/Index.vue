<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    sales: {
        type: Object,
        default: () => ({}),
    },
});

const showDeleteModal = ref(false);

const openDeleteModal = () => {
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
};
</script>

<template>
    <Head title="Sales" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-lg sm:text-xl font-semibold leading-tight">
                <div class="breadcrumbs text-xs sm:text-sm text-base-content">
                    <ul>
                        <li><Link :href="route('dashboard')">Home</Link></li>
                        <li>Sales</li>
                    </ul>
                </div>
            </h2>
        </template>

        <div class="py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-base-100 shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex justify-end gap-2 sm:gap-4 mb-4">
                            <Link :href="route('sales.create')" class="btn btn-primary btn-sm sm:btn-md text-xs sm:text-sm">
                            Upload CSV
                            </Link>
                            <button @click="openDeleteModal" class="btn btn-error btn-sm sm:btn-md text-xs sm:text-sm">
                                Delete All
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra text-xs sm:text-sm">
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
                                    <tr v-for="sale in sales.data" :key="sale.id">
                                        <td>{{ sale.company }}</td>
                                        <td>
                                            <template v-if="sale.location_model">
                                                <Link class="link" :href="route('locations.show', sale.location_model.id)">
                                                {{ sale.location_model.location_abbreviation }}
                                                </Link>
                                            </template>
                                            <template v-else>
                                                {{ sale.location }}
                                            </template>
                                        </td>
                                        <td>{{ sale.order_no }}</td>
                                        <td>{{ sale.backorder }}</td>
                                        <td>{{ sale.order_date }}</td>
                                        <td>{{ sale.order_type }}</td>
                                        <td>{{ sale.customer_no }}</td>
                                        <td>{{ sale.customer_name }}</td>
                                        <td>{{ sale.customer_class }}</td>
                                        <td>{{ sale.brand }}</td>
                                        <td>{{ sale.flag }}</td>
                                        <td>
                                            <template v-if="sale.salesperson_model">
                                                <Link class="link"
                                                    :href="route('salespeople.show', sale.salesperson_model.id)">
                                                {{ sale.salesperson_model.salesman_name }}
                                                </Link>
                                            </template>
                                            <template v-else>
                                                {{ sale.salesperson }}
                                            </template>
                                        </td>
                                        <td>{{ sale.invoice_no }}</td>
                                        <td>{{ sale.invoice_date }}</td>
                                        <td>{{ sale.item_no }}</td>
                                        <td>{{ sale.item_desc }}</td>
                                        <td>{{ sale.item_division }}</td>
                                        <td>{{ sale.inv_class }}</td>
                                        <td>{{ sale.qty }}</td>
                                        <td>{{ sale.ext_sales }}</td>
                                        <td>{{ sale.ext_cost }}</td>
                                        <td>{{ sale.period }}</td>
                                        <td>{{ sale.order_status }}</td>
                                        <td>{{ sale.advertising_source }}</td>
                                        <td>{{ sale.finance_co_rate }}</td>
                                        <td>{{ sale.price_matrix }}</td>
                                        <td>{{ sale.price_list_applied }}</td>
                                        <td>{{ sale.price_after_disc }}</td>
                                        <td>{{ sale.ship_to_no }}</td>
                                        <td>{{ sale.ship_to_name }}</td>
                                        <td>{{ sale.ship_to_city }}</td>
                                        <td>{{ sale.ship_to_state }}</td>
                                        <td>{{ sale.requested_ship_date }}</td>
                                        <td>{{ sale.customer_desire_date }}</td>
                                        <td>{{ sale.mfg_code }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 flex justify-between w-full items-center">
                            <div class="text-base-content text-xs sm:text-sm">
                                Showing {{ sales.from }} to {{ sales.to }} of {{ sales.total }} results
                            </div>
                            <Pagination :links="sales.links" />
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <dialog :open="showDeleteModal" class="modal">
                            <div class="modal-box border-2 border-base-300">
                                <h3 class="text-base sm:text-lg font-bold">Confirm Deletion</h3>
                                <p class="py-4 text-xs sm:text-sm">Are you sure you want to delete all sales records? This action cannot be
                                    undone.
                                </p>
                                <div class="modal-action">
                                    <Link :href="route('sales.deleteAll')" method="delete" as="button"
                                        class="btn btn-error text-xs sm:text-sm" preserve-scroll @click="closeDeleteModal">
                                    Delete All
                                    </Link>
                                    <button class="btn text-xs sm:text-sm" @click="closeDeleteModal">Cancel</button>
                                </div>
                            </div>
                        </dialog>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
