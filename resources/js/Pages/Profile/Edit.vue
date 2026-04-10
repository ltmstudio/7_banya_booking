<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

</script>

<template>
    <Head title="Профиль" />

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">Профиль</h1>
        </template>

        <div class="mx-auto max-w-3xl space-y-6">
            <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <UpdateProfileInformationForm class="max-w-xl" />
            </div>
            <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <UpdatePasswordForm class="max-w-xl" />
            </div>
            <!-- <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <DeleteUserForm class="max-w-xl" />
            </div> -->
        </div>
    </DashboardLayout>
</template>
