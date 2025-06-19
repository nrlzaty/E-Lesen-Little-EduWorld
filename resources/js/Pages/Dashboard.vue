<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const dashboardData = ref({
    penyemakanPending: 0,
    perlulusanPending: 0,
    newApplications: 0,
    diluluskan: 0,
    tidakDiluluskan: 0,
    expiringSoon: 0,
    borangTidakLengkap: 0,
    keraniNotifications: [],
    penyemakNotifications: [],
    perlulusNotifications: [],
});

const userRole = ref('');
const keraniNotiSeen = ref(false);

// Add userName for welcome title
const userName = ref('');

onMounted(async () => {
    try {
        const response = await axios.get('/dashboard-data');
        dashboardData.value = response.data;
        userRole.value = response.data.userRole;
        userName.value = page.props.auth.user.name || '';
        if (userRole.value === 'Pegawai Penyemakan' && response.data.renewSemakanNotifications) {
            dashboardData.value.renewReviewPending = response.data.renewSemakanNotifications.length;
        }
        // Update: check for 'Kerani'
        if (userRole.value === 'Kerani') {
            keraniDiluluslanNotiBlockVisible.value = localStorage.getItem('keraniDiluluslanNotiBlockVisible') !== 'false';
            keraniRenewDiluluslanNotiBlockVisible.value = localStorage.getItem('keraniRenewDiluluslanNotiBlockVisible') !== 'false';
        }
        // Hide dashboard noti block if user has clicked the dropdown noti
        if (
            userRole.value === 'Kerani' &&
            localStorage.getItem('keraniDiluluskanDropdownNotiHidden') === 'true'
        ) {
            keraniDiluluslanNotiBlockVisible.value = false;
        }
        if (
            userRole.value === 'Kerani' &&
            localStorage.getItem('keraniRenewDiluluskanDropdownNotiHidden') === 'true'
        ) {
            keraniRenewDiluluslanNotiBlockVisible.value = false;
        }
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    }
});

// Hide kerani notification block after click
const keraniNotiBlockVisible = ref(true);
const hideKeraniNotiBlock = () => {
    keraniNotiBlockVisible.value = false;
    keraniNotiSeen.value = true;
};

// Hide kerani diluluskan notification block and persist
const keraniDiluluslanNotiBlockVisible = ref(true);
const hideKeraniDiluluslanNotiBlock = () => {
    keraniDiluluslanNotiBlockVisible.value = false;
    localStorage.setItem('keraniDiluluslanNotiBlockVisible', 'false');
    // Also hide if triggered from dropdown
    localStorage.setItem('keraniDiluluskanDropdownNotiHidden', 'true');
};

// Hide kerani perbaharui lesen diluluskan notification block and persist
const keraniRenewDiluluslanNotiBlockVisible = ref(true);
const hideKeraniRenewDiluluslanNotiBlock = () => {
    keraniRenewDiluluslanNotiBlockVisible.value = false;
    localStorage.setItem('keraniRenewDiluluslanNotiBlockVisible', 'false');
    localStorage.setItem('keraniRenewDiluluskanDropdownNotiHidden', 'true');
};

// Pegawai Penyemakan noti block minimize state

const penyemakNotiBlockMinimized = ref(false);
const penyemakRenewSemakanNotiBlockMinimized = ref(false);

// Pegawai Perlulusan noti block minimize state
const perlulusTelahDisemakNotiBlockMinimized = ref(false);
const perlulusRenewTelahDisemakNotiBlockMinimized = ref(false);

// Pop-up for Kerani: show guideline to create/apply application
const keraniCreatePopupVisible = ref(true);
const hideKeraniCreatePopup = () => {
    keraniCreatePopupVisible.value = false;
};

// Remove a single notification from keraniDiluluskanNotifications
function removeKeraniDiluluskanNoti(id) {
    dashboardData.value.keraniDiluluskanNotifications = dashboardData.value.keraniDiluluskanNotifications.filter(n => n.id !== id);
}

// Remove a single notification from keraniRenewDiluluskanNotifications
function removeKeraniRenewDiluluskanNoti(id) {
    dashboardData.value.keraniRenewDiluluskanNotifications = dashboardData.value.keraniRenewDiluluskanNotifications.filter(n => n.id !== id);
}

// Remove a single notification from keraniNotifications
function removeKeraniNoti(id) {
    dashboardData.value.keraniNotifications = dashboardData.value.keraniNotifications.filter(n => n.id !== id);
}

// Add a computed property to count "Hampir Tamat Tempoh Lesen"
const hampirTamatTempohCount = computed(() => {
    // If backend already provides keraniHampirTamatTempoh, use it.
    if (dashboardData.value.keraniHampirTamatTempoh !== undefined) {
        return dashboardData.value.keraniHampirTamatTempoh;
    }
    // Fallback: count from keraniNotifications if available, else 0
    if (dashboardData.value.keraniRenewExpiringSoon !== undefined) {
        return dashboardData.value.keraniRenewExpiringSoon;
    }
    return 0;
});

