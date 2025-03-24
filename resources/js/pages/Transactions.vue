<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { formatCurrency } from '@/utils/formatters';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: '/transactions',
    },
];

interface Transaction {
    id: number;
    amount: number;
    type: string;
    description: string;
    status: string;
    created_at: string;
}

const props = defineProps<{
    transactions: {
        data: Transaction[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
}>();

const page = usePage();
const search = ref(page.props.search || '');
const sort = ref(page.props.sort || 'desc');

const formatDate = (date: string) => {
    return new Date(date).toLocaleString('ru-RU');
};

const getAmountClass = (type: string) => {
    return type === 'deposit' ? 'text-green-600' : 'text-red-600';
};

const getStatusClass = (status: string) => {
    return {
        'bg-green-100 text-green-800': status === 'completed',
        'bg-yellow-100 text-yellow-800': status === 'pending',
        'bg-red-100 text-red-800': status === 'failed'
    };
};

const updateQuery = debounce(() => {
    router.get('/transactions', {
        search: search.value,
        sort: sort.value,
        page: 1
    }, {
        preserveState: true,
        preserveScroll: true
    });
}, 300);

watch([search, sort], () => {
    updateQuery();
});

const changePage = (page: number) => {
    router.get('/transactions', {
        search: search.value,
        sort: sort.value,
        page
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const handleSortChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    sort.value = target.value;
};
</script>

<template>
    <Head title="Transaction history" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="">
            <div class="mx-auto max-w-7xl">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6 flex items-center justify-between">
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                Transaction history
                            </h1>
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <input
                                        v-model="search"
                                        type="text"
                                        placeholder="Search by description..."
                                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    />
                                </div>
                                <select
                                    v-model="sort"
                                    @change="handleSortChange"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="desc">Date desc</option>
                                    <option value="asc">Date asc</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Description
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Amount
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    <tr v-for="transaction in transactions.data" :key="transaction.id">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(transaction.created_at) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ transaction.description }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <span :class="['font-semibold', getAmountClass(transaction.type)]">
                                                {{ transaction.type === 'deposit' ? '+' : '-' }}
                                                {{ formatCurrency(transaction.amount) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                :class="getStatusClass(transaction.status)"
                                            >
                                                {{ transaction.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ transactions.from }} to {{ transactions.to }} of {{ transactions.total }} records
                            </div>
                            <div class="flex space-x-2">
                                <button
                                    v-for="page in transactions.last_page"
                                    :key="page"
                                    @click="changePage(page)"
                                    class="rounded-lg px-4 py-2 text-sm font-medium"
                                    :class="{
                                        'bg-blue-500 text-white': page === transactions.current_page,
                                        'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600': page !== transactions.current_page
                                    }"
                                >
                                    {{ page }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 