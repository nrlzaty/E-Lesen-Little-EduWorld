<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import Swal from 'sweetalert2';
import moment from 'moment';

const search = ref('');
const page = usePage();
// Set statusFilter from route query if present, else empty string
const statusFilter = ref(page.url.includes('status=') ? decodeURIComponent((page.url.split('status=')[1] || '').split('&')[0]) : '');
const allApplicants = ref(null); // Will hold all applicants when filtering/searching

// Status options (adjust as needed)
const statusOptions = [
    { label: 'Semua Status', value: '' },
    { label: 'Pemohonan Baru', value: 'Pemohonan Baru' },
    { label: 'Diluluskan', value: 'Diluluskan' },
    { label: 'Tidak Diluluskan', value: 'Tidak Diluluskan' },
    { label: 'Dalam Semakan', value: 'Dalam Semakan' },
    { label: 'Telah Disemak', value: 'Telah Disemak' },
    { label: 'Borang Tidak Lengkap', value: 'Borang Tidak Lengkap' },
    { label: 'Hampir Tamat Tempoh Lesen', value: 'Hampir Tamat Tempoh Lesen' },
    { label: 'Tamat Tempoh Lesen', value: 'Tamat Tempoh Lesen' },
    { label: 'Perbaharui Lesen Tidak Diluluskan', value: 'Perbaharui Lesen Tidak Diluluskan' },
    { label: 'Perbaharui Lesen Borang Tidak Lengkap', value: 'Perbaharui Lesen Borang Tidak Lengkap' },
    { label: 'Perbaharui Lesen Dalam Semakan', value: 'Perbaharui Lesen Dalam Semakan' },
    { label: 'Perbaharui Lesen Telah Disemak', value: 'Perbaharui Lesen Telah Disemak' },
    { label: 'Perbaharui Lesen Diluluskan', value: 'Perbaharui Lesen Diluluskan' },
];

