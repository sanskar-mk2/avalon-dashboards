<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    storeRoute: {
        type: String,
        required: true,
    },
    checkExistingRoute: {
        type: String,
        required: true,
    },
    showDateSelectors: {
        type: Boolean,
        default: true,
    },
});

const form = useForm({
    file: null,
    month: new Date().getMonth() + 1, // Current month
    year: new Date().getFullYear(), // Current year
    replace: false,
});

const showConfirmDialog = ref(false);
const isLoading = ref(false);
const isUploading = ref(false);

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const checkExistingData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.post(route(props.checkExistingRoute), {
            month: form.month,
            year: form.year,
        });

        if (response.data.exists) {
            showConfirmDialog.value = true;
        } else {
            submitForm();
        }
    } catch (error) {
        console.error("Error checking existing data:", error);
    } finally {
        isLoading.value = false;
    }
};

const submitForm = () => {
    form.post(route(props.storeRoute), {
        preserveScroll: true,
        onStart: () => (isUploading.value = true),
        onFinish: () => {
            isUploading.value = false;
            form.reset();
            form.month = new Date().getMonth() + 1;
            form.year = new Date().getFullYear();
            showConfirmDialog.value = false;
        },
    });
};

const handleSubmit = (e) => {
    e.preventDefault();
    if (!form.file) return;
    checkExistingData();
};

const closeModal = () => {
    showConfirmDialog.value = false;
    form.replace = false; // Reset replace flag when closing modal
};

const months = [
    { value: 1, label: "January" },
    { value: 2, label: "February" },
    { value: 3, label: "March" },
    { value: 4, label: "April" },
    { value: 5, label: "May" },
    { value: 6, label: "June" },
    { value: 7, label: "July" },
    { value: 8, label: "August" },
    { value: 9, label: "September" },
    { value: 10, label: "October" },
    { value: 11, label: "November" },
    { value: 12, label: "December" },
];

const years = Array.from(
    { length: 10 },
    (_, i) => new Date().getFullYear() - 5 + i
);
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-3 sm:space-y-4">
        <div class="grid w-full max-w-sm items-center gap-1.5">
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text text-sm sm:text-base"
                        >{{ label }} CSV File</span
                    >
                </div>
                <input
                    type="file"
                    @input="handleFileUpload"
                    accept=".csv"
                    class="file-input file-input-bordered w-full max-w-xs text-sm sm:text-base"
                />
                <div class="label">
                    <span class="label-text-alt text-xs sm:text-sm"
                        >Please upload a CSV file</span
                    >
                </div>
            </label>

            <fieldset v-if="showDateSelectors" class="border rounded-lg p-4">
                <legend class="text-sm sm:text-base px-2">Uploading for</legend>
                <div class="flex gap-4">
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-sm sm:text-base"
                                >Month</span
                            >
                        </div>
                        <select
                            v-model="form.month"
                            class="select select-bordered w-full max-w-xs text-sm sm:text-base"
                        >
                            <option
                                v-for="month in months"
                                :key="month.value"
                                :value="month.value"
                            >
                                {{ month.label }}
                            </option>
                        </select>
                    </label>

                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text text-sm sm:text-base"
                                >Year</span
                            >
                        </div>
                        <select
                            v-model="form.year"
                            class="select select-bordered w-full max-w-xs text-sm sm:text-base"
                        >
                            <option
                                v-for="year in years"
                                :key="year"
                                :value="year"
                            >
                                {{ year }}
                            </option>
                        </select>
                    </label>
                </div>
            </fieldset>

            <button
                type="submit"
                :disabled="!form.file || isLoading || isUploading"
                class="btn btn-primary text-sm sm:text-base"
            >
                <span
                    v-if="isLoading || isUploading"
                    class="loading loading-spinner"
                ></span>
                Upload CSV
            </button>
        </div>

        <!-- Confirmation Dialog -->
        <div v-if="showConfirmDialog" class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Data Already Exists</h3>
                <p class="py-4">
                    Data for this month already exists. Would you like to
                    replace the existing data or append new records?
                </p>
                <div class="modal-action">
                    <button
                        @click="
                            form.replace = true;
                            submitForm();
                        "
                        :disabled="isUploading"
                        class="btn btn-error"
                    >
                        Replace
                    </button>
                    <button
                        @click="
                            form.replace = false;
                            submitForm();
                        "
                        :disabled="isUploading"
                        class="btn btn-primary"
                    >
                        Append
                    </button>
                    <button
                        @click="closeModal"
                        :disabled="isUploading"
                        class="btn"
                    >
                        Cancel
                    </button>
                </div>
            </div>
            <div class="modal-backdrop" @click="closeModal"></div>
        </div>
    </form>
</template>
