<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
// import { ref } from 'vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import Swal from 'sweetalert2';

const search = ref('');
// const openDropdownIndex = ref(null);

// // const Swal = require('sweetalert2');

// const toggleDropdown = (index) => {
//   if (openDropdownIndex.value === index) {
//     openDropdownIndex.value = null; // Close if the same dropdown is clicked again
//   } else {
//     openDropdownIndex.value = index; // Open the clicked dropdown
//   }
// };

const props = defineProps({
    pengalamanPengurus:[Object,Array],
})
const OnDelete = (row) => {
    Swal.fire({
        title: "Adakah anda pasti?",
        text: "Anda tidak akan dapat mengembalikan ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, padamkan!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            // Hanya lakukan penghapusan jika disahkan
            router.delete(route('pengalamanPengurus.destroy', { id: row })).then(() => {
                Swal.fire({
                    title: "Dipadamkan!",
                    text: "Fail anda telah dipadamkan.",
                    icon: "success",
                });
            }).catch((error) => {
                Swal.fire({
                    title: "Ralat",
                    text: "Terdapat masalah semasa memadamkan pengguna.",
                    icon: "error",
                });
            });
        } else {
            // Tangani tindakan batal (jika perlu)
            Swal.fire({
                title: "Dibatalkan",
                text: "Tindakan anda telah dibatalkan.",
                icon: "info",
            });
        }
    });
};


watch(search, (newSearch) => {
    router.get(route('pengalamanPengurus.index'), { search: newSearch }, { preserveState: true });
});
const clearSearch = () => {
    search.value = ''; // Reset the search term
};


</script>

<template>
    <AppLayout title="Jenis Pengalaman (Pengurus)">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Jenis Pengalaman (Pengurus)
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden sm:rounded-lg">
                    <!-- Start block -->
<section class="sm:p-5 antialiased">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form class="flex items-center">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="simple-search" v-model="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Search" required="">
                        </div>
                        <!-- Clear Button -->
                        <button v-if="search" @click="clearSearch" class="flex items-center justify-center text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 ml-2 dark:bg-red-500 dark:hover:bg-red-600 focus:outline-none dark:focus:ring-red-700">
                        Clear
                        </button>
                    </form>
                </div>
                <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    <!-- <a :href="route('applicant.create')"><button type="button" id="createProductModalButton" data-modal-target="createProductModal" data-modal-toggle="createProductModal" class="flex items-center justify-center text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-purple-600 dark:hover:bg-purple-700 focus:outline-none dark:focus:ring-purple-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Pengurus Baru
                    </button>
                    </a> -->
                    
                    
                </div>
            </div>
            <div class="overflow-x-auto">
                <!-- {{ Users }} -->

                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-4">Nama Pemohon</th>
                            <th scope="col" class="px-4 py-4">Nama</th>
                            <th scope="col" class="px-4 py-3">No Kad Pengenalan</th> 
                            <!-- <th scope="col" class="px-4 py-3">Umur</th>
                            <th scope="col" class="px-4 py-3">Kelulusan Akedemik Pegurus</th>
                            <th scope="col" class="px-4 py-3">Jawatan Hakiki Pengurus</th> -->
                            <!-- <th scope="col" class="px-4 py-3">No Telefon(P)</th>
                            <th scope="col" class="px-4 py-3">No Faks</th> -->
                                <span class="sr-only">Actions</span>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(butiranPengurus,index) in props.butiranPengurus.data" class="border-b dark:border-gray-700">
                            <th v-if="butiranPengurus.applicant" scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ butiranPengurus.applicant.nama }}</th>
                            
                            <td class="px-4 py-3">{{ butiranPengurus.nama_pengurus }}</td>
                            <td class="px-4 py-3">{{ butiranPengurus.kad_pengenalan_pengurus }}</td>
                            <!-- <td class="px-4 py-3">{{ butiranPengurus.email }}</td> -->
                            <!-- <td class="px-4 py-3">{{ applicant.telefon_r }}</td>
                            <td class="px-4 py-3">{{ applicant.telefon_r }}</td>
                            <td class="px-4 py-3">{{ applicant.telefon_r }}</td> -->

                            <td class="px-4 py-3 flex items-center justify-end">
                                
                                <td class="px-4 py-3 flex items-center justify-end">
                                <div class="inline-flex rounded-md shadow-sm" role="group">
                                    <a href="#">
                                    <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                    <svg class="w-3 h-3 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
                                    </svg>
                                    Profil
                                    </button>
                                    </a>
                                    <a :href="route('butiranpengurus.edit', butiranPengurus.id)">
                                    <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                    Edit
                                    </button>
                                    </a>
                                    <button @click="OnDelete(butiranPengurus.id)" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-red-700 focus:text-red-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-red-500 dark:focus:text-white">
                                    Padam
                                    </button>
                                </div>
                                </td>

                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                
                 

            </div>
            
        </div>
        <div class="float-end">
             <Pagination class="mt-4" :links="butiranPengurus.links"/>
        </div>
       
    </div>
</section>
<!-- End block -->

                </div>
            </div>
        </div>
    </AppLayout>
</template>
