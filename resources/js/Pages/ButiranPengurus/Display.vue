<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    butiranPengurus: [Object, Array],
    applicant: Object,
    pengalaman_keterangan: Array, // <-- add this line
});

const dropdownOpen = ref(false);

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const form = useForm({
    applicant_id: props.applicant?.id,
    nama_pengurus: props.butiranPengurus?.nama_pengurus,
    kad_pengenalan_pengurus: props.butiranPengurus?.kad_pengenalan_pengurus,
    umur_pengurus: props.butiranPengurus?.umur_pengurus,
    kelulusan_akademik_pengurus: props.butiranPengurus?.kelulusan_akademik_pengurus,
    jawatan_hakiki_pengurus: props.butiranPengurus?.jawatan_hakiki_pengurus,
    jenis_pengalaman_pengurus: props.butiranPengurus?.jenis_pengalaman_pengurus,

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
                        <h3 class="text-lg font-semibold text-gray-700">Maklumat Pengurus</h3>
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
                                :href="route('dokumen.show', applicant.id)"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Document Sokongan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Applicant Details -->
                    <div class="p-12">
                        <table class="w-full border-collapse border border-gray-200">
                            <tbody>
                                <tr class="bg-gray-50">
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Nama Pengurus
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ butiranPengurus.nama_pengurus }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Kad Pengenalan Pengurus
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ butiranPengurus.kad_pengenalan_pengurus }}
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Umur Pengurus
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ butiranPengurus.umur_pengurus }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Kelulusan Akademik Pengurus
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ butiranPengurus.kelulusan_akademik_pengurus }}
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Jawatan Hakiki Pengurus
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ butiranPengurus.jawatan_hakiki_pengurus }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Jenis Pengalaman Pengurus
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        <template v-if="props.pengalaman_keterangan && props.pengalaman_keterangan.length">
                                            <ul class="list-disc pl-5">
                                                <li v-for="(item, idx) in props.pengalaman_keterangan" :key="idx">{{ item }}</li>
                                            </ul>
                                        </template>
                                        <template v-else>
                                            Tiada pengalaman
                                        </template>
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
