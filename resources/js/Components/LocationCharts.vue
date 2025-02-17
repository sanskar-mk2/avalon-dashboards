<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { router, Link } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import theme from "daisyui/src/theming/themes";
import Color from "colorjs.io";
import ChartDataLabels from "chartjs-plugin-datalabels";

Chart.register(ChartDataLabels);

const props = defineProps({
    location_chart_data: Object,
    top_sales_by_location: Object,
    month: String,
});

const locationChartRef = ref(null);
const gpLocationChartRef = ref(null);
let locationChart = null;
let gpLocationChart = null;
const current_theme = ref(null);

const numberFormatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
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

const createLocationChart = () => {
    if (locationChart) {
        locationChart.destroy();
    }
    if (gpLocationChart) {
        gpLocationChart.destroy();
    }

    // Check if canvas elements exist before creating charts
    if (!locationChartRef.value || !gpLocationChartRef.value) {
        return;
    }

    const colors = Object.keys(getThemeColor.value)
        .filter(
            (key) =>
                key !== "base-100" &&
                key !== "base-200" &&
                key !== "base-250" &&
                key !== "secondary-content"
        )
        .map((key) => getThemeColor.value[key]);

    const chartData = {
        labels: props.location_chart_data.sales_by_location.labels,
        datasets: [
            {
                data: props.location_chart_data.sales_by_location.datasets[0]
                    .data,
                backgroundColor: colors.map((color) => color + "80"),
                borderColor: colors,
                borderWidth: 1,
            },
        ],
    };

    const gpChartData = {
        labels: props.location_chart_data.gp_by_location.labels,
        datasets: [
            {
                data: props.location_chart_data.gp_by_location.datasets[0].data,
                backgroundColor: colors.map((color) => color + "80"),
                borderColor: colors,
                borderWidth: 1,
            },
        ],
    };

    const chartOptions = {
        responsive: false,
        width: 250, // Set specific width
        height: 250, // Set specific height
        maintainAspectRatio: true,
        onHover: (event, elements) => {
            event.native.target.style.cursor = elements.length
                ? "pointer"
                : "default";
        },
        onClick: (event, elements, chart) => {
            if (elements.length > 0) {
                const index = elements[0].index;
                const location = chart.data.labels[index];
                const locationId =
                    props.location_chart_data.location_abbrevation_mapping[
                        location
                    ];
                router.get(route("sales.index"), {
                    "filter[location]": locationId,
                    "filter[period]": props.month,
                });
            }
        },
        plugins: {
            legend: {
                labels: {
                    font: {
                        size: 10, // Smaller legend labels
                    },
                    boxWidth: 10,
                },
            },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        const value = context.raw;
                        return numberFormatter.format(value);
                    },
                },
            },
            datalabels: {
                color: "white",
                formatter: function (value, context) {
                    const label = context.chart.data.labels[context.dataIndex];
                    return label + "\n$" + (value / 1000000).toFixed(1) + "M";
                },
                backgroundColor: function (context) {
                    return context.dataset.borderColor[context.dataIndex];
                },
                textAlign: "center",
                borderRadius: 4,
                padding: 4,
                font: {
                    weight: "bold",
                    size: 10,
                },
                display: function (context) {
                    const dataset = context.dataset.data;
                    const value = dataset[context.dataIndex];
                    return (
                        value >
                        dataset.reduce(
                            (a, b) => parseFloat(a) + parseFloat(b)
                        ) *
                            0.05
                    );
                },
            },
        },
    };

    locationChart = new Chart(locationChartRef.value, {
        type: "pie",
        data: chartData,
        options: chartOptions,
    });

    gpLocationChart = new Chart(gpLocationChartRef.value, {
        type: "pie",
        data: gpChartData,
        options: chartOptions,
    });
};

// Watch for theme changes
new MutationObserver((mutations) => {
    if (mutations.some((m) => m.attributeName === "data-theme")) {
        current_theme.value =
            document.documentElement.getAttribute("data-theme");
        createLocationChart();
    }
}).observe(document.documentElement, {
    attributes: true,
    attributeFilter: ["data-theme"],
});

// Watch for data changes
watch(
    () => props.location_chart_data,
    () => {
        createLocationChart();
    },
    { deep: true }
);

onMounted(() => {
    current_theme.value = document.documentElement.getAttribute("data-theme");
    createLocationChart();
});
</script>

<template>
    <div class="flex justify-center flex-wrap sm:flex-nowrap mt-10 gap-8">
        <div class="sm:basis-1/3 w-full mx-4 sm:mx-0 card bg-base-100 p-4">
            <h2 class="text-lg sm:text-xl font-semibold mb-4">
                Sales By Location
            </h2>
            <div class="flex justify-center">
                <canvas
                    ref="locationChartRef"
                    width="250"
                    height="250"
                ></canvas>
            </div>
        </div>
        <div class="sm:basis-1/3 w-full mx-4 sm:mx-0 card bg-base-100 p-4">
            <h2 class="text-lg sm:text-xl font-semibold mb-4">
                GP By Location
            </h2>
            <div class="flex justify-center">
                <canvas
                    ref="gpLocationChartRef"
                    width="250"
                    height="250"
                ></canvas>
            </div>
        </div>
        <div class="sm:basis-1/3 w-full mx-4 sm:mx-0 card bg-base-100 p-4">
            <h2 class="text-lg sm:text-xl font-semibold mb-4">
                Sales By Location
            </h2>
            <div class="h-full sm:h-[250px] overflow-y-auto">
                <table
                    class="min-w-full bg-base-100 border border-base-250 rounded-lg overflow-hidden"
                >
                    <thead class="bg-primary sticky top-0">
                        <tr>
                            <th
                                class="px-4 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-primary-content uppercase tracking-wider"
                            >
                                Location
                            </th>
                            <th
                                class="px-4 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-primary-content uppercase tracking-wider"
                            >
                                Sales
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-250">
                        <tr
                            v-for="location in top_sales_by_location"
                            :key="location.id"
                            class="hover:bg-base-200"
                        >
                            <td
                                class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-base-content"
                            >
                                <Link
                                    :href="
                                        route(
                                            'locations.show',
                                            location.location_model.id
                                        )
                                    "
                                    class="link"
                                >
                                    {{
                                        location.location_model
                                            .location_abbreviation
                                    }}
                                </Link>
                            </td>
                            <td
                                class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-base-content"
                            >
                                {{
                                    numberFormatter.format(location.total_sales)
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