const props = defineProps({
    Applicants:[Object,Array],
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
            router.delete(route('applicant.destroy', { id: row })).then(() => {
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
    router.get(route('applicant.index'), { search: newSearch }, { preserveState: true });
});
const clearSearch = () => {
    search.value = ''; // Reset the search term
};

const formatDate = (date, time) => {
  const dateTime = `${date} ${time}`; // Combine date and time
  return moment(dateTime, 'YYYY-MM-DD HH:mm:ss').format('DD MMMM YYYY, h:mm:ss a');
};
const changeStatus = (expiredDate, expiredTime, status) => {
  // Do not override status for renewal-in-process statuses
  const renewalStatuses = [
    'Perbaharui Lesen',
    'Perbaharui Lesen Dalam Semakan',
    'Perbaharui Lesen Telah Disemak',
    'Perbaharui Lesen Borang Tidak Lengkap',
    'Perbaharui Lesen Tidak Diluluskan'
    // Do NOT include 'Perbaharui Lesen Diluluskan' here!
  ];
  if (renewalStatuses.includes(status)) {
    return status;
  }

  const now = moment();
  const expiryDateTime = moment(`${expiredDate} ${expiredTime}`, 'YYYY-MM-DD HH:mm:ss');

  let statusText = status;

  // Treat Perbaharui Lesen Diluluskan like Diluluskan for expiry logic
  if (['Diluluskan', 'Perbaharui Lesen Diluluskan'].includes(status)) {
    if (expiryDateTime.isValid()) {
      if (expiryDateTime.isBefore(now)) {
        statusText = 'Tamat Tempoh Lesen';
      } else if (expiryDateTime.diff(now, 'minutes') <= 30 && expiryDateTime.diff(now, 'minutes') >= 0) {
        statusText = 'Hampir Tamat Tempoh Lesen';
      }
    }
  }

  return statusText;
};

// Add this computed property for filtering
const filteredApplicants = computed(() => {
    // If filtering/searching, use allApplicants (unpaginated), else use paginated data
    let data = allApplicants.value ?? props.Applicants.data;
    if (search.value) {
        const s = search.value.toLowerCase();
        data = data.filter(a =>
            (a.nama && a.nama.toLowerCase().includes(s)) ||
            (a.kad_pengenalan && a.kad_pengenalan.toLowerCase().includes(s)) ||
            (a.email && a.email.toLowerCase().includes(s))
        );
    }
    if (statusFilter.value) {
        data = data.filter(a => (a.status || '').toLowerCase() === statusFilter.value.toLowerCase());
    }
    // Hide old application if renewal in progress (for Kerani)
    const userRole = page.props.auth.user.role;
    if (userRole && userRole.toLowerCase() === 'kerani') {
        // Find all previous_applicant_id that have a renewal in progress
        const renewalStatuses = [
            'Perbaharui Lesen',
            'Perbaharui Lesen Dalam Semakan',
            'Perbaharui Lesen Telah Disemak',
            'Perbaharui Lesen Diluluskan',
            'Perbaharui Lesen Tidak Diluluskan',
            'Perbaharui Lesen Borang Tidak Lengkap'
        ];
        // This requires backend to not send the old application, but for safety:
        const renewedIds = new Set(
            (props.Applicants.data || []).filter(a =>
                renewalStatuses.includes(a.status)
            ).map(a => a.previous_applicant_id).filter(Boolean)
        );
        data = data.filter(a => !renewedIds.has(a.id));
    }
    return data;
});

const fetchAllApplicants = async () => {
    // Fetch all applicants for the user (no pagination)
    const params = {};
    if (search.value) params.search = search.value;
    if (statusFilter.value) params.status = statusFilter.value;
    params.all = 1; // Custom param to indicate "fetch all"
    const response = await axios.get(route('applicant.index'), { params });
    allApplicants.value = response.data.Applicants.data;
};

watch([search, statusFilter], async ([newSearch, newStatus]) => {
    if (newSearch || newStatus) {
        await fetchAllApplicants();
    } else {
        allApplicants.value = null;
    }
});

</script>

<template>
    <AppLayout title="Senarai Pemohonan ">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Senarai Pemohonan 
            </h2>
        </template>

        <!-- Borang Tidak Lengkap Notification Block (Kerani only, always show at every page, list all Borang Tidak Lengkap for kerani user) -->
        <div
            v-if="$page.props.auth.user.role === 'kerani'"
            class="mb-1 mt-2"
        >
            <div class="bg-yellow-50 border-l-4 border-yellow-600 text-yellow-900 p-4 rounded-lg shadow flex items-start relative">
                <svg class="w-6 h-6 text-yellow-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <div class="flex-1">
                    <div class="font-semibold mb-1">
                        Sila kemaskini borang permohonan yang bertanda <b>Borang Tidak Lengkap</b>.
                    </div>
                    <div class="text-sm mb-2">Klik nama permohonan untuk kemaskini:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <li
                            v-for="a in $page.props.all_borang_tidak_lengkap ?? []"
                            :key="a.id"
                        >
                            <a
                                :href="route('applicant.edit', a.id)"
                                class="font-semibold text-yellow-900 hover:underline hover:text-yellow-700 transition"
                            >
                                {{ a.nama }}
                            </a>
                        </li>
                        <li v-if="!$page.props.all_borang_tidak_lengkap || $page.props.all_borang_tidak_lengkap.length === 0" class="italic text-yellow-700">
                            Tiada permohonan Borang Tidak Lengkap untuk dikemaskini.
                        </li>
                    </ul>
                </div>
                <!-- No close button -->
            </div>
        </div>



        <div class="py-12">
            <div class="w-full mx-auto px-0">
                <div class="bg-white overflow-hidden sm:rounded-lg">
                    <!-- Start block -->
<section class="sm:p-5 antialiased">
    <div class="w-full px-0">
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
                    <!-- Filter Icon and Status Dropdown -->
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                        <div>
                            <label for="status-filter" class="sr-only">Status</label>
                            <select
                                id="status-filter"
                                v-model="statusFilter"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            >
                                <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <a :href="route('applicant.create')"><button type="button" id="createProductModalButton" data-modal-target="createProductModal" data-modal-toggle="createProductModal" class="flex items-center justify-center text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-purple-600 dark:hover:bg-purple-700 focus:outline-none dark:focus:ring-purple-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Pemohonan Baru
                    </button>
                    </a>
                    
                    
                </div>
            </div>
            <div class="overflow-x-auto w-full">
                <!-- {{ Users }} -->

                <table class="w-full lg:min-w-[1200px] text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-4">#</th>
                            <th scope="col" class="px-4 py-4">Nama Pemohon</th>
                            <th scope="col" class="px-4 py-3">No Kad Pengenalan</th> 
                            <th scope="col" class="px-4 py-3">Email</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Tamat Tempoh</th> <!-- Add this column -->
                            <th scope="col" class="px-4 py-3">Komen</th>
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
                            <td class="px-4 py-3">{{ (props.Applicants.current_page - 1) * props.Applicants.per_page + index + 1 }}</td>
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ applicant.nama }}</th>
                            <td class="px-4 py-3">{{ applicant.kad_pengenalan }}</td>
                            <td class="px-4 py-3">{{ applicant.email }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <span>{{ changeStatus(applicant.expired_date, applicant.expired_time, applicant.status) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span v-if="applicant.expired_date && applicant.expired_date !== '0000-00-00' && applicant.expired_date !== '' && applicant.expired_date !== null">
                                    {{ formatDate(applicant.expired_date, applicant.expired_time) }}
                                </span>
                                <span v-else class="italic text-gray-400">-</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        v-if="applicant.komen && applicant.komen.trim() !== ''"
                                        @click="Swal.fire({ title: 'Komen', text: applicant.komen, icon: 'info' })"
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
                                    <a :href="route('applicant.show', applicant.id)">
                                        <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                            <svg class="w-3 h-3 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
                                            </svg>
                                            Profil
                                        </button>
                                    </a>
                                    <!-- Kemaskini & Padam: Only available if status is Borang Tidak Lengkap or Perbaharui Lesen -->
                                    <template v-if="(applicant.status === 'Borang Tidak Lengkap' && applicant.notification_status === 'alert_kerani') || applicant.status === 'Perbaharui Lesen'">
                                        <a :href="route('applicant.edit', applicant.id)">
                                            <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                                Kemaskini
                                            </button>
                                        </a>
                                        <button
                                            v-if="applicant.status === 'Borang Tidak Lengkap'"
                                            @click="OnDelete(applicant.id)"
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-red-700 focus:text-red-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-red-500 dark:focus:text-white"
                                        >
                                            Padam
                                        </button>
                                        <button
                                            v-else
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-e-lg cursor-not-allowed"
                                            @click="() => Swal.fire('Tidak dibenarkan', 'Permohonan ini sedang diproses atau telah selesai. Anda hanya boleh kemaskini atau padam jika status adalah Borang Tidak Lengkap.', 'info')"
                                            disabled
                                        >
                                            Padam
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border-t border-b border-gray-200 cursor-not-allowed"
                                            @click="() => Swal.fire('Tidak dibenarkan', 'Permohonan ini sedang diproses atau telah selesai. Anda hanya boleh kemaskini atau padam jika status adalah Borang Tidak Lengkap atau Perbaharui Lesen.', 'info')"
                                            disabled
                                        >
                                            Kemaskini
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-e-lg cursor-not-allowed"
                                            @click="() => Swal.fire('Tidak dibenarkan', 'Permohonan ini sedang diproses atau telah selesai. Anda hanya boleh kemaskini atau padam jika status adalah Borang Tidak Lengkap.', 'info')"
                                            disabled
                                        >
                                            Padam
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                
                 

            </div>
            
        </div>
        <!-- Only show pagination if not filtering/searching -->
        <div class="float-end" v-if="!search && !statusFilter">
             <Pagination class="mt-4" :links="Applicants.links"/>
        </div>
       
    </div>
</section>
<!-- End block -->

                </div>
            </div>
        </div>
    </AppLayout>
</template>

