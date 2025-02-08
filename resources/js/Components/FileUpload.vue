<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    storeRoute: {
        type: String,
        required: true,
    },
});

const form = useForm({
    file: null,
});

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const submit = () => {
    form.post(route(props.storeRoute), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-3 sm:space-y-4">
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
            <button
                type="submit"
                :disabled="!form.file"
                class="btn btn-primary text-sm sm:text-base"
            >
                Upload CSV
            </button>
        </div>
    </form>
</template>
