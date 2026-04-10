/**
 * Форматирует номер телефона гостя: только +993 и до 8 цифр после (формат Туркменистана).
 * @param {string} value - сырое значение поля
 * @returns {string} - "+993" + до 8 цифр или пустая строка
 */
/** Значение по умолчанию: префикс показывается заранее. */
export const GUEST_PHONE_PREFIX = '+993';

export function formatGuestPhone(value) {
    if (value == null || value === '') return GUEST_PHONE_PREFIX;
    const digits = String(value).replace(/\D/g, '');
    if (digits.length === 0) return GUEST_PHONE_PREFIX;
    if (digits.startsWith('993')) {
        return '+993' + digits.slice(3, 11);
    }
    return '+993' + digits.slice(0, 8);
}
