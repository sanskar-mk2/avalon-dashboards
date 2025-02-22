<script setup>
import { ref, onMounted, watch, computed, nextTick } from "vue";
import { router, Link } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import theme from "daisyui/src/theming/themes";
import Color from "colorjs.io";
import ChartDataLabels from "chartjs-plugin-datalabels";

Chart.register(ChartDataLabels);

const props = defineProps({
    sales_by_mfg_code: Object,
    top_sales_by_mfg_code: Object,
    month: String,
    additional_filters: Object,
});

const mfgCodeChartRef = ref(null);
let mfgCodeChart = null;
const current_theme = ref(null);

const numberFormatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
});

const hasData = computed(() => {
    return props.sales_by_mfg_code?.labels?.length > 0;
});

const getThemeColor = computed(() => {
    const theme_obj = theme[current_theme.value];
    if (!theme_obj) {
        return {};
    }
    const color_keys = Object.keys(theme_obj).filter(
        (key) =>
            !key.startsWith("--") &&
            key !== "color-scheme" &&
            key !== "fontFamily"
    );
    const color_obj = {};
    color_keys.forEach((key) => {
        color_obj[key] = new Color(theme_obj[key])
            .toGamut({ space: "srgb" })
            .to("srgb")
            .toString({ format: "hex" });
    });
    return color_obj;
});

const createMfgCodeChart = () => {
    if (mfgCodeChart) {
        mfgCodeChart.destroy();
        mfgCodeChart = null;
    }

    if (!hasData.value) {
        return;
    }

    // Check if canvas element exists before creating chart
    if (!mfgCodeChartRef.value) {
        return;
    }

    const chartData = { ...props.sales_by_mfg_code };
    chartData.datasets = chartData.datasets.map((dataset) => ({
        ...dataset,
        backgroundColor: getThemeColor.value.primary + "80",
        borderColor: getThemeColor.value.primary,
        borderWidth: 1,
    }));

    chartData.datasets.reverse();

    mfgCodeChart = new Chart(mfgCodeChartRef.value, {
        type: "bar",
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 3,
            onHover: (event, elements) => {
                event.native.target.style.cursor = elements.length
                    ? "pointer" 
                    : "default";
            },
            onClick: (event, elements, chart) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const mfg_code = chart.data.labels[index];
                    router.get(route("sales.index"), {
                        "filter[mfg_code]": mfg_code,
                        "filter[period]": props.month,
                        ...props.additional_filters,
                    });
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function (value) {
                            return value >= 1000000
                                ? (value / 1000000).toFixed(1) + "M"
                                : (value / 1000).toFixed(1) + "K";
                        },
                    },
                },
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 10,
                        },
                        boxWidth: 10,
                    },
                },
                datalabels: {
                    color: "white",
                    formatter: function (value) {
                        const formatter = new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: 'USD',
                            notation: 'compact',
                            maximumFractionDigits: 1
                        });
                        return formatter.format(value);
                    },
                    backgroundColor: function (context) {
                        return context.dataset.borderColor;
                    },
                    borderRadius: 4,
                    padding: 4,
                    font: {
                        weight: "bold",
                        size: 10,
                    },
                    display: function (context) {
                        return context.dataset.data[context.dataIndex] !== 0;
                    },
                    anchor: "end",
                    align: "end",
                },
            },
        },
    });
};

// Watch for theme changes
new MutationObserver((mutations) => {
    if (mutations.some((m) => m.attributeName === "data-theme")) {
        current_theme.value =
            document.documentElement.getAttribute("data-theme");
        createMfgCodeChart();
    }
}).observe(document.documentElement, {
    attributes: true,
    attributeFilter: ["data-theme"],
});

// Watch for data changes
watch(
    () => props.sales_by_mfg_code,
    () => {
        nextTick(() => {
            createMfgCodeChart();
        });
    },
    { deep: true }
);

onMounted(() => {
    current_theme.value = document.documentElement.getAttribute("data-theme");
    createMfgCodeChart();
});
</script>

<template>
    <div class="flex justify-center flex-wrap sm:flex-nowrap mt-10 gap-8">
        <div class="sm:basis-1/3 w-full mx-4 sm:mx-0 card bg-base-100 p-4">
            <h2 class="text-lg sm:text-xl font-semibold mb-4">
                Sales By Manufacturer
            </h2>
            <div v-if="!hasData" class="text-center py-12 text-base-content/50">
                No data available
            </div>
            <div v-else class="h-full sm:h-[250px] overflow-y-auto">
                <table
                    class="min-w-full bg-base-100 border border-base-300 rounded-lg overflow-hidden"
                >
                    <thead class="bg-primary sticky top-0">
                        <tr>
                            <th
                                class="px-4 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-primary-content uppercase tracking-wider"
                            >
                                Manufacturer
                            </th>
                            <th
                                class="px-4 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-primary-content uppercase tracking-wider"
                            >
                                Sales
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-300">
                        <tr
                            v-for="mfg in top_sales_by_mfg_code"
                            :key="mfg.mfg_code"
                            class="hover:bg-base-200"
                        >
                            <td
                                class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-base-content"
                            >
                                <Link
                                    :href="
                                        route(
                                            'manufacturers.show',
                                            mfg.mfg_code
                                        )
                                    "
                                    class="link"
                                >
                                    {{ mfg.mfg_code }}
                                </Link>
                            </td>
                            <td
                                class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-base-content"
                            >
                                {{
                                    numberFormatter.format(mfg.total_sales)
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sm:basis-2/3 w-full mx-4 sm:mx-0 card bg-base-100 p-4">
            <h2 class="text-lg sm:text-xl font-semibold mb-4">
                Sales By Manufacturer
            </h2>
            <div v-if="!hasData" class="text-center py-12 text-base-content/50">
                No data available
            </div>
            <div v-else class="h-[250px]">
                <canvas ref="mfgCodeChartRef"></canvas>
            </div>
        </div>
    </div>
</template>
