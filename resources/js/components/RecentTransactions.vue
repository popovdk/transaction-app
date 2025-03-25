<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { formatCurrency } from '@/utils/formatters';

interface Transaction {
    id: number;
    amount: number;
    type: string;
    description: string;
    status: string;
    created_at: string;
}

const transactions = ref<Transaction[]>([]);
let intervalId: number | null = null;

const fetchTransactions = async () => {
    try {
        const response = await axios.get('/api/recent-transactions');
        transactions.value = response.data.data.map((transaction: any) => ({
            ...transaction,
            amount: Number(transaction.amount)
        }));
    } catch (error) {
        console.error('Error fetching transactions:', error);
    }
};

onMounted(() => {
    fetchTransactions();
    // Обновляем транзакции каждые 30 секунд
    intervalId = window.setInterval(fetchTransactions, 30000);
});

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleString('ru-RU');
};

const getAmountClass = (type: string) => {
    return type === 'deposit' ? 'text-green-600' : 'text-red-600';
};
</script>

<template>
    <div class="flex h-full flex-col p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent operations</h2>
        <div class="mt-4 flex-1 overflow-y-auto">
            <div v-if="transactions.length === 0" class="text-center text-gray-500">
                There are no operations available
            </div>
            <div v-else class="space-y-4">
                <div v-for="transaction in transactions" :key="transaction.id" 
                    class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ transaction.description }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ formatDate(transaction.created_at) }}
                            </p>
                        </div>
                        <p :class="['font-semibold', getAmountClass(transaction.type)]">
                            {{ transaction.type === 'deposit' ? '+' : '-' }}
                            {{ formatCurrency(transaction.amount) }}
                        </p>
                    </div>
                    <div class="mt-2">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="{
                                'bg-green-100 text-green-800': transaction.status === 'completed',
                                'bg-yellow-100 text-yellow-800': transaction.status === 'pending',
                                'bg-red-100 text-red-800': transaction.status === 'failed'
                            }">
                            {{ transaction.status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 