<script setup>
import ArrowDown from './ArrowDown.vue';
import Dash from './Dash.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import dayjs from 'dayjs';
const props = defineProps({
    cards_data: Object,
});

const sales_cards = computed(() => {
    const last_sales_month = dayjs(props.cards_data.sales[0].period).format('MMM YYYY');
    const last_month_sales = (props.cards_data.sales[0].total_amount / 1000000).toFixed(2) + 'M';
    const percent_change = ((props.cards_data.sales[0].total_amount - props.cards_data.sales[1].total_amount) / props.cards_data.sales[1].total_amount) * 100;

    return {
        last_sales_month,
        last_month_sales,
        percent_change,
    };
});

const open_orders_cards = computed(() => {
    const last_open_orders_month = dayjs(props.cards_data.open_orders[0].period).format('MMM YYYY');
    const last_month_open_orders = (props.cards_data.open_orders[0].total_amount / 1000000).toFixed(2) + 'M';
    const percent_change = ((props.cards_data.open_orders[0].total_amount - props.cards_data.open_orders[1].total_amount) / props.cards_data.open_orders[1].total_amount) * 100;

    return {
        last_open_orders_month,
        last_month_open_orders,
        percent_change,
    };
});

const locations_cards = computed(() => {
    const last_month = dayjs(props.cards_data.active_locations[0].period).format('MMM YYYY');
    const count = props.cards_data.active_locations[0].count;
    const percent_change = ((props.cards_data.active_locations[0].count - props.cards_data.active_locations[1].count) / props.cards_data.active_locations[1].count) * 100;

    return {
        last_month,
        count,
        percent_change,
    };
});

const salespeople_cards = computed(() => {
    const last_month = dayjs(props.cards_data.active_salespeople[0].period).format('MMM YYYY');
    const count = props.cards_data.active_salespeople[0].count;
    const percent_change = ((props.cards_data.active_salespeople[0].count - props.cards_data.active_salespeople[1].count) / props.cards_data.active_salespeople[1].count) * 100;

    return {
        last_month,
        count,
        percent_change,
    };
});
</script>

<template>
    <div class="grid grid-cols-4 gap-4">
        <div class="stats shadow">
            <div class="stat">
                <div class="stat-title">Total
                    <Link :href="route('sales.index')" class="link">Sales</Link> for {{ sales_cards.last_sales_month }}
                </div>
                <div class="stat-value text-primary">{{ sales_cards.last_month_sales }}</div>
                <div class="stat-desc flex items-center gap-1">
                    <div class="badge" :class="{
                        'bg-success/10 text-success': sales_cards.percent_change > 0,
                        'bg-error/10 text-error': sales_cards.percent_change < 0,
                        'bg-warning/10 text-warning': sales_cards.percent_change === 0
                    }">
                        <ArrowDown v-if="sales_cards.percent_change < 0" class="w-4 h-4 stroke-error" />
                        <ArrowDown v-else-if="sales_cards.percent_change > 0" class="w-4 h-4 stroke-success rotate-180" />
                        <Dash v-else class="w-4 h-4 stroke-warning" />
                        {{ Math.abs(sales_cards.percent_change).toFixed(2) }}%
                    </div>
                    <span>
                        Compared to last month
                    </span>
                </div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-title">Total
                    <Link :href="route('open_orders.index')" class="link">Open Orders</Link> for {{
                        open_orders_cards.last_open_orders_month }}
                </div>
                <div class="stat-value text-primary">{{ open_orders_cards.last_month_open_orders }}</div>
                <div class="stat-desc flex items-center gap-1">
                    <div class="badge" :class="{
                        'bg-success/10 text-success': open_orders_cards.percent_change > 0,
                        'bg-error/10 text-error': open_orders_cards.percent_change < 0,
                        'bg-warning/10 text-warning': open_orders_cards.percent_change === 0
                    }">
                        <ArrowDown v-if="open_orders_cards.percent_change < 0" class="w-4 h-4 stroke-error" />
                        <ArrowDown v-else-if="open_orders_cards.percent_change > 0" class="w-4 h-4 stroke-success rotate-180" />
                        <Dash v-else class="w-4 h-4 stroke-warning" />
                        {{ Math.abs(open_orders_cards.percent_change).toFixed(2) }}%
                    </div>
                    <span>
                        Compared to last month
                    </span>
                </div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-title">Active <Link :href="route('locations.index')" class="link">Locations</Link> for {{ locations_cards.last_month }}</div>
                <div class="stat-value text-primary">{{ locations_cards.count }}</div>
                <div class="stat-desc flex items-center gap-1">
                    <div class="badge" :class="{
                        'bg-success/10 text-success': locations_cards.percent_change > 0,
                        'bg-error/10 text-error': locations_cards.percent_change < 0,
                        'bg-warning/10 text-warning': locations_cards.percent_change === 0
                    }">
                        <ArrowDown v-if="locations_cards.percent_change < 0" class="w-4 h-4 stroke-error" />
                        <ArrowDown v-else-if="locations_cards.percent_change > 0" class="w-4 h-4 stroke-success rotate-180" />
                        <Dash v-else class="w-4 h-4 stroke-warning" />
                        {{ Math.abs(locations_cards.percent_change).toFixed(2) }}%
                    </div>
                    <span>
                        Compared to last month
                    </span>
                </div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-title">Active <Link :href="route('salespeople.index')" class="link">Salespeople</Link> for {{ salespeople_cards.last_month }}</div>
                <div class="stat-value text-primary">{{ salespeople_cards.count }}</div>
                <div class="stat-desc flex items-center gap-1">
                    <div class="badge" :class="{
                        'bg-success/10 text-success': salespeople_cards.percent_change > 0,
                        'bg-error/10 text-error': salespeople_cards.percent_change < 0,
                        'bg-warning/10 text-warning': salespeople_cards.percent_change === 0
                    }">
                        <ArrowDown v-if="salespeople_cards.percent_change < 0" class="w-4 h-4 stroke-error" />
                        <ArrowDown v-else-if="salespeople_cards.percent_change > 0" class="w-4 h-4 stroke-success rotate-180" />
                        <Dash v-else class="w-4 h-4 fill-warning" />
                        {{ Math.abs(salespeople_cards.percent_change).toFixed(2) }}%
                    </div>
                    <span>
                        Compared to last month
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>