<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
import Swal from 'sweetalert2';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    Applicants: [Object, Array],
    Pengusaha: [Object, Array],
    butiranTaska: [Object, Array],
    butiranPengurus: [Object, Array],
    butiranPenyelia: [Object, Array],
    
  
    status: Object,
    userRole: String, // <-- Add this prop, or get from auth if available
    documents: {
        type: Array,
        default: () => []
    },
    pengurus_pengalaman_keterangan: Array,
    penyelia_pengalaman_keterangan: Array,
});

// const form = ref({
//    status: '', // Default selected value
//    komen: '', // Add a field for comments
//    expired_date: '',
//    expired_time: '', // <-- Add this for expiry date/time
//});

const form = useForm({
    status: null,
    komen: null,
    expired_date: null,
    expired_time: null,
})

const currentStatus = ref('');
const dropdownOpen = ref(false);

const sections = ref({
    pemohon: true,
    pengusaha: true,
    taska: true,
    pengurus: true,
    penyelia: true,
    pekerja: true,
    documents: true, // <-- Add this line
});

const toggleSection = (section) => {
    sections.value[section] = !sections.value[section];
};

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const documents = ref([]);

// Initialize the current status when the component is mounted
onMounted(() => {
    // Check localStorage for the saved status
    const savedStatus = localStorage.getItem(`currentStatus-${props.Applicants.id}`);
    if (savedStatus) {
        currentStatus.value = savedStatus;
    } else {
        currentStatus.value = props.Applicants.current_status || 'No status available';
    }

    // Add documents ref and initialize from props
    documents.value = props.documents || [];
});

// Method to update status in the database
const updateStatus = async () => {
    const role = (props.userRole || '').toLowerCase();
    const isAdmin = role === 'admin';
    const isPenyemak = role === 'pegawai penyemakan' || role === 'pegawai_penyemak';
    const isPerlulus = role === 'pegawai perlulusan' || role === 'pegawai_perlulus';

    // Komen required for Borang Tidak Lengkap (Penyemak/Admin) or Tidak Diluluskan (Perlulus/Admin)
    const requireKomen =
        (form.status === 'Borang Tidak Lengkap' && (isPenyemak || isAdmin)) ||
        (form.status === 'Tidak Diluluskan' && (isPerlulus || isAdmin)) ||
        (form.status === 'Perbaharui Lesen Tidak Diluluskan' && (isPerlulus || isAdmin));

    if (requireKomen && !form.komen.trim()) {
        Swal.fire({
            title: 'Komen Diperlukan!',
            text: 'Sila tulis komen terlebih dahulu sebelum klik Hantar.',
            icon: 'warning',
        });
        return;
    }
    // Require expiry_date if approving
    const requireExpiry = (
        (form.status === 'Diluluskan' || form.status === 'Perbaharui Lesen Diluluskan') &&
        (isPerlulus || isAdmin)
    );
    if (requireExpiry && !form.expired_date) {
        Swal.fire({
            title: 'Tarikh Tamat Diperlukan!',
            text: 'Sila pilih tarikh tamat tempoh lesen.',
            icon: 'warning',
        });
        return;
    }
    try {
        
        form.put(route('status.update',{id: props.Applicants.id}), {
            errorBag: 'updateStatus',
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Berjaya!',
                    text: 'Status berjaya dikemaskini.',
                    icon: 'success',
                });
            },
        });

    } 
    
    catch (error) {
        Swal.fire({
            title: 'Ralat!',
            text: 'Gagal mengemaskini status.',
            icon: 'error',
        });

        console.log(error);
    }
};

// Watch for changes to the dropdown and update status
watch(
    () => form.status,
    (newStatus) => {
        if (newStatus) {
            updateStatus();
        }
    }
);

const showScrollButton = ref(false);

const handleScroll = () => {
    showScrollButton.value = window.scrollY > 200;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

// Filter status options based on userRole
const filteredStatus = computed(() => {
    // Normalize role string for comparison
    const role = (props.userRole || '').toLowerCase();
    if (role === 'pegawai perlulusan' || role === 'pegawai_perlulus') {
        // For renew, handled in template
        return props.status.filter(s =>
            s.keterangan === 'Diluluskan' ||
            s.keterangan === 'Tidak Diluluskan'
        );
    }
    if (role === 'pegawai penyemakan' || role === 'pegawai_penyemak') {
        return props.status.filter(
            s =>
                s.keterangan === 'Dalam Semakan' ||
                s.keterangan === 'Telah Disemak' ||
                s.keterangan === 'Borang Tidak Lengkap' // <-- Add this line
        );
    }
    // Admin or others see all
    return props.status;
});

// Tab logic
const activeTab = ref('borang'); // 'borang' or 'status'
</script>

<template>
    <AppLayout title="Status Pemohonan">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Status Pemohonan
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
                            <a :href="route('status.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">Senarai Tindakan</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Status Pemohonan</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </template>

        <!-- Tab Navigation -->
        <div class="flex border-b mb-4 bg-white rounded-t-lg">
            <button
                :class="['px-4 py-2 focus:outline-none', activeTab === 'borang' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600']"
                @click="activeTab = 'borang'"
            >
                Borang Pemohonan
            </button>
            <button
                :class="['px-4 py-2 focus:outline-none', activeTab === 'status' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600']"
                @click="activeTab = 'status'"
            >
                Kemaskini Status
            </button>
        </div>

        <!-- Borang Pemohonan Tab -->
        <div v-if="activeTab === 'borang'">
            <!-- Applicant Details -->
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
                        <!-- Section Header -->
                        <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b cursor-pointer" @click="toggleSection('pemohon')">
                            <h3 class="text-lg font-semibold text-gray-700">Maklumat Pemohon</h3>
                            <svg :class="{'rotate-180': sections.pemohon}" class="w-6 h-6 text-gray-500 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div v-show="sections.pemohon" class="p-6">
                            <table class="w-full border-collapse border border-gray-200">
                                <tbody>
                                    <tr class="bg-gray-50" v-if="Applicants.nama">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Nama Pemohon
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Applicants.nama }}
                                        </td>
                                    </tr>
                                    <tr v-if="Applicants.kad_pengenalan">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Kad Pengenalan
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Applicants.kad_pengenalan }}
                                        </td>
                                    </tr>
                                    <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Warganegara
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ Applicants.warganegara }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Alamat Rumah
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ Applicants.alamat_rumah }}
                                    </td>
                                </tr>
                                 <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">
                                        Alamat pos jika berlainan daripada alamat rumah
                                    </th>
                                    <td class="border px-4 py-2 text-gray-700">
                                        {{ Applicants.alamat_berlainan }}
                                    </td>
                                </tr>
                                    <tr class="bg-gray-50" v-if="Applicants.email">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Email
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Applicants.email }}
                                        </td>
                                    </tr>
                                    <tr v-if="Applicants.telefon_r">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Telefon (R)
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Applicants.telefon_r }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="Applicants.telefon_b">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Telefon (B)
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Applicants.telefon_b }}
                                        </td>
                                    </tr>
                                    <tr v-if="Applicants.telefon_p">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Telefon (P)
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Applicants.telefon_p }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="Applicants.faks">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Faks
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Applicants.faks }}
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="2" class="border px-4 py-2 text-gray-700 text-center">
                                            No data available
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengusaha Details -->
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
                        <!-- Section Header -->
                        <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b cursor-pointer" @click="toggleSection('pengusaha')">
                            <h3 class="text-lg font-semibold text-gray-700">Maklumat Pengusaha</h3>
                            <svg :class="{'rotate-180': sections.pengusaha}" class="w-6 h-6 text-gray-500 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div v-show="sections.pengusaha" class="p-6">
                            <table class="w-full border-collapse border border-gray-200">
                                <tbody>
                                    <tr class="bg-gray-50" v-if="Pengusaha && Pengusaha.nama_pengusaha">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Nama Pengusaha
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Pengusaha.nama_pengusaha }}
                                        </td>
                                    </tr>
                                    <tr v-if="Pengusaha && Pengusaha.kad_pengenalan_pengusaha">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Kad Pengenalan
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Pengusaha.kad_pengenalan_pengusaha }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="Pengusaha && Pengusaha.warganegara_pengusaha">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Warganegara
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Pengusaha.warganegara_pengusaha }}
                                        </td>
                                    </tr>
                                    <tr v-if="Pengusaha && Pengusaha.alamat_rumah_pengusaha">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Alamat Rumah
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Pengusaha.alamat_rumah_pengusaha }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="Pengusaha && Pengusaha.alamat_berlainan_pengusaha">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Alamat pos jika berlainan daripada
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Pengusaha.alamat_berlainan_pengusaha }}
                                        </td>
                                    </tr>
                                    <tr v-if="Pengusaha && Pengusaha.email_pengusaha">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Email
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Pengusaha.email_pengusaha }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="Pengusaha && Pengusaha.telefon_pengusaha">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Telefon
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Pengusaha.telefon_pengusaha }}
                                        </td>
                                    </tr>
                                    <tr v-if="Pengusaha && Pengusaha.faks_pengusaha">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Faks
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Pengusaha.faks_pengusaha }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="Pengusaha && Pengusaha.jenis_pengusaha">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Jenis Pengusaha
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ Pengusaha.jenis_pengusaha }}
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="2" class="border px-4 py-2 text-gray-700 text-center">
                                            No data available
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Butiran Taska -->
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
                        <!-- Section Header -->
                        <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b cursor-pointer" @click="toggleSection('taska')">
                            <h3 class="text-lg font-semibold text-gray-700">Butiran Taska</h3>
                            <svg :class="{'rotate-180': sections.taska}" class="w-6 h-6 text-gray-500 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <!-- Butiran Taska Details -->
                        <div v-show="sections.taska" class="p-6">
                            <table class="w-full border-collapse border border-gray-200">
                                <tbody>
                                    <tr class="bg-gray-50" v-if="butiranTaska && butiranTaska.nama_taska">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Nama TASKA
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranTaska.nama_taska }}
                                        </td>
                                    </tr>
                                    <tr v-if="butiranTaska && butiranTaska.alamat_taska">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Alamat
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranTaska.alamat_taska }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="butiranTaska && butiranTaska.telefon_taska_r">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Telefon (R)
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranTaska.telefon_taska_r }}
                                        </td>
                                    </tr>
                                    <tr v-if="butiranTaska && butiranTaska.telefon_taska_b">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Telefon (B)
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranTaska.telefon_taska_b }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="butiranTaska && butiranTaska.telefon_taska_p">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No Telefon (P)
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranTaska.telefon_taska_p }}
                                        </td>
                                    </tr>
                                    <tr v-if="butiranTaska && butiranTaska.faks_taska">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            No. Faks
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranTaska.faks_taska }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="butiranTaska && butiranTaska.email_taska">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Emel
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranTaska.email_taska }}
                                        </td>
                                    </tr>
                                    <tr v-if="butiranTaska && butiranTaska.laman_web_taska">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Laman Web
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranTaska.laman_web_taska }}
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="2" class="border px-4 py-2 text-gray-700 text-center">
                                            No data available
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengurus Details -->
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
                        <!-- Section Header -->
                        <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b cursor-pointer" @click="toggleSection('pengurus')">
                            <h3 class="text-lg font-semibold text-gray-700">Maklumat Pengurus</h3>
                            <svg :class="{'rotate-180': sections.pengurus}" class="w-6 h-6 text-gray-500 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <!-- Pengurus Details -->
                        <div v-show="sections.pengurus" class="p-12">
                            <table class="w-full border-collapse border border-gray-200">
                                <tbody>
                                    <tr class="bg-gray-50" v-if="butiranPengurus && butiranPengurus.nama_pengurus">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Nama Pengurus
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranPengurus.nama_pengurus }}
                                        </td>
                                    </tr>
                                    <tr v-if="butiranPengurus && butiranPengurus.kad_pengenalan_pengurus">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Kad Pengenalan Pengurus
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranPengurus.kad_pengenalan_pengurus }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="butiranPengurus && butiranPengurus.umur_pengurus">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Umur Pengurus
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranPengurus.umur_pengurus }}
                                        </td>
                                    </tr>
                                    <tr v-if="butiranPengurus && butiranPengurus.kelulusan_akademik_pengurus">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Kelulusan Akademik Pengurus
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranPengurus.kelulusan_akademik_pengurus }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="butiranPengurus && butiranPengurus.jawatan_hakiki_pengurus">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Jawatan Hakiki Pengurus
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranPengurus.jawatan_hakiki_pengurus }}
                                        </td>
                                    </tr>
                                    <tr v-if="butiranPengurus && butiranPengurus.jenis_pengalaman_pengurus">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Jenis Pengalaman Pengurus
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            <template v-if="props.pengurus_pengalaman_keterangan && props.pengurus_pengalaman_keterangan.length">
                                                <ul class="list-disc pl-5">
                                                    <li v-for="(item, idx) in props.pengurus_pengalaman_keterangan" :key="idx">{{ item }}</li>
                                                </ul>
                                            </template>
                                            <template v-else>
                                                Tiada pengalaman
                                            </template>
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="2" class="border px-4 py-2 text-gray-700 text-center">
                                            No data available
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penyelia Details -->
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
                        <!-- Section Header -->
                        <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b cursor-pointer" @click="toggleSection('penyelia')">
                            <h3 class="text-lg font-semibold text-gray-700">Butiran Penyelia</h3>
                            <svg :class="{'rotate-180': sections.penyelia}" class="w-6 h-6 text-gray-500 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <!-- Penyelia Details -->
                        <div v-show="sections.penyelia" class="p-12">
                            <table class="w-full border-collapse border border-gray-200">
                                <tbody>
                                    <tr class="bg-gray-50" v-if="butiranPenyelia && butiranPenyelia.nama_penyelia">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Nama Penyelia
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranPenyelia.nama_penyelia }}
                                        </td>
                                    </tr>
                                    <tr v-if="butiranPenyelia && butiranPenyelia.kad_pengenalan_penyelia">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Kad Pengenalan Penyelia
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranPenyelia.kad_pengenalan_penyelia }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="butiranPenyelia && butiranPenyelia.umur_penyelia">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Umur Penyelia
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranPenyelia.umur_penyelia }}
                                        </td>
                                    </tr>
                                    <tr v-if="butiranPenyelia && butiranPenyelia.kelulusan_akademik_penyelia">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Kelulusan Akademik Penyelia
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                            {{ butiranPenyelia.kelulusan_akademik_penyelia }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50" v-if="butiranPenyelia && butiranPenyelia.jenis_pengalaman_penyelia">
                                        <th class="border px-4 py-2 text-left text-gray-600">
                                            Jenis Pengalaman Penyelia
                                        </th>
                                        <td class="border px-4 py-2 text-gray-700">
                                        <template v-if="props.penyelia_pengalaman_keterangan && props.penyelia_pengalaman_keterangan.length">
                                            <ul class="list-disc pl-5">
                                                <li v-for="(item, idx) in props.penyelia_pengalaman_keterangan" :key="idx">{{ item }}</li>
                                            </ul>
                                        </template>
                                        <template v-else>
                                            Tiada pengalaman
                                        </template>
                                    </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="2" class="border px-4 py-2 text-gray-700 text-center">
                                            No data available
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- dokument -->
            <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
          <!-- Collapsible Section Header for Documents -->
          <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b cursor-pointer" @click="toggleSection('documents')">
            <h3 class="text-lg font-semibold text-gray-700">Dokumen yang dimuat naik</h3>
            <svg :class="{'rotate-180': sections.documents}" class="w-6 h-6 text-gray-500 transition-transform" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="p-8" v-show="sections.documents">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg shadow">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                  <tr v-for="(document, idx) in documents" :key="document.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ idx + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap flex items-center">
                      <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7V3a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v4m-10 0h10M5 7v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7M5 7h14"></path>
                      </svg>
                      <span class="font-medium text-gray-800">{{ document.nama_dokumen }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <a
                        :href="`/storage/${document.file_dokument}`"
                        target="_blank"
                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700 transition"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Lihat / Muat Turun
                      </a>
                    </td>
                  </tr>
                  <tr v-if="documents.length === 0">
                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">Tiada dokumen dimuat naik.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
            
        </div>

        <!-- Kemaskini Status Tab -->
        <div v-else class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
            <!-- Current Status Display -->
            <div class="mb-6">
                <label class="block font-semibold mb-2">Status Terkini:</label>
                <div class="px-4 py-2 bg-gray-100 rounded border border-gray-200 text-gray-700">
                    {{ Applicants.status || 'Tiada status' }}
                </div>
            </div>
            <!-- Show komen if status is Borang Tidak Lengkap and komen exists -->
            <div v-if="Applicants.status === 'Borang Tidak Lengkap' && Applicants.komen" class="mb-6">
                <label class="block font-semibold mb-2 text-red-600">Komen Pegawai Penyemakan:</label>
                <div class="px-4 py-2 bg-yellow-100 rounded border border-yellow-300 text-gray-800">
                    {{ Applicants.komen }}
                </div>
            </div>
            <form @submit.prevent="updateStatus">
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Tindakan:</label>
                    <div class="flex flex-col space-y-2">
                        <!-- For Pegawai Perlulusan, show renew approve/reject options if status is Perbaharui Lesen Telah Disemak -->
                        <template v-if="Applicants.status === 'Perbaharui Lesen Telah Disemak' && (userRole?.toLowerCase() === 'pegawai perlulusan' || userRole?.toLowerCase() === 'pegawai_perlulus')">
                            <label class="inline-flex items-start break-words max-w-full">
                                <input
                                    type="radio"
                                    class="form-radio mt-1"
                                    v-model="form.status"
                                    value="Perbaharui Lesen Diluluskan"
                                />
                                <span class="ml-2 break-words max-w-xs">Perbaharui Lesen Diluluskan</span>
                            </label>
                            <label class="inline-flex items-start break-words max-w-full">
                                <input
                                    type="radio"
                                    class="form-radio mt-1"
                                    v-model="form.status"
                                    value="Perbaharui Lesen Tidak Diluluskan"
                                />
                                <span class="ml-2 break-words max-w-xs">Perbaharui Lesen Tidak Diluluskan</span>
                            </label>
                        </template>
                        <template v-else-if="Applicants.status === 'Perbaharui Lesen Dalam Semakan'">
                            <label class="inline-flex items-start break-words max-w-full">
                                <input
                                    type="radio"
                                    class="form-radio mt-1"
                                    v-model="form.status"
                                    value="Perbaharui Lesen Telah Disemak"
                                />
                                <span class="ml-2 break-words max-w-xs">Perbaharui Lesen Telah Disemak</span>
                            </label>
                            <label class="inline-flex items-start break-words max-w-full">
                                <input
                                    type="radio"
                                    class="form-radio mt-1"
                                    v-model="form.status"
                                    value="Perbaharui Lesen Borang Tidak Lengkap"
                                />
                                <span class="ml-2 break-words max-w-xs">Perbaharui Lesen Borang Tidak Lengkap</span>
                            </label>
                        </template>
                        <template v-else>
                            <!-- Default: show filteredStatus as before -->
                            <template v-for="Status in filteredStatus" :key="Status.id">
                                <label class="inline-flex items-start break-words max-w-full">
                                    <input
                                        type="radio"
                                        class="form-radio mt-1"
                                        v-model="form.status"
                                        :value="Status.keterangan"
                                    />
                                    <span class="ml-2 break-words max-w-xs">{{ Status.keterangan }}</span>
                                </label>
                            </template>
                        </template>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Pegawai Bertugas:</label>
                    <input
                        type="text"
                        class="form-input w-full"
                        :value="$page.props.auth.user.name"
                        readonly
                    />
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Tarikh:</label>
                    <input
                        type="text"
                        class="form-input w-full"
                        :value="new Date().toLocaleDateString('ms-MY')"
                        readonly
                    />
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">
                        Komen:
                        <span v-if="requireKomen" class="text-red-600">*</span>
                    </label>
                    <textarea
                        class="form-textarea w-full"
                        rows="4"
                        v-model="form.komen"
                        placeholder="Masukkan komen di sini..."
                    ></textarea>
                </div>
                <!-- Show expiry date/time picker if approving -->
                <div class="mb-4" v-if="(form.status === 'Diluluskan' || form.status === 'Perbaharui Lesen Diluluskan') && (userRole?.toLowerCase() === 'pegawai perlulusan' || userRole?.toLowerCase() === 'admin')">
                    <label class="block font-semibold mb-2">Tarikh Tamat Tempoh Lesen:</label>
                    <input
                        type="date"
                        class="form-input w-full"
                        v-model="form.expired_date"
                        required
                    />
                    <label class="block font-semibold mb-2">Masa Tamat Tempoh Lesen:</label>
                    <input
                        type="time"
                        class="form-input w-full"
                        v-model="form.expired_time"
                        required
                    />
                </div>
                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
                >
                    Hantar
                </button>
            </form>
        </div>

        <!-- Scroll to Top Button -->
        <button
            v-show="showScrollButton"
            @click="scrollToTop"
            class="fixed bottom-4 right-4 p-3 bg-blue-500 text-white rounded-full shadow-lg hover:bg-blue-700 transition"
        >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a1 1 0 01-1-1V4.414L4.707 8.707a1 1 0 01-1.414-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 4.414V17a1 1 0 01-1 1z" clip-rule="evenodd"/>
            </svg>
        </button>
    </AppLayout>
</template>