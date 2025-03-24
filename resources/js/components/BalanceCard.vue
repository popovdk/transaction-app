<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { formatCurrency } from '@/utils/formatters';

const balance = ref<number>(0);
let intervalId: number | null = null;

const fetchBalance = async () => {
    try {
        const response = await axios.get('/api/balance');
        balance.value = Number(response.data.balance);
    } catch (error) {
        console.error('Error fetching balance:', error);
    }
};

onMounted(() => {
    fetchBalance();
    // Обновляем баланс каждые 30 секунд
    intervalId = window.setInterval(fetchBalance, 30000);
});

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
});
</script>

<template>
    <div class="flex h-full flex-col justify-between p-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Current balance
            </h2>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                {{ formatCurrency(balance) }}
            </p>
        </div>
        <div class="mt-4">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Refreshes automatically every 30 seconds
            </p>
        </div>
    </div>
</template> 