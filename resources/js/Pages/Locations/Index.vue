<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";

const props = defineProps({
    locations: {
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

    <Head title="Locations" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                <div class="breadcrumbs text-sm text-base-content">
                    <ul>
                        <li><Link :href="route('dashboard')">Home</Link></li>
                        <li>Locations</li>
                    </ul>
                </div>
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-base-100 shadow-sm sm:rounded-lg">
                    <div class="p-6 ">
                        <div class="flex justify-end gap-4 mb-4">
                            <Link :href="route('locations.create')" class="btn btn-primary">
                            Upload Location CSV
                            </Link>
                            <button @click="openDeleteModal" class="btn btn-error">
                                Delete All Location Records
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Location Name</th>
                                        <th>Location Abbreviation</th>
                                        <th>Sales</th>
                                        <th>Open Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="location in locations.data" :key="location.id">
                                        <td>{{ location.location }}</td>
                                        <td>{{ location.location_name }}</td>
                                        <td>
                                            <Link class="link" :href="route('locations.show', location.id)">
                                                {{ location.location_abbreviation }}
                                            </Link>
                                        </td>
                                        <td>{{ location.sales_count }}</td>
                                        <td>{{ location.open_orders_count }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 flex justify-between w-full items-center">
                            <div class="text-base-content text-md">
                                Showing {{ locations.from }} to {{ locations.to }} of {{ locations.total }}
                                results
                            </div>
                            <Pagination :links="locations.links" />
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <dialog :open="showDeleteModal" class="modal">
                            <div class="modal-box border-2 border-base-300">
                                <h3 class="text-lg font-bold">Confirm Deletion</h3>
                                <p class="py-4">Are you sure you want to delete all location records? This action
                                    cannot be
                                    undone.</p>
                                <div class="modal-action">
                                    <Link :href="route('locations.deleteAll')" method="delete" as="button"
                                        class="btn btn-error" preserve-scroll @click="closeDeleteModal">
                                    Delete All
                                    </Link>
                                    <button class="btn" @click="closeDeleteModal">Cancel</button>
                                </div>
                            </div>
                        </dialog>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
