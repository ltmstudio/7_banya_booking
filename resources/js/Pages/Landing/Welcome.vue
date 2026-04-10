<script setup>
/**
 * Публичная главная страница — лендинг банного комплекса.
 * Получает rooms, extraServices, popupPromo с бэкенда; передаёт в секции, управляет попапом.
 */
import LandingLayout from '@/Layouts/LandingLayout.vue';
import LandingHero from '@/Components/Landing/LandingHero.vue';
import LandingAbout from '@/Components/Landing/LandingAbout.vue';
import LandingServices from '@/Components/Landing/LandingServices.vue';
import LandingGallery from '@/Components/Landing/LandingGallery.vue';
import LandingBooking from '@/Components/Landing/LandingBooking.vue';
import PopupPromo from '@/Components/Public/PopupPromo.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useSyncLocale } from '@/Composables/useSyncLocale';

useSyncLocale();

const props = defineProps({
    canLogin: { type: Boolean },
    rooms: { type: Array, default: () => [] },
    extraServices: { type: Array, default: () => [] },
    popupPromo: { type: Object, default: null },
    landing: { type: Object, default: () => ({}) },
    bookingSettings: { type: Object, default: () => ({ min_hours: 1, max_hours: 10 }) },
});

const meta = computed(() => usePage().props.landing?.meta || {});
const currentLocale = computed(() => (usePage().props.locale ?? 'ru'));

const showPromo = ref(!localStorage.getItem('promo_shown') && !!props.popupPromo);
function closePromo() {
    localStorage.setItem('promo_shown', '1');
    showPromo.value = false;
}
</script>

<template>
    <LandingLayout>
        <Head :title="meta.title || 'Главная — Банный комплекс'" />
        <LandingHero />
        <LandingBooking
            :rooms="rooms"
            :extra-services="extraServices"
            :booking-settings="bookingSettings"
        />
        <LandingAbout />
        <LandingServices :extra-services="extraServices" />
        <LandingGallery :rooms="rooms" />
        <PopupPromo
            v-if="showPromo && popupPromo"
            :promo="popupPromo"
            :locale="currentLocale"
            @close="closePromo"
        />
    </LandingLayout>
</template>

