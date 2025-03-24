/**
 * Форматирует число в денежный формат в USD
 * @param amount - сумма для форматирования
 * @returns отформатированная строка с валютой
 */
export const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-US', { 
    style: 'currency', 
    currency: 'USD', 
    currencyDisplay: 'narrowSymbol'
  }).format(amount);
}; 