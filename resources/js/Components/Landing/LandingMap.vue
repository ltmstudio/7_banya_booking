<script setup>
/**
 * Карта для быстрого поиска банного комплекса (Leaflet).
 * Переключатель: Схема (по умолчанию), Спутник.
 */
import { onMounted, onUnmounted, ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    latitude: { type: Number, default: 37.95 },
    longitude: { type: Number, default: 58.38 },
    title: { type: String, default: 'Банный комплекс' },
});

const { t } = useI18n();
const mapContainer = ref(null);
const activeMode = ref('street');
let map = null;
let marker = null;
let currentLayer = null;

const layers = {
    street: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
    }),
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '&copy; Esri',
    }),
};

function setLayer(mode) {
    if (!map || !layers[mode]) return;
    if (currentLayer) map.removeLayer(currentLayer);
    currentLayer = layers[mode];
    map.addLayer(currentLayer);
    activeMode.value = mode;
}

onMounted(() => {
    if (!mapContainer.value) return;

    map = L.map(mapContainer.value).setView([props.latitude, props.longitude], 15);
    setLayer('street');

    const icon = L.divIcon({
        className: 'custom-marker',
        html: `<div style="display:flex;align-items:center;justify-content:center;width:44px;height:44px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#d97706" width="44" height="44" style="filter:drop-shadow(0 2px 4px rgba(0,0,0,0.4))">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
        </div>`,
        iconSize: [44, 44],
        iconAnchor: [22, 44],
    });
    marker = L.marker([props.latitude, props.longitude], { icon })
        .addTo(map)
        .bindPopup(props.title);
});

onUnmounted(() => {
    if (map) {
        map.remove();
        map = null;
    }
});
</script>

<template>
    <section class="w-full" :aria-label="t('landing.map.aria_label')">
        <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
            <div class="flex gap-2">
            <button
                type="button"
                :class="[
                    'rounded-lg px-3 py-1.5 text-sm font-medium transition-colors',
                    activeMode === 'street'
                        ? 'bg-amber-600 text-white'
                        : 'bg-stone-200 text-stone-700 hover:bg-stone-300 dark:bg-stone-700 dark:text-stone-300 dark:hover:bg-stone-600'
                ]"
                @click="setLayer('street')"
            >
                {{ t('landing.map.scheme') }}
                </button>
                <button
                type="button"
                :class="[
                    'rounded-lg px-3 py-1.5 text-sm font-medium transition-colors',
                    activeMode === 'satellite'
                        ? 'bg-amber-600 text-white'
                        : 'bg-stone-200 text-stone-700 hover:bg-stone-300 dark:bg-stone-700 dark:text-stone-300 dark:hover:bg-stone-600'
                ]"
                @click="setLayer('satellite')"
            >
                {{ t('landing.map.satellite') }}
                </button>
            </div>
            <p class="text-sm text-stone-600 dark:text-stone-400">
                {{ title }} · {{ t('landing.map.latitude') }}: {{ latitude.toFixed(6) }}, {{ t('landing.map.longitude') }}: {{ longitude.toFixed(6) }}
            </p>
        </div>
        <div
            ref="mapContainer"
            class="h-80 w-full rounded-xl overflow-hidden border border-stone-200 dark:border-stone-600"
        />
    </section>
</template>
