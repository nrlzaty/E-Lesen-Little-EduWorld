<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import moment from 'moment';

const props = defineProps({
    applicants: Object,
});

const search = ref('');
const renewing = ref(null);

const renewLicense = (id) => {
    renewing.value = id;
    router.post(route('renew-license.renew', id), {}, {
        onFinish: () => { renewing.value = null; }
    });
};

// Remove onSearch and clearSearch router logic, use local filtering
const clearSearch = () => {
    search.value = '';
};

// Filter applicants locally, only status 'Hampir Tamat Tempoh Lesen'
const filteredApplicants = computed(() => {
    let data = props.applicants.data ?? [];
    // Defensive: trim and lowercase both sides
    data = data.filter(a => ((a.status || '').trim().toLowerCase() === 'hampir tamat tempoh lesen'.toLowerCase()));
    if (search.value) {
        const s = search.value.toLowerCase();
        data = data.filter(a =>
            (a.nama && a.nama.toLowerCase().includes(s)) ||
            (a.kad_pengenalan && a.kad_pengenalan.toLowerCase().includes(s)) ||
            (a.email && a.email.toLowerCase().includes(s))
        );
    }
    return data;
});

const changeStatus = (date) => {
    var d = date;
   const now = moment.utc();
    const expiryDate = moment.utc(d);
    if (expiryDate.isBefore(now)) {
        return 'Tamat Tempoh Lesen';
    } else if (expiryDate.diff(now, 'minutes') <= 1) {
        return 'Hampir Tamat Tempoh';
    } else {
        return 'Diluluskan';
    }

}
</script>

<template>
    <AppLayout title="Perbaharui Lesen">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Perbaharui Lesen
            </h2>
        </template>

        <!-- Info Block: What this page is about -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded-lg shadow flex items-start">
                <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <div>
                    <div class="font-semibold mb-1">
                        Halaman ini membolehkan anda memulakan atau meneruskan proses perbaharui lesen bagi permohonan yang telah diluluskan.
                    </div>
                    <div class="text-sm">
                        Sila cari dan pilih permohonan yang ingin diperbaharui lesen. Hanya permohonan dengan status <b>Diluluskan</b> akan dipaparkan di sini.
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-2 pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden sm:rounded-lg">
                    <!-- Start block -->
<section class="sm:p-5 antialiased">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form class="flex items-center" @submit.prevent>
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="simple-search" v-model="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Search" autocomplete="off">
                        </div>
                        <!-- Clear Button -->
                        <button v-if="search" @click="clearSearch" type="button" class="flex items-center justify-center text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 ml-2 dark:bg-red-500 dark:hover:bg-red-600 focus:outline-none dark:focus:ring-red-700">
                        Clear
                        </button>
                    </form>
                </div>
                <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    
                </div>
            </div>
            <div class="overflow-x-auto w-full">
                <!-- {{ Users }} -->

                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-4">#</th>
                            <th scope="col" class="px-4 py-4">Nama Pemohon</th>
                            <th scope="col" class="px-4 py-3">No Kad Pengenalan</th> 
                            <th scope="col" class="px-4 py-3">Email</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-2 py-3 w-20">Komen</th>
                            <span class="sr-only">Actions</span>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="filteredApplicants.length === 0">
                            <td colspan="7" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                Tiada data dijumpai
                            </td>
                        </tr>
                        <tr v-for="(applicant, index) in filteredApplicants" :key="index" class="border-b dark:border-gray-700">
                            <td class="px-4 py-3">{{ (props.applicants.current_page - 1) * props.applicants.per_page + index + 1 }}</td>
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ applicant.nama }}</th>
                            <td class="px-4 py-3">{{ applicant.kad_pengenalan }}</td>
                            <td class="px-4 py-3">{{ applicant.email }}</td>
                            <td class="px-4 py-3">{{ applicant.status }}</td>
                            <td class="px-2 py-3 w-20">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        v-if="applicant.komen && applicant.komen.trim() !== ''"
                                        @click="Swal.fire({ title: 'Komen', text: applicant.komen, icon: 'info', customClass: { popup: 'swal2-center-comment' }, })"
                                        type="button"
                                        class="p-1 rounded bg-emerald-200 hover:bg-emerald-300 text-gray-700"
                                        title="Lihat komen"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                                        </svg>
                                    </button>
                                    <button
                                        v-else
                                        type="button"
                                        class="p-1 rounded bg-emerald-50 text-gray-400 cursor-not-allowed"
                                        title="Tiada komen diberi"
                                        disabled
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <div class="inline-flex rounded-md shadow-sm" role="group">
                                    <template v-if="applicant.status === 'Dalam Semakan' || applicant.status === 'Dalam Proses Semakan'">
                                        <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-green-100 border border-green-200 rounded-lg">
                                            Permohonan telah dihantar untuk semakan
                                        </span>
                                    </template>
                                    <template v-else>
                                        <button
                                            v-if="!applicant.renew_in_progress"
                                            type="button"
                                            @click="renewLicense(applicant.id)"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white"
                                            :disabled="renewing === applicant.id || applicant.status === 'Tamat Tempoh Lesen'"
                                        >
                                            Mula Perbaharui Lesen
                                        </button>
                                        <a
                                            v-else
                                            :href="route('applicant.edit', applicant.renew_in_progress)"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 border border-blue-200 rounded-lg hover:bg-blue-200 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700"
                                        >
                                            Teruskan Perbaharui Lesen
                                        </a>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                 

            </div>
            
        </div>
        <div class="float-end">
            <Pagination
                v-if="applicants && applicants.links && applicants.links.length > 3"
                class="mt-4"
                :links="applicants.links"
            />
        </div>
       
    </div>
</section>
<!-- End block -->

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* Add this style to center SweetAlert2 popup text for comments */
.swal2-center-comment .swal2-html-container,
.swal2-center-comment .swal2-content {
    text-align: center !important;
}
</style>


