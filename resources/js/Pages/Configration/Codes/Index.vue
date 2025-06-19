<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
// import { ref } from 'vue';
import { ref, watch, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import Swal from 'sweetalert2';

const openDropdownIndex = ref(null);
const search = ref('');


const toggleDropdown = (index) => {
  if (openDropdownIndex.value === index) {
    openDropdownIndex.value = null; // Close if the same dropdown is clicked again
  } else {
    openDropdownIndex.value = index; // Open the clicked dropdown
  }
};

const props = defineProps({
    codes:[Object,Array],
    kategori:String,
    search: String,
})

onMounted(() => {
    if (props.search !== undefined && props.search !== null) {
        search.value = props.search;
    }
});

const OnDelete = (row) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Check how you pass parameters, you may need to do this if ID is passed differently
            router.delete(route('setting.code.destroy', row))
                .then(() => {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        icon: "success",
                    });
                })
                .catch((error) => {
                    // Log and show error for easier debugging
                    console.error("Error deleting:", error);
                    Swal.fire({
                        title: "Error",
                        text: "There was a problem deleting the user.",
                        icon: "error",
                    });
                });
        } else {
            Swal.fire({
                title: "Cancelled",
                text: "Your action has been cancelled.",
                icon: "info",
            });
        }
    });
};

watch(search, (newSearch) => {
    // Only trigger if value actually changes
    if (newSearch !== props.search) {
        router.get(route('setting.code.index', { kategori: props.kategori }), {
            search: newSearch
        }, { preserveState: true, replace: true });
    }
});

const clearSearch = (e) => {
    if (e) e.preventDefault();
    search.value = '';
    router.get(route('setting.code.index', { kategori: props.kategori }), {}, { preserveState: true, replace: true });
};
</script>

<template>
    <AppLayout title="Kod">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Jenis Kod
            </h2>
            <!-- Breadcrumb Navigation -->
            <nav class="flex mt-4" aria-label="Breadcrumb">
              <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                  <a :href="route('dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                      <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Laman Utama
                  </a>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a :href="route('setting.category.index')" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
                      Kategori
                    </a>
                  </div>
                </li>
                <li aria-current="page">
                  <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">
                      Jenis Kod
                    </span>
                  </div>
                </li>
              </ol>
            </nav>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden sm:rounded-lg">
                    <!-- Start block -->
<section class="sm:p-5 antialiased">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <!-- Prevent default form submit -->
                    <form class="flex items-center" @submit.prevent>
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="simple-search" v-model="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Search">
                        </div>
                        <!-- Clear Button -->
                        <button
                            v-if="search"
                            @click="clearSearch"
                            type="button"
                            class="flex items-center justify-center text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 ml-2 dark:bg-red-500 dark:hover:bg-red-600 focus:outline-none dark:focus:ring-red-700"
                        >
                            Clear
                        </button>
                    </form>
                </div>
                <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    <a :href="route('setting.code.create', {category:kategori})"><button type="button" id="createProductModalButton" data-modal-target="createProductModal" data-modal-toggle="createProductModal" class="flex items-center justify-center text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-purple-600 dark:hover:bg-purple-700 focus:outline-none dark:focus:ring-purple-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah
                    </button>
                    </a>
                    
                    
                </div>
            </div>
            <div class="overflow-x-auto">
            
                <!-- {{ Users }} -->

                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-4">#</th>
                            <th scope="col" class="px-4 py-4">Kod</th>
                            <th scope="col" class="px-4 py-4">Keterangan</th>
                            <th scope="col" class="px-4 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(code,index) in props.codes.data" :key="code.id" class="border-b dark:border-gray-700">
                            <td class="px-4 py-3">
                                {{ (props.codes.current_page - 1) * props.codes.per_page + index + 1 }}
                            </td>
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ code.kod }}</th>
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ code.keterangan }}</th>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <!-- <button id="apple-imac-27-dropdown-button" @click="toggleDropdown(index)" class="inline-flex items-center text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5 dark:hover-bg-gray-800 text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button> -->
                                <!-- <div v-if="openDropdownIndex==index" id="apple-imac-27-dropdown" class="absoluted z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600"> -->
                                    <!-- <ul class="py-1 text-sm" aria-labelledby="apple-imac-27-dropdown-button"> -->
                                        <!-- <li> -->
                                            <a :href="route('setting.code.edit', code.id)">
                                                <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">                                                   
                                                Kemaskini
                                            </button> </a>
                                        <!-- </li> -->
                                        <!-- <li>
                                            <a :href="route('setting.code.index', category.kategori)">
                                            <button type="button" data-modal-target="readProductModal" data-modal-toggle="readProductModal" class="flex w-full items-center py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-gray-700 dark:text-gray-200">
                                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                                Preview
                                            </button>
                                            </a>
                                        </li> -->
                                        <!-- <li> -->
                                            <button @click="OnDelete(code.id)" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-red-700 focus:text-red-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-red-500 dark:focus:text-white">

                                                Padam
                                            </button>
                                        <!-- </li> -->
                                    <!-- </ul> -->
                                <!-- </div> -->
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                
                 

            </div>
            
        </div>
        <div class="float-end">
             <Pagination class="mt-4" :links="codes.links"/>
        </div>
       
    </div>
</section>
<!-- End block -->

                </div>
            </div>
        </div>
    </AppLayout>
</template>

