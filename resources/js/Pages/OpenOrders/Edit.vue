<template>
    <PageLayout title="Edit Open Order" :breadcrumbs="breadcrumbs">
        <form
            @submit.prevent="
                form.put(route('open_orders.update', openOrder.id))
            "
            class="max-w-3xl mx-auto"
        >
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Basic Information -->
                        <div class="form-control">
                            <label class="label">Company</label>
                            <input
                                type="text"
                                v-model="form.company"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Location</label>
                            <input
                                type="text"
                                v-model="form.location"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Order No</label>
                            <input
                                type="number"
                                v-model="form.order_no"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Backorder</label>
                            <input
                                type="checkbox"
                                v-model="form.backorder"
                                class="toggle"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Order Date</label>
                            <input
                                type="date"
                                v-model="form.order_date"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Order Type</label>
                            <input
                                type="text"
                                v-model="form.order_type"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <!-- Customer Information -->
                        <div class="form-control">
                            <label class="label">Customer No</label>
                            <input
                                type="text"
                                v-model="form.customer_no"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Customer Name</label>
                            <input
                                type="text"
                                v-model="form.customer_name"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Customer Class</label>
                            <input
                                type="text"
                                v-model="form.customer_class"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <!-- Product Information -->
                        <div class="form-control">
                            <label class="label">Brand</label>
                            <input
                                type="text"
                                v-model="form.brand"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Flag</label>
                            <input
                                type="text"
                                v-model="form.flag"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Salesperson</label>
                            <input
                                type="number"
                                v-model="form.salesperson"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Invoice No</label>
                            <input
                                type="number"
                                v-model="form.invoice_no"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Invoice Date</label>
                            <input
                                type="date"
                                v-model="form.invoice_date"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <!-- Item Information -->
                        <div class="form-control">
                            <label class="label">Item No</label>
                            <input
                                type="text"
                                v-model="form.item_no"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Item Description</label>
                            <input
                                type="text"
                                v-model="form.item_desc"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Item Division</label>
                            <input
                                type="number"
                                v-model="form.item_division"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Inventory Class</label>
                            <input
                                type="number"
                                v-model="form.inv_class"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Quantity</label>
                            <input
                                type="number"
                                v-model="form.qty"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <!-- Editable Fields -->
                        <div class="form-control">
                            <label class="label">Extended Sales</label>
                            <input
                                type="number"
                                step="0.01"
                                v-model="form.ext_sales"
                                class="input input-bordered"
                                :class="{
                                    'input-error': form.errors.ext_sales,
                                }"
                            />
                            <div
                                v-if="form.errors.ext_sales"
                                class="text-error text-sm mt-1"
                            >
                                {{ form.errors.ext_sales }}
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label">Extended Cost</label>
                            <input
                                type="number"
                                step="0.01"
                                v-model="form.ext_cost"
                                class="input input-bordered"
                                :class="{ 'input-error': form.errors.ext_cost }"
                            />
                            <div
                                v-if="form.errors.ext_cost"
                                class="text-error text-sm mt-1"
                            >
                                {{ form.errors.ext_cost }}
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="form-control">
                            <label class="label">Period</label>
                            <input
                                type="date"
                                v-model="form.period"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Order Status</label>
                            <input
                                type="text"
                                v-model="form.order_status"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Advertising Source</label>
                            <input
                                type="text"
                                v-model="form.advertising_source"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Finance Co Rate</label>
                            <input
                                type="number"
                                step="0.01"
                                v-model="form.finance_co_rate"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Price Matrix</label>
                            <input
                                type="text"
                                v-model="form.price_matrix"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Price List Applied</label>
                            <input
                                type="text"
                                v-model="form.price_list_applied"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Price After Discount</label>
                            <input
                                type="number"
                                step="0.0001"
                                v-model="form.price_after_disc"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <!-- Shipping Information -->
                        <div class="form-control">
                            <label class="label">Ship To No</label>
                            <input
                                type="text"
                                v-model="form.ship_to_no"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Ship To Name</label>
                            <input
                                type="text"
                                v-model="form.ship_to_name"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Ship To City</label>
                            <input
                                type="text"
                                v-model="form.ship_to_city"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Ship To State</label>
                            <input
                                type="text"
                                v-model="form.ship_to_state"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Requested Ship Date</label>
                            <input
                                type="date"
                                v-model="form.requested_ship_date"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">Customer Desire Date</label>
                            <input
                                type="date"
                                v-model="form.customer_desire_date"
                                class="input input-bordered"
                                disabled
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">MFG Code</label>
                            <input
                                type="text"
                                v-model="form.mfg_code"
                                class="input input-bordered"
                                disabled
                            />
                        </div>
                    </div>

                    <div class="card-actions justify-end mt-4">
                        <Link :href="route('open_orders.index')" class="btn"
                            >Cancel</Link
                        >
                        <button
                            type="submit"
                            class="btn btn-primary"
                            :disabled="form.processing"
                        >
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </PageLayout>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import PageLayout from "@/Components/PageLayout.vue";

const props = defineProps({
    openOrder: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    company: props.openOrder.company,
    location: props.openOrder.location,
    order_no: props.openOrder.order_no,
    backorder: props.openOrder.backorder,
    order_date: props.openOrder.order_date,
    order_type: props.openOrder.order_type,
    customer_no: props.openOrder.customer_no,
    customer_name: props.openOrder.customer_name,
    customer_class: props.openOrder.customer_class,
    brand: props.openOrder.brand,
    flag: props.openOrder.flag,
    salesperson: props.openOrder.salesperson,
    invoice_no: props.openOrder.invoice_no,
    invoice_date: props.openOrder.invoice_date,
    item_no: props.openOrder.item_no,
    item_desc: props.openOrder.item_desc,
    item_division: props.openOrder.item_division,
    inv_class: props.openOrder.inv_class,
    qty: props.openOrder.qty,
    ext_sales: props.openOrder.ext_sales,
    ext_cost: props.openOrder.ext_cost,
    period: props.openOrder.period,
    order_status: props.openOrder.order_status,
    advertising_source: props.openOrder.advertising_source,
    finance_co_rate: props.openOrder.finance_co_rate,
    price_matrix: props.openOrder.price_matrix,
    price_list_applied: props.openOrder.price_list_applied,
    price_after_disc: props.openOrder.price_after_disc,
    ship_to_no: props.openOrder.ship_to_no,
    ship_to_name: props.openOrder.ship_to_name,
    ship_to_city: props.openOrder.ship_to_city,
    ship_to_state: props.openOrder.ship_to_state,
    requested_ship_date: props.openOrder.requested_ship_date,
    customer_desire_date: props.openOrder.customer_desire_date,
    mfg_code: props.openOrder.mfg_code,
});

const breadcrumbs = [
    { label: "Home", route: "dashboard" },
    { label: "Open Orders", route: "open_orders.index" },
    { label: "Edit Order #" + props.openOrder.order_no },
];
</script>
