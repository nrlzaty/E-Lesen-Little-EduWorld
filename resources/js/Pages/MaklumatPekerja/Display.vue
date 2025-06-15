<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    maklumatPekerja: [Object, Array],
    applicant: Object,
});

const dropdownOpen = ref(false);

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const form = useForm({
    applicant_id: props.applicant?.id,
    nama_pekerja: props.maklumatPekerja?.nama_pekerja,
    kad_pengenalan_pekerja: props.maklumatPekerja?.kad_pengenalan_pekerja,
    umur_pekerja: props.maklumatPekerja?.umur_pekerja,
    jantina_pekerja: props.maklumatPekerja?.jantina_pekerja,
    kelayakan_pekerja: props.maklumatPekerja?.kelayakan_pekerja,
    jawatan_pekerja: props.maklumatPekerja?.jawatan_pekerja,
    tarikh_mula_pekerja: props.maklumatPekerja?.tarikh_mula_pekerja,
    
});
</script>

<template>
    <AppLayout title="Maklumat Pemohon">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Maklumat Pemohon
            </h2>
            <br>
            <!-- Breadcrumb -->
           <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a :href="route('dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z"/>
                            </svg>
                            <a :href="route('applicant.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">Senarai Pemohon</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Maklumat Pemohon</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
                    <!-- Section Header -->
                    <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b">
                        <h3 class="text-lg font-semibold text-gray-700">Maklumat Pekerja</h3>
                        <div class="relative">
                            <button
                                @click="toggleDropdown"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                            >
                                Pilih Halaman
                            </button>
                            <div
                                v-if="dropdownOpen"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10"
                            >
                                <a
                                    :href="route('applicant.show', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Butiran Pemohon
                                </a>
                                <a
                                    :href="route('pengusaha.show', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Butiran Pengusaha
                                </a>
                                <a
                                    :href="route('butirantaska.show', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Butiran Taman Asuhan Kanak-Kanak
                                </a>
                                <a
                                    href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Bilangan Kanak-Kanak
                                </a>
                                <a
                                    href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Butiran Bayaran Kanak
                                </a>
                                <a
                                    :href="route('butiranpengurus.show', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Butiran Pengurus
                                </a>
                                <a
                                    :href="route('butiranpenyelia.show', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Butiran Penyelia
                                </a>
                                <a
                                    :href="route('maklumatpekerja.show', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Maklumat Pekerja
                                </a>
                                <a
                                :href="route('dokumen.show', applicant.id)"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Document Sokongan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Applicant Details -->
                    <div class="p-6">
                        <table class="w-full border-collapse border border-gray-200">
                            <tbody>
                                <tr class="bg-gray-50">
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Nama Pekerja
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ maklumatPekerja.nama_pekerja }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        No Kad Pengenalan Pekerja
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ maklumatPekerja.kad_pengenalan_pekerja }}
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                       Umur Pekerja
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ maklumatPekerja.umur_pekerja }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                       Jantina
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ maklumatPekerja.jantina_pekerja }}
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        kelayakan Pekerja
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ maklumatPekerja.kelayakan_pekerja }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Jawatan Pekerja
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ maklumatPekerja.jawatan_pekerja }}
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Tarikh Mula Berkerja
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ maklumatPekerja.tarikh_mula_pekerja }}
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
