/**
 * Возвращает список длительностей с шагом 30 минут.
 */
export function buildHalfHourOptions(minHours, maxHours) {
    const opts = [];
    const start = Number(minHours) || 1;
    const end = Number(maxHours) || start;
    for (let i = start; i <= end; i += 0.5) {
        opts.push(Number(i.toFixed(1)));
    }
    return opts;
}

/**
 * Форматирует длительность для UI: "1 ч" или "1 ч 30 мин".
 */
export function formatDurationLabel(hours, hoursShort, minutesShort) {
    const totalMinutes = Math.round((Number(hours) || 0) * 60);
    const h = Math.floor(totalMinutes / 60);
    const m = totalMinutes % 60;
    if (m === 0) return `${h} ${hoursShort}`;
    return `${h} ${hoursShort} ${m} ${minutesShort}`;
}
