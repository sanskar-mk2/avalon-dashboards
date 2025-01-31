<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";

const props = defineProps({
    salespeople: {
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
    <Head title="Salespeople" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-lg sm:text-xl font-semibold leading-tight">
                <div class="breadcrumbs text-xs sm:text-sm text-base-content">
                    <ul>
                        <li><Link :href="route('dashboard')">Home</Link></li>
                        <li>Salespeople</li>
                    </ul>
                </div>
            </h2>
        </template>

        <div class="py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-base-100 shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex justify-end gap-2 sm:gap-4 mb-4">
                            <Link :href="route('salespeople.create')" class="btn btn-primary btn-sm sm:btn-md text-xs sm:text-sm">
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
                                        <th>Company No</th>
                                        <th>Salesman No</th>
                                        <th>Salesman Name</th>
                                        <th>Sales</th>
                                        <th>Open Orders</th>
                                        <th>As Of Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="salesperson in salespeople.data" :key="salesperson.id">
                                        <td>{{ salesperson.company_no }}</td>
                                        <td>{{ salesperson.salesman_no }}</td>
                                        <td>
                                            <Link class="link" :href="route('salespeople.show', salesperson.id)">
                                                {{ salesperson.salesman_name }}
                                            </Link>
                                        </td>
                                        <td>{{ salesperson.sales_count }}</td>
                                        <td>{{ salesperson.open_orders_count }}</td>
                                        <td>{{ salesperson.as_of_date }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 flex justify-between w-full items-center">
                            <div class="text-base-content text-xs sm:text-sm">
                                Showing {{ salespeople.from }} to {{ salespeople.to }} of {{ salespeople.total }}
                                results
                            </div>
                            <Pagination :links="salespeople.links" />
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <dialog :open="showDeleteModal" class="modal">
                            <div class="modal-box border-2 border-base-300">
                                <h3 class="text-base sm:text-lg font-bold">Confirm Deletion</h3>
                                <p class="py-4 text-xs sm:text-sm">Are you sure you want to delete all salesperson records? This action cannot be undone.</p>
                                <div class="modal-action">
                                    <Link :href="route('salespeople.deleteAll')" method="delete" as="button"
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