// Add these functions for external trigger (from dropdown)
window.hideKeraniDiluluskanDashboardNoti = hideKeraniDiluluslanNotiBlock;
window.hideKeraniRenewDiluluskanDashboardNoti = hideKeraniRenewDiluluslanNotiBlock;

// Admin dashboard slider
const adminSlideIndex = ref(0);
const adminSlides = computed(() => [
    {
        label: 'Pemohonan Baru',
        value: dashboardData.value.newApplications ?? 0,
        color: 'text-blue-600',
        bg: 'bg-blue-100',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>`,
        link: route('status.pemohonan-baru'),
    },
    {
        label: 'Diluluskan',
        value: dashboardData.value.diluluskan ?? 0,
        color: 'text-green-600',
        bg: 'bg-green-100',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        link: route('status.diluluskan'),
    },
    {
        label: 'Tidak Diluluskan',
        value: dashboardData.value.tidakDiluluskan ?? 0,
        color: 'text-red-600',
        bg: 'bg-red-100',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
        link: route('status.tidak-diluluskan'),
    },
    {
        label: 'Perbaharui Lesen Diluluskan',
        value: dashboardData.value.renewDiluluskan ?? 0,
        color: 'text-green-600',
        bg: 'bg-green-100',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        link: '#',
    },
    {
        label: 'Perbaharui Lesen Tidak Diluluskan',
        value: dashboardData.value.renewTidakDiluluskan ?? 0,
        color: 'text-red-600',
        bg: 'bg-red-100',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
        link: '#',
    },
]);

const nextAdminSlide = () => {
    if (adminSlideIndex.value < adminSlides.value.length - 1) adminSlideIndex.value++;
};
const prevAdminSlide = () => {
    if (adminSlideIndex.value > 0) adminSlideIndex.value--;
};

// Only one dropdown open at a time, clicking one always closes the other
const penyemakanDropdownOpen = ref(false);
const perlulusanDropdownOpen = ref(false);

function openPenyemakanDropdown() {
    penyemakanDropdownOpen.value = !penyemakanDropdownOpen.value;
    if (penyemakanDropdownOpen.value) {
        perlulusanDropdownOpen.value = false;
    }
}
function openPerlulusanDropdown() {
    perlulusanDropdownOpen.value = !perlulusanDropdownOpen.value;
    if (perlulusanDropdownOpen.value) {
        penyemakanDropdownOpen.value = false;
    }
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Selamat Datang!{{ userName ? ' ' + userName : '' }}
            </h2>
        </template>

        <!-- PASTED IMAGE UI AT THE TOP -->
        <div v-if="userRole === 'Admin' && !dashboardData.isNewUser" class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Slider Card -->
                <div class="flex flex-col items-center">
                    <div class="relative w-full max-w-xl">
                        <div class="flex items-center justify-center">
                            <button @click="prevAdminSlide" :disabled="adminSlideIndex === 0" class="p-2 rounded-full bg-white shadow border border-gray-200 transition mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <div class="flex-1 flex justify-center">
                                <div class="bg-white rounded-2xl shadow p-8 flex items-center min-w-[350px] max-w-[400px]">
                                    <div class="flex-shrink-0 bg-blue-100 rounded-xl p-4 mr-6 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-4xl font-bold text-blue-600">{{ adminSlides[adminSlideIndex].value }}</div>
                                        <div class="text-gray-600 font-medium mt-1">{{ adminSlides[adminSlideIndex].label }}</div>
                                    </div>
                                </div>
                            </div>
                            <button @click="nextAdminSlide" :disabled="adminSlideIndex === adminSlides.length - 1" class="p-2 rounded-full bg-white shadow border border-gray-200 transition ml-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                        <!-- Dots -->
                        <div class="flex justify-center mt-4">
                            <span v-for="(slide, idx) in adminSlides" :key="idx"
                                class="w-2 h-2 rounded-full mx-1"
                                :class="adminSlideIndex === idx ? 'bg-blue-500' : 'bg-gray-200'"></span>
                        </div>
                    </div>
                </div>
                <!-- Dropdown Cards Row -->
                <div class="flex flex-col sm:flex-row justify-center gap-8 mt-8">
                    <!-- Pegawai Penyemakan Card with Dropdown -->
                    <div class="relative">
                        <button
                            @click="openPenyemakanDropdown"
                            class="flex items-center bg-[#FFFEF0] border border-yellow-100 rounded-2xl shadow p-6 min-w-[320px] hover:shadow-md transition focus:outline-none"
                        >
                            <span class="bg-yellow-100 rounded-full p-3 mr-4 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                </svg>
                            </span>
                            <span class="font-semibold text-lg text-black">Pegawai Penyemakan</span>
                            <svg :class="penyemakanDropdownOpen ? 'rotate-180' : ''" class="ml-auto h-5 w-5 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-if="penyemakanDropdownOpen" class="absolute left-0 z-10 mt-2 w-full min-w-[320px] bg-white rounded-2xl shadow-lg border border-yellow-100">
                            <div class="p-4 space-y-4">
                                <!-- Card 1 -->
                                <a :href="route('status.dalam-semakan')" class="flex items-center bg-yellow-50 rounded-xl p-4 hover:bg-yellow-100 transition">
                                    <span class="bg-yellow-100 rounded-full p-3 mr-4 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                        </svg>
                                    </span>
                                    <span>Penyemakan Belum Selesai: <b>{{ dashboardData.penyemakanPending ?? 0 }}</b></span>
                                </a>
                                <!-- Card 2 -->
                                <a :href="route('status.perbaharui-lesen')" class="flex items-center bg-purple-50 rounded-xl p-4 hover:bg-purple-100 transition">
                                    <span class="bg-purple-100 rounded-full p-3 mr-4 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                        </svg>
                                    </span>
                                    <span>Perbaharui Lesen Belum Disemak: <b>{{ dashboardData.renewReviewPending ?? 0 }}</b></span>
                                </a>
                                <!-- Card 3 -->
                                <a :href="route('status.borang-tidak-lengkap')" class="flex items-center bg-pink-50 rounded-xl p-4 hover:bg-pink-100 transition">
                                    <span class="bg-pink-100 rounded-full p-3 mr-4 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    </span>
                                    <span>Borang Tidak Lengkap: <b>{{ dashboardData.borangTidakLengkap ?? 0 }}</b></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Pegawai Perlulusan Card with Dropdown -->
                    <div class="relative" v-if="userRole === 'Pegawai Perlulusan' || userRole === 'Admin'">
                        <button
                            @click="openPerlulusanDropdown"
                            class="flex items-center bg-[#FFFEF0] border border-red-100 rounded-2xl shadow p-6 min-w-[320px] hover:shadow-md transition focus:outline-none"
                        >
                            <span class="bg-red-100 rounded-full p-3 mr-4 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                </svg>
                            </span>
                            <span class="font-semibold text-lg text-black">Pegawai Perlulusan</span>
                            <svg :class="perlulusanDropdownOpen ? 'rotate-180' : ''" class="ml-auto h-5 w-5 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-if="perlulusanDropdownOpen" class="absolute left-0 z-10 mt-2 w-full min-w-[320px] bg-white rounded-2xl shadow-lg border border-red-100">
                            <div class="p-4 space-y-4">
                                <!-- Card 1 -->
                                <a :href="route('status.telah-disemak')" class="flex items-center bg-red-50 rounded-xl p-4 hover:bg-red-100 transition">
                                    <span class="bg-red-100 rounded-full p-3 mr-4 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                        </svg>
                                    </span>
                                    <span>Perlulusan Belum Selesai: <b>{{ dashboardData.perlulusanPending ?? 0 }}</b></span>
                                </a>
                                <!-- Card 2 -->
                                <a :href="route('status.perbaharui-lesen-telah-disemak')" class="flex items-center bg-cyan-50 rounded-xl p-4 hover:bg-cyan-100 transition">
                                    <span class="bg-cyan-100 rounded-full p-3 mr-4 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                        </svg>
                                    </span>
                                    <span>Perbaharui Lesen Belum Diluluskan: <b>{{ dashboardData.renewTelahDisemak ?? 0 }}</b></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kerani Create Application Guideline Pop-up -->
        <div
            v-if="userRole === 'Kerani' && keraniCreatePopupVisible"
            class="mb-6"
        >
            <div class="bg-indigo-100 border-l-4 border-indigo-500 text-indigo-800 p-4 rounded-lg shadow flex items-start relative">
                <svg class="w-6 h-6 text-indigo-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <div class="flex-1">
                    <div class="font-semibold mb-1">
                        Panduan: Untuk membuat permohonan baru, sila klik butang di bawah.
                    </div>
                    <div class="mb-2">
                        <a
                            :href="route('applicant.create')"
                            class="inline-block bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition"
                        >
                            + Permohonan Baru
                        </a>
                    </div>
                </div>
                <button
                    @click="hideKeraniCreatePopup"
                    class="ml-4 text-indigo-700 hover:text-indigo-900 font-bold text-lg absolute top-2 right-4"
                    aria-label="Tutup"
                    style="background: none; border: none;"
                >&times;</button>
            </div>
        </div>

        <!-- Kerani Notification Block: Diluluskan -->
        <div
            v-if="$page.props.auth.user.role === 'kerani' && dashboardData && dashboardData.keraniDiluluskanNotifications && dashboardData.keraniDiluluskanNotifications.length > 0 && keraniDiluluslanNotiBlockVisible"
            class="mb-8"
        >
            <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow flex items-start relative">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <div class="font-semibold mb-1">
                        Tahniah! Permohonan anda telah <span class="font-bold">Diluluskan</span>.
                    </div>
                    <div class="text-sm mb-2">Klik nama permohonan untuk lihat maklumat:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="noti in dashboardData.keraniDiluluskanNotifications" :key="noti.id">
                            <a
                                :href="route('applicant.show', noti.id)"
                                class="font-semibold text-green-900 hover:underline hover:text-green-700 transition"
                                @click="hideKeraniDiluluslanNotiBlock"
                            >
                                {{ noti.nama }}
                            </a>
                        </li>
                    </ul>
                </div>
                <button
                    @click="hideKeraniDiluluslanNotiBlock"
                    class="ml-4 text-green-700 hover:text-green-900 font-bold text-lg absolute top-2 right-4"
                    aria-label="Tutup"
                    style="background: none; border: none;"
                >&times;</button>
            </div>
        </div>

        <!-- Kerani Notification Block: Perbaharui Lesen Diluluskan -->
        <div
            v-if="$page.props.auth.user.role === 'kerani' && dashboardData && dashboardData.keraniRenewDiluluskanNotifications && dashboardData.keraniRenewDiluluskanNotifications.length > 0 && keraniRenewDiluluslanNotiBlockVisible"
            class="mb-8"
        >
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded-lg shadow flex items-start relative">
                <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4m2 2v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"/>
                </svg>
                <div class="flex-1">
                    <div class="font-semibold mb-1">
                        Tahniah! Permohonan <span class="font-bold">Perbaharui Lesen</span> anda telah <span class="font-bold">Diluluskan</span>.
                    </div>
                    <div class="text-sm mb-2">Klik nama permohonan untuk lihat maklumat:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="noti in dashboardData.keraniRenewDiluluskanNotifications" :key="noti.id">
                            <a
                                :href="route('applicant.show', noti.id)"
                                class="font-semibold text-blue-900 hover:underline hover:text-blue-700 transition"
                                @click="hideKeraniRenewDiluluslanNotiBlock"
                            >
                                {{ noti.nama }}
                            </a>
                        </li>
                    </ul>
                </div>
                <button
                    @click="hideKeraniRenewDiluluslanNotiBlock"
                    class="ml-4 text-blue-700 hover:text-blue-900 font-bold text-lg absolute top-2 right-4"
                    aria-label="Tutup"
                    style="background: none; border: none;"
                >&times;</button>
            </div>
        </div>
        <!-- Kerani Notification Block: Borang Tidak Lengkap -->
        <div
            v-if="$page.props.auth.user.role === 'kerani' && dashboardData && dashboardData.keraniNotifications && dashboardData.keraniNotifications.length > 0 && keraniNotiBlockVisible"
            class="mb-2 mt-4"
        >
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-lg shadow flex items-start relative">
                <svg class="w-6 h-6 text-yellow-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <div class="flex-1">
                    <div class="font-semibold mb-1">
                        Sila kemaskini borang permohonan yang bertanda 
                        <span class="font-bold">Borang Tidak Lengkap</span>.
                    </div>
                    <div class="text-sm mb-2">Klik nama permohonan untuk kemaskini:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="noti in dashboardData.keraniNotifications" :key="noti.id">
                            <a
                                :href="route('applicant.edit', noti.id)"
                                class="font-semibold text-yellow-900 hover:underline hover:text-yellow-700 transition"
                            >
                                {{ noti.nama }}
                            </a>
                        </li>
                    </ul>
                </div>
                <button
                    @click="hideKeraniNotiBlock"
                    class="ml-4 text-yellow-700 hover:text-yellow-900 font-bold text-lg absolute top-2 right-4"
                    aria-label="Tutup"
                    style="background: none; border: none;"
                >&times;</button>
            </div>
        </div>

        

        <!-- Pegawai Penyemakan Notification Block (Borang Tidak Lengkap flow) -->
        <div
            v-if="(userRole === 'Pegawai Penyemakan' || userRole === 'Admin') && dashboardData && dashboardData.penyemakNotifications && dashboardData.penyemakNotifications.length > 0"
            class="mb-8 mt-2"
        >
            <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow flex items-start relative">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <div class="flex-1" v-show="!penyemakNotiBlockMinimized">
                    <div class="font-semibold mb-1">
                        Terdapat permohonan yang telah dikemaskini oleh Kerani selepas status <b>Borang Tidak Lengkap</b>.
                    </div>
                    <div class="text-sm mb-2">Klik nama permohonan untuk semakan:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="noti in dashboardData.penyemakNotifications" :key="noti.id">
                            <a
                                :href="route('status.show', noti.id)"
                                class="font-semibold text-green-900 hover:underline hover:text-green-700 transition"
                            >
                                {{ noti.nama }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="flex-1" v-show="penyemakNotiBlockMinimized">
                    <div class="font-semibold">
                        Terdapat permohonan yang telah dikemaskini oleh Kerani selepas status <b>Borang Tidak Lengkap</b>.
                    </div>
                </div>
                <button
                    @click="penyemakNotiBlockMinimized = !penyemakNotiBlockMinimized"
                    class="ml-4 text-green-700 hover:text-green-900 font-bold text-lg absolute top-2 right-4"
                    :aria-label="penyemakNotiBlockMinimized ? 'Maximize' : 'Minimize'"
                    style="background: none; border: none;"
                >
                    <span v-if="!penyemakNotiBlockMinimized">-</span>
                    <span v-else>+</span>
                </button>
            </div>
        </div>

        <!-- Pegawai Penyemakan Notification Block (Renew Lesen Dalam Semakan) -->
        <div
            v-if="(userRole === 'Pegawai Penyemakan' || userRole === 'Admin') && dashboardData && dashboardData.renewSemakanNotifications && dashboardData.renewSemakanNotifications.length > 0"
            class="mb-8 mt-4"
        >
            <div class="bg-purple-100 border-l-4 border-purple-500 text-purple-800 p-4 rounded-lg shadow flex items-start relative">
                <svg class="w-6 h-6 text-purple-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <div class="flex-1" v-show="!penyemakRenewSemakanNotiBlockMinimized">
                    <div class="font-semibold mb-1">
                        Terdapat permohonan <b>Perbaharui Lesen</b> yang telah dihantar oleh Kerani untuk semakan.
                    </div>
                    <div class="text-sm mb-2">Klik nama permohonan untuk semakan:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="noti in dashboardData.renewSemakanNotifications" :key="noti.id">
                            <a
                                :href="route('status.show', noti.id)"
                                class="font-semibold text-purple-900 hover:underline hover:text-purple-700 transition"
                            >
                                {{ noti.nama }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="flex-1" v-show="penyemakRenewSemakanNotiBlockMinimized">
                    <div class="font-semibold">
                        Terdapat permohonan <b>Perbaharui Lesen</b> yang telah dihantar oleh Kerani untuk semakan.
                    </div>
                </div>
                <button
                    @click="penyemakRenewSemakanNotiBlockMinimized = !penyemakRenewSemakanNotiBlockMinimized"
                    class="ml-4 text-purple-700 hover:text-purple-900 font-bold text-lg absolute top-2 right-4"
                    :aria-label="penyemakRenewSemakanNotiBlockMinimized ? 'Maximize' : 'Minimize'"
                    style="background: none; border: none;"
                >
                    <span v-if="!penyemakRenewSemakanNotiBlockMinimized">-</span>
                    <span v-else>+</span>
                </button>
            </div>
        </div>

        <!-- Pegawai Perlulusan Notification Block: Telah Disemak -->
        <div
            v-if="(userRole === 'Pegawai Perlulusan' || userRole === 'Admin') && dashboardData && dashboardData.perlulusNotifications && dashboardData.perlulusNotifications.length > 0"
            class="mb-8"
        >
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded-lg shadow flex items-start relative">
                <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <div class="flex-1" v-show="!perlulusTelahDisemakNotiBlockMinimized">
                    <div class="font-semibold mb-1">
                        Terdapat permohonan yang telah disemak oleh Pegawai Penyemakan dan menunggu kelulusan.
                    </div>
                    <div class="text-sm mb-2">Klik nama permohonan untuk proses kelulusan:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="noti in dashboardData.perlulusNotifications" :key="noti.id">
                            <a
                                :href="route('status.show', noti.id)"
                                class="font-semibold text-blue-900 hover:underline hover:text-blue-700 transition"
                            >
                                {{ noti.nama }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="flex-1" v-show="perlulusTelahDisemakNotiBlockMinimized">
                    <div class="font-semibold">
                        Terdapat permohonan telah disemak oleh Pegawai Penyemakan.
                    </div>
                </div>
                <button
                    @click="perlulusTelahDisemakNotiBlockMinimized = !perlulusTelahDisemakNotiBlockMinimized"
                    class="ml-4 text-blue-700 hover:text-blue-900 font-bold text-lg absolute top-2 right-4"
                    :aria-label="perlulusTelahDisemakNotiBlockMinimized ? 'Maximize' : 'Minimize'"
                    style="background: none; border: none;"
                >
                    <span v-if="!perlulusTelahDisemakNotiBlockMinimized">-</span>
                    <span v-else>+</span>
                </button>
            </div>
        </div>

        <!-- Pegawai Perlulusan Notification Block: Perbaharui Lesen Telah Disemak -->
        <div
            v-if="(userRole === 'Pegawai Perlulusan' || userRole === 'Admin') && dashboardData && dashboardData.renewTelahDisemakNotifications && dashboardData.renewTelahDisemakNotifications.length > 0"
            class="mb-8"
        >
            <div class="bg-cyan-100 border-l-4 border-cyan-500 text-cyan-800 p-4 rounded-lg shadow flex items-start relative">
                <svg class="w-6 h-6 text-cyan-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <div class="flex-1" v-show="!perlulusRenewTelahDisemakNotiBlockMinimized">
                    <div class="font-semibold mb-1">
                        Terdapat permohonan <b>Perbaharui Lesen</b> yang telah disemak oleh Pegawai Penyemakan dan menunggu kelulusan.
                    </div>
                    <div class="text-sm mb-2">Klik nama permohonan untuk proses kelulusan:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="noti in dashboardData.renewTelahDisemakNotifications" :key="noti.id">
                            <a
                                :href="route('status.show', noti.id)"
                                class="font-semibold text-cyan-900 hover:underline hover:text-cyan-700 transition"
                            >
                                {{ noti.nama }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="flex-1" v-show="perlulusRenewTelahDisemakNotiBlockMinimized">
                    <div class="font-semibold">
                        Terdapat permohonan Perbaharui Lesen telah disemak oleh Pegawai Penyemakan.
                    </div>
                </div>
                <button
                    @click="perlulusRenewTelahDisemakNotiBlockMinimized = !perlulusRenewTelahDisemakNotiBlockMinimized"
                    class="ml-4 text-cyan-700 hover:text-cyan-900 font-bold text-lg absolute top-2 right-4"
                    :aria-label="perlulusRenewTelahDisemakNotiBlockMinimized ? 'Maximize' : 'Minimize'"
                    style="background: none; border: none;"
                >
                    <span v-if="!perlulusRenewTelahDisemakNotiBlockMinimized">-</span>
                    <span v-else>+</span>
                </button>
            </div>
        </div>

        <!-- Kerani Dashboard Cards -->
        <div v-if="userRole === 'kerani' && !dashboardData.isNewUser" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Permohonan Diluluskan card for Kerani -->
                    <div class="bg-white shadow rounded-xl p-5 flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                            <!-- CheckCircleIcon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-green-500">
                                <a
                                    v-if="dashboardData.diluluskan > 0"
                                    :href="route('applicant.index', { status: 'Diluluskan' })"
                                    class="hover:underline"
                                >
                                    {{ dashboardData.diluluskan }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Permohonan Diluluskan</div>
                        </div>
                    </div>
                    <!-- Perbaharui Lesen Diluluskan card for Kerani -->
                    <div class="bg-white shadow rounded-xl p-5 flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                            <!-- DocumentCheckIcon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4m2 2v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-blue-500">
                                <a
                                    v-if="dashboardData.keraniRenewDiluluskan > 0"
                                    :href="route('applicant.index', { status: 'Perbaharui Lesen Diluluskan' })"
                                    class="hover:underline"
                                >
                                    {{ dashboardData.keraniRenewDiluluskan }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Perbaharui Lesen Diluluskan</div>
                        </div>
                    </div>
                    <!-- Card: Perbaharui Lesen Hampir Tamat Tempoh -->
                    <div class="bg-white shadow rounded-xl p-5 flex items-center">
                        <div class="flex-shrink-0 bg-orange-100 rounded-full p-3">
                            <!-- ExclamationCircleIcon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-orange-500">
                                <a
                                    v-if="hampirTamatTempohCount > 0"
                                    :href="route('renew-license.index')"
                                    class="hover:underline"
                                >
                                    {{ hampirTamatTempohCount }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Perbaharui Lesen Hampir Tamat Tempoh</div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- ADMIN DASHBOARD REDESIGN
        <div v-if="userRole === 'Admin' && !dashboardData.isNewUser" class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                Slider for main stats 
                <div class="flex flex-col items-center mb-10">
                    <div class="relative w-full max-w-lg">
                        SLIDER CARDS (stay at the top)
                        <div class="overflow-hidden rounded-2xl shadow-lg bg-white/60 backdrop-blur border border-blue-100">
                            <div class="transition-all duration-300 flex" :style="{ transform: `translateX(-${adminSlideIndex * 100}%)` }">
                                <div v-for="(slide, idx) in adminSlides" :key="slide.label" class="min-w-full flex items-center p-8">
                                    <div :class="['flex-shrink-0 rounded-xl p-4 mr-6 shadow', slide.bg]" style="box-shadow: 0 2px 16px 0 rgba(0,0,0,0.06);">
                                        <span v-html="slide.icon"></span>
                                    </div>
                                    <div>
                                        <div :class="['text-4xl font-extrabold', slide.color]">
                                            <a v-if="slide.link && slide.link !== '#'" :href="slide.link" class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-400 rounded transition">
                                                {{ slide.value }}
                                            </a>
                                            <span v-else>{{ slide.value }}</span>
                                        </div>
                                        <div class="text-base text-gray-600 mt-2 font-medium tracking-wide">{{ slide.label }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        SLIDER NAVIGATION (move here, below the cards)
                        <div class="flex items-center justify-between mt-4">
                            <button @click="prevAdminSlide" :disabled="adminSlideIndex === 0" class="p-2 rounded-full bg-white/60 hover:bg-blue-100 shadow border border-blue-100 disabled:opacity-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <div class="flex space-x-2">
                                <span v-for="(slide, idx) in adminSlides" :key="idx" class="w-2 h-2 rounded-full transition-all duration-200" :class="adminSlideIndex === idx ? 'bg-blue-500' : 'bg-gray-300'"></span>
                            </div>
                            <button @click="nextAdminSlide" :disabled="adminSlideIndex === adminSlides.length - 1" class="p-2 rounded-full bg-white/60 hover:bg-blue-100 shadow border border-blue-100 disabled:opacity-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                 Kerja Belum Selesai section
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                     Pegawai Penyemakan Dropdown
                    <div class="glass-card">
                        <button @click="openPenyemakanDropdown" class="w-full flex items-center justify-between p-6 focus:outline-none hover:bg-blue-50/40 rounded-2xl transition">
                            <div class="flex items-center">
                                <span class="bg-yellow-100 rounded-xl p-3 mr-4 shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>

                                </span>
                                <span class="font-semibold text-lg tracking-wide">Pegawai Penyemakan</span>
                            </div>
                            <svg :class="penyemakanDropdownOpen ? 'rotate-180' : ''" class="h-6 w-6 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-if="penyemakanDropdownOpen" class="border-t border-blue-100 px-6 pb-6">
                            <ul class="divide-y divide-blue-50">
                                <li class="py-4 flex items-center">
                                    <a :href="route('status.dalam-semakan')" class="flex items-center w-full hover:bg-yellow-50 rounded-xl transition p-2 -m-2">
                                        <span class="bg-yellow-100 rounded-xl p-3 mr-4 shadow">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                            </svg>
                                        </span>
                                        <span>Penyemakan Belum Selesai: <b>{{ dashboardData.penyemakanPending ?? 0 }}</b></span>
                                    </a>
                                </li>
                                <li class="py-4 flex items-center">
                                    <a :href="route('status.perbaharui-lesen')" class="flex items-center w-full hover:bg-purple-50 rounded-xl transition p-2 -m-2">
                                        <span class="bg-purple-100 rounded-xl p-3 mr-4 shadow">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                            </svg>
                                        </span>
                                        <span>Perbaharui Lesen Belum Selesai: <b>{{ dashboardData.renewReviewPending ?? 0 }}</b></span>
                                    </a>
                                </li>
                                <li class="py-4 flex items-center">
                                    <a :href="route('status.borang-tidak-lengkap')" class="flex items-center w-full hover:bg-pink-50 rounded-xl transition p-2 -m-2">
                                        <span class="bg-pink-100 rounded-xl p-3 mr-4 shadow">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        </span>
                                        <span>Borang Tidak Lengkap: <b>{{ dashboardData.borangTidakLengkap ?? 0 }}</b></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    Pegawai Perlulusan Dropdown -
                    <div class="glass-card">
                        <button @click="openPerlulusanDropdown" class="w-full flex items-center justify-between p-6 focus:outline-none hover:bg-blue-50/40 rounded-2xl transition">
                            <div class="flex items-center">
                                <span class="bg-red-100 rounded-xl p-3 mr-4 shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
</svg>

                                </span>
                                <span class="font-semibold text-lg tracking-wide">Pegawai Perlulusan</span>
                            </div>
                            <svg :class="perlulusanDropdownOpen ? 'rotate-180' : ''" class="h-6 w-6 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-if="perlulusanDropdownOpen" class="border-t border-blue-100 px-6 pb-6">
                            <ul class="divide-y divide-blue-50">
                                <li class="py-4 flex items-center">
                                    <a :href="route('status.telah-disemak')" class="flex items-center w-full hover:bg-red-50 rounded-xl transition p-2 -m-2">
                                        <span class="bg-red-100 rounded-xl p-3 mr-4 shadow">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                        </span>
                                        <span>Perlulusan Belum Selesai: <b>{{ dashboardData.perlulusanPending ?? 0 }}</b></span>
                                    </a>
                                </li>
                                <li class="py-4 flex items-center">
                                    <a :href="route('status.perbaharui-lesen-telah-disemak')" class="flex items-center w-full hover:bg-cyan-50 rounded-xl transition p-2 -m-2">
                                        <span class="bg-cyan-100 rounded-xl p-3 mr-4 shadow">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                        </span>
                                        <span>Perlulusan Perbaharui Lesen Belum Selesai: <b>{{ dashboardData.renewTelahDisemak ?? 0 }}</b></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Other roles dashboard cards -->
        <div v-if="userRole && userRole !== 'kerani' && !dashboardData.isNewUser">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-if="userRole !== 'Admin'"
                    class="grid grid-cols-1 md:grid-cols-3 gap-6"
                >
                    <!-- Pegawai Penyemakan & Admin: Pemohonan Baru -->
                    <div
                        v-if="userRole === 'Pegawai Penyemakan' || userRole === 'Admin'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                            <!-- Custom SVG icon as requested -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-blue-600">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-blue-600">
                                <a v-if="dashboardData.newApplications > 0" :href="route('status.pemohonan-baru')" class="hover:underline">
                                    {{ dashboardData.newApplications }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Pemohonan Baru</div>
                        </div>
                    </div>

                    <!-- Pegawai Penyemakan & Admin: Borang Tidak Lengkap -->
                    <div
                        v-if="userRole === 'Admin' || userRole === 'Pegawai Penyemakan'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-pink-100 rounded-full p-3">
                            <!-- Custom SVG icon for Borang Tidak Lengkap -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-pink-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-pink-500">
                                <a v-if="dashboardData.borangTidakLengkap > 0" :href="route('status.borang-tidak-lengkap')" class="hover:underline">
                                    {{ dashboardData.borangTidakLengkap }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Borang Tidak Lengkap</div>
                        </div>
                    </div>

                    <!-- Pegawai Penyemakan & Admin: Penyemakan Pending -->
                    <div
                        v-if="userRole === 'Pegawai Penyemakan' || userRole === 'Admin'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                            <!-- ClipboardListIcon SVG -->
                            a<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-yellow-500">
                                <a v-if="dashboardData.penyemakanPending > 0" :href="route('status.dalam-semakan')" class="hover:underline">
                                    {{ dashboardData.penyemakanPending }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Penyemakan Belum Selesai</div>
                        </div>
                    </div>

                    <!-- Pegawai Penyemakan: Perbaharui Lesen Pending Card -->
                    <div
                        v-if="userRole === 'Pegawai Penyemakan' || userRole === 'Admin'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                            <!-- Custom SVG icon for Perbaharui Lesen Belum Selesai -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-purple-600">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-purple-600">
                                <a v-if="dashboardData.renewReviewPending > 0" :href="route('status.perbaharui-lesen')" class="hover:underline">
                                    {{ dashboardData.renewReviewPending }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Perbaharui Lesen Belum Selesai</div>
                        </div>
                    </div>

                    <!-- Pegawai Perlulusan & Admin: Perlulusan Belum Selesai -->
                    <div
                        v-if="userRole === 'Pegawai Perlulusan' || userRole === 'Admin'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                            <!-- ClipboardListIcon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-red-500">
                                <a v-if="dashboardData.perlulusanPending > 0" :href="route('status.telah-disemak')" class="hover:underline">
                                    {{ dashboardData.perlulusanPending }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Perlulusan Belum Selesai</div>
                        </div>
                    </div>
                    <!-- Pegawai Perlulusan & Admin: Perlulusan Perbaharui Lesen Belum Selesai -->
                    <div
                        v-if="userRole === 'Pegawai Perlulusan' || userRole === 'Admin'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-cyan-100 rounded-full p-3">
                            <!-- SVG icon as requested -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-cyan-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-cyan-500">
                                <a v-if="dashboardData.renewTelahDisemak > 0" :href="route('status.perbaharui-lesen-telah-disemak')" class="hover:underline">
                                    {{ dashboardData.renewTelahDisemak }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Perlulus Perbaharui Lesen Belum Selesai</div>
                        </div>
                    </div>
                    <!-- NEW: Perbaharui Lesen Diluluskan card for Pegawai Perlulusan -->
                    <div
                        v-if="userRole === 'Pegawai Perlulusan' || userRole === 'Admin'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                            <!-- CheckCircleIcon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-green-500">
                                <span>{{ dashboardData.renewDiluluskan ?? 0 }}</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Perbaharui Lesen Diluluskan</div>
                        </div>
                    </div>
                    <!-- NEW: Perbaharui Lesen Tidak Diluluskan card for Pegawai Perlulusan -->
                    <div
                        v-if="userRole === 'Pegawai Perlulusan' || userRole === 'Admin'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                            <!-- XCircleIcon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-red-600">
                                <span>{{ dashboardData.renewTidakDiluluskan ?? 0 }}</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Perbaharui Lesen Tidak Diluluskan</div>
                        </div>
                    </div>
                    <div
                        v-if="userRole === 'Pegawai Perlulusan' || userRole === 'Admin'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                            <!-- CheckCircleIcon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-green-500">
                                <a v-if="dashboardData.diluluskan > 0" :href="route('status.diluluskan')" class="hover:underline">
                                    {{ dashboardData.diluluskan }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Diluluskan</div>
                        </div>
                    </div>
                    <div
                        v-if="userRole === 'Pegawai Perlulusan' || userRole === 'Admin'"
                        class="bg-white shadow rounded-xl p-5 flex items-center"
                    >
                        <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                            <!-- XCircleIcon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-3xl font-extrabold text-red-600">
                                <a v-if="dashboardData.tidakDiluluskan > 0" :href="route('status.tidak-diluluskan')" class="hover:underline">
                                    {{ dashboardData.tidakDiluluskan }}
                                </a>
                                <span v-else>0</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Tidak Diluluskan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.glass-card {
    @apply bg-white/60 backdrop-blur rounded-2xl shadow-lg border border-blue-100 transition;
}
</style>




