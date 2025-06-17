<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const props = defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

const isOpen = ref(false);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const isBuka = ref(false);
const toggleSenaraiTindakan = () => {
    isBuka.value = !isBuka.value;
};

const keraniNotifications = ref([]);
const keraniDiluluskanNotifications = ref([]);
const keraniRenewDiluluskanNotifications = ref([]);

const keraniNotiOpen = ref(false);
const keraniNotiSeen = ref(false);

// Add: track if diluluskan/renew diluluskan noti has been seen (dropdown)
const keraniDiluluskanNotiSeen = ref(localStorage.getItem('keraniDiluluskanNotiSeen') === 'true');
const keraniRenewDiluluskanNotiSeen = ref(localStorage.getItem('keraniRenewDiluluskanNotiSeen') === 'true');

// Hide dropdown and persist seen state for each noti type
function hideKeraniNotiDropdown(type) {
    keraniNotiOpen.value = false;
    if (type === 'diluluskan') {
        keraniDiluluskanNotiSeen.value = true;
        localStorage.setItem('keraniDiluluskanNotiSeen', 'true');
    }
    if (type === 'renewDiluluskan') {
        keraniRenewDiluluskanNotiSeen.value = true;
        localStorage.setItem('keraniRenewDiluluskanNotiSeen', 'true');
    }
}

const keraniNotiCount = computed(() =>
    keraniNotifications.value.filter(noti => noti.status === 'Borang Tidak Lengkap').length +
    keraniDiluluskanNotifications.value.length +
    keraniRenewDiluluskanNotifications.value.length
);

const penyemakNotifications = ref([]);
const penyemakRenewSemakanNotifications = ref([]);
const penyemakNotiOpen = ref(false);
const penyemakNotiSeen = ref(false);

const penyemakTotalNotiCount = computed(() =>
    penyemakNotifications.value.length + penyemakRenewSemakanNotifications.value.length
);

// New: Perlulusan Notifications
const perlulusanNotifications = ref([]); // Telah Disemak
const perlulusanRenewTelahDisemakNotifications = ref([]); // Perbaharui Lesen Telah Disemak
const perlulusanNotiOpen = ref(false);
const perlulusanNotiSeen = ref(false);

const perlulusanNotiCount = computed(() =>
    perlulusanNotifications.value.length + perlulusanRenewTelahDisemakNotifications.value.length
);

const page = usePage();

const fetchKeraniNotifications = async () => {
    if (page.props.auth.user.role === 'kerani') {
        try {
            const response = await axios.get('/dashboard-data');
            keraniNotifications.value = response.data.keraniNotifications || [];
            keraniDiluluskanNotifications.value = response.data.keraniDiluluskanNotifications || [];
            keraniRenewDiluluskanNotifications.value = response.data.keraniRenewDiluluskanNotifications || [];
            if (
                keraniNotifications.value.length > 0 ||
                keraniDiluluskanNotifications.value.length > 0 ||
                keraniRenewDiluluskanNotifications.value.length > 0
            ) {
                keraniNotiSeen.value = false;
            }
            // Reset seen state if there are new notifications
            if (keraniDiluluskanNotifications.value.length > 0) {
                keraniDiluluskanNotiSeen.value = false;
                localStorage.removeItem('keraniDiluluskanNotiSeen');
            }
            if (keraniRenewDiluluskanNotifications.value.length > 0) {
                keraniRenewDiluluskanNotiSeen.value = false;
                localStorage.removeItem('keraniRenewDiluluskanNotiSeen');
            }
        } catch (e) {
            keraniNotifications.value = [];
            keraniDiluluskanNotifications.value = [];
            keraniRenewDiluluskanNotifications.value = [];
        }
    }
};

const fetchPenyemakNotifications = async () => {
    if (page.props.auth.user.role === 'Pegawai Penyemakan') {
        try {
            // Get both types of notifications
            const [notiRes, renewRes] = await Promise.all([
                axios.get('/dashboard-data'),
                axios.get('/penyemak-renew-dalam-semak-notifications')
            ]);
            penyemakNotifications.value = notiRes.data.penyemakNotifications || [];
            penyemakRenewSemakanNotifications.value = (renewRes.data.notifications || []).map(n => ({
                ...n,
                kerani_nama: n.kerani_nama // if available
            }));
            if (
                penyemakNotifications.value.length > 0 ||
                penyemakRenewSemakanNotifications.value.length > 0
            ) {
                penyemakNotiSeen.value = false;
            }
        } catch (e) {
            penyemakNotifications.value = [];
            penyemakRenewSemakanNotifications.value = [];
        }
    }
};

// New: Fetch Perlulusan Notifications
const fetchPerlulusanNotifications = async () => {
    if (page.props.auth.user.role === 'Pegawai Perlulusan') {
        try {
            const [telahDisemakRes, renewTelahDisemakRes] = await Promise.all([
                axios.get('/perlulus-telah-disemak-notifications'),
                axios.get('/perlulus-renew-telah-disemak-notifications')
            ]);
            perlulusanNotifications.value = telahDisemakRes.data.notifications || [];
            perlulusanRenewTelahDisemakNotifications.value = renewTelahDisemakRes.data.notifications || [];
            if (
                perlulusanNotifications.value.length > 0 ||
                perlulusanRenewTelahDisemakNotifications.value.length > 0
            ) {
                perlulusanNotiSeen.value = false;
            }
        } catch (e) {
            perlulusanNotifications.value = [];
            perlulusanRenewTelahDisemakNotifications.value = [];
        }
    }
};

onMounted(() => {
    fetchKeraniNotifications();
    fetchPenyemakNotifications();
    fetchPerlulusanNotifications();
});

const toggleKeraniNoti = () => {
    keraniNotiOpen.value = !keraniNotiOpen.value;
    if (keraniNotiOpen.value) {
        fetchKeraniNotifications();
        keraniNotiSeen.value = true;
    }
};

const togglePenyemakNoti = () => {
    penyemakNotiOpen.value = !penyemakNotiOpen.value;
    if (penyemakNotiOpen.value) {
        fetchPenyemakNotifications();
        penyemakNotiSeen.value = true;
    }
};

// New: Toggle Perlulusan Noti
const togglePerlulusanNoti = () => {
    perlulusanNotiOpen.value = !perlulusanNotiOpen.value;
    if (perlulusanNotiOpen.value) {
        fetchPerlulusanNotifications();
        perlulusanNotiSeen.value = true;
    }
};

// Remove a single notification from keraniDiluluskanNotifications
function removeKeraniDiluluskanNoti(id) {
    keraniDiluluskanNotifications.value = keraniDiluluskanNotifications.value.filter(n => n.id !== id);
    // Also hide dashboard block for this noti type
    localStorage.setItem('keraniDiluluskanDropdownNotiHidden', 'true');
    if (window.hideKeraniDiluluskanDashboardNoti) window.hideKeraniDiluluskanDashboardNoti();
}

// Remove a single notification from keraniRenewDiluluskanNotifications
function removeKeraniRenewDiluluskanNoti(id) {
    keraniRenewDiluluskanNotifications.value = keraniRenewDiluluskanNotifications.value.filter(n => n.id !== id);
    localStorage.setItem('keraniRenewDiluluskanDropdownNotiHidden', 'true');
    if (window.hideKeraniRenewDiluluskanDashboardNoti) window.hideKeraniRenewDiluluskanDashboardNoti();
}
</script>

<template>
<Head :title="title" />   
<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center justify-start rtl:justify-end">
        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
               <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
         </button>
        <a href="https://flowbite.com" class="flex ms-2 md:me-24 items-center">
          <img src="/images/logo.png" class="h-8 me-3" alt="Logo" />
          <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">E-Lesen Little EduWorld</span>
        </a>
      </div>
      <div class="flex items-center">
        <!-- Combined Kerani Notification Bell -->
        <div v-if="$page.props.auth.user.role === 'kerani'" class="relative mr-4">
            <button @click="toggleKeraniNoti" class="relative focus:outline-none">
                <!-- Bell SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 ">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                <span
                    v-if="keraniNotiCount > 0 && !keraniNotiOpen && !keraniNotiSeen"
                    class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-2 py-0.5 font-bold"
                >
                    {{ keraniNotiCount }}
                </span>
            </button>
            <!-- Dropdown -->
            <div v-if="keraniNotiOpen" class="absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                <div class="p-3 border-b font-semibold bg-white rounded-t-lg">Notifikasi</div>
                <div v-if="keraniNotiCount === 0" class="p-4 text-gray-500 text-sm">Tiada notifikasi baru.</div>
                <ul v-else class="max-h-96 overflow-y-auto">
                    <!-- Borang Tidak Lengkap -->
                    <li v-for="noti in keraniNotifications.filter(n => n.status === 'Borang Tidak Lengkap').reverse()" :key="'borang-' + noti.id">
                        <a
                            :href="route('applicant.edit', noti.id)"
                            class="block px-4 py-3 border-b border-gray-200 last:border-b-0 bg-white hover:bg-gray-50 transition rounded"
                        >
                            <div class="font-medium text-gray-900">
                                Status: <span class="capitalize">{{ noti.status }}</span>
                            </div>
                            <div class="text-sm text-gray-700">
                                <span v-if="noti.komen && noti.komen.trim() !== ''">Komen: {{ noti.komen }}</span>
                                <span v-else>Komen: Tiada komen</span>
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Tindakan diperlukan: Kemaskini borang permohonan.
                            </div>
                        </a>
                    </li>
                    <!-- Diluluskan -->
                    <li
                        v-for="noti in keraniDiluluskanNotifications"
                        :key="'diluluskan-' + noti.id"
                    >
                        <a
                            :href="route('applicant.show', noti.id)"
                            class="block px-4 py-3 border-b border-gray-200 last:border-b-0 bg-white hover:bg-gray-50 transition rounded"
                            @click="removeKeraniDiluluskanNoti(noti.id)"
                        >
                            <div class="font-semibold text-gray-900">
                                Tahniah! Permohonan anda telah <span class="font-bold">Diluluskan</span>.
                            </div>
                            <div class="text-sm text-gray-700">
                                Klik nama permohonan untuk lihat maklumat:
                            </div>
                            <div class="font-bold text-gray-800 mt-1">
                                {{ noti.nama }}
                            </div>
                        </a>
                    </li>
                    <!-- Perbaharui Lesen Diluluskan -->
                    <li
                        v-for="noti in keraniRenewDiluluskanNotifications"
                        :key="'renew-diluluskan-' + noti.id"
                    >
                        <a
                            :href="route('applicant.show', noti.id)"
                            class="block px-4 py-3 border-b border-gray-200 last:border-b-0 bg-white hover:bg-gray-50 transition rounded"
                            @click="removeKeraniRenewDiluluskanNoti(noti.id)"
                        >
                            <div class="font-semibold text-gray-900">
                                Tahniah! Permohonan <span class="font-bold">Perbaharui Lesen</span> anda telah <span class="font-bold">Diluluskan</span>.
                            </div>
                            <div class="text-sm text-gray-700">
                                Klik nama permohonan untuk lihat maklumat:
                            </div>
                            <div class="font-bold text-gray-800 mt-1">
                                {{ noti.nama }}
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Pegawai Penyemakan Notification Bell -->
        <div v-if="$page.props.auth.user.role === 'Pegawai Penyemakan'" class="relative mr-4">
            <button @click="togglePenyemakNoti" class="relative focus:outline-none">
                <!-- Bell SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-700 dark:text-white">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                <span
                    v-if="penyemakTotalNotiCount > 0 && !penyemakNotiOpen && !penyemakNotiSeen"
                    class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-2 py-0.5 font-bold"
                >
                    {{ penyemakTotalNotiCount }}
                </span>
            </button>
            <!-- Dropdown -->
            <div v-if="penyemakNotiOpen" class="absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                <div class="p-3 border-b font-semibold text-gray-700">Notifikasi</div>
                <div v-if="penyemakNotifications.length === 0 && penyemakRenewSemakanNotifications.length === 0" class="p-4 text-gray-500 text-sm">Tiada notifikasi baru.</div>
                <ul v-else class="max-h-96 overflow-y-auto">
                    <!-- Existing notifications -->
                    <li v-for="noti in [...penyemakNotifications].reverse()" :key="'semak-' + noti.id">
                        <a
                            :href="route('status.show', noti.id)"
                            class="block px-4 py-3 border-b last:border-b-0 hover:bg-green-50 transition rounded"
                        >
                            <div class="font-medium">
                                Status: <span class="capitalize">{{ noti.status }}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <span v-if="noti.komen && noti.komen.trim() !== ''">Komen: {{ noti.komen }}</span>
                                <span v-else>Komen: Tiada komen</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Permohonan telah dikemaskini oleh Kerani. Sila semak semula borang permohonan.
                            </div>
                        </a>
                    </li>
                    <!-- Perbaharui Lesen Dalam Semakan notifications (highlighted) -->
                    <li v-for="noti in penyemakRenewSemakanNotifications" :key="'renew-' + noti.id">
                        <a
                            :href="route('status.show', noti.id)"
                            class="block px-4 py-3 border-b last:border-b-0 bg-gradient-to-r from-purple-100 to-purple-50 hover:bg-purple-200 transition rounded"
                        >
                            <div class="font-medium text-purple-800">
                                <span class="capitalize">Perbaharui Lesen Dalam Semakan</span>
                            </div>
                            <div class="text-sm text-purple-700">
                                <span v-if="noti.komen && noti.komen.trim() !== ''">Komen: {{ noti.komen }}</span>
                                <span v-else>Komen: Tiada komen</span>
                            </div>
                            <div class="text-xs text-purple-600 mt-1">
                                Permohonan telah dihantar oleh Kerani: <span class="font-semibold">{{ noti.kerani_nama || 'Kerani' }}</span>. Sila semak permohonan perbaharui lesen.
                            </div>
                            <div class="text-xs text-purple-400 mt-1" v-if="noti.updated_at">
                                Dikemaskini pada: {{ new Date(noti.updated_at).toLocaleString() }}
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Pegawai Perlulusan Notification Bell -->
        <div v-if="$page.props.auth.user.role === 'Pegawai Perlulusan'" class="relative mr-4">
            <button @click="togglePerlulusanNoti" class="relative focus:outline-none">
                <!-- Bell SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-700 dark:text-white">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                <span
                    v-if="perlulusanNotiCount > 0 && !perlulusanNotiOpen && !perlulusanNotiSeen"
                    class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-2 py-0.5 font-bold"
                >
                    {{ perlulusanNotiCount }}
                </span>
            </button>
            <!-- Dropdown -->
            <div v-if="perlulusanNotiOpen" class="absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                <div class="p-3 border-b font-semibold text-gray-700">Notifikasi</div>
                <div v-if="perlulusanNotiCount === 0" class="p-4 text-gray-500 text-sm">Tiada notifikasi baru.</div>
                <ul v-else class="max-h-96 overflow-y-auto">
                    <!-- Telah Disemak notifications -->
                    <li v-for="noti in [...perlulusanNotifications].reverse()" :key="'perlulus-telah-disemak-' + noti.id">
                        <a
                            :href="route('status.show', noti.id)"
                            class="block px-4 py-3 border-b last:border-b-0 hover:bg-green-50 transition rounded"
                        >
                            <div class="font-medium">
                                Status: <span class="capitalize">{{ noti.status }}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <span v-if="noti.komen && noti.komen.trim() !== ''">Komen: {{ noti.komen }}</span>
                                <span v-else>Komen: Tiada komen</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Permohonan telah disemak oleh Pegawai Penyemakan. Sila buat keputusan kelulusan.
                            </div>
                        </a>
                    </li>
                    <!-- Perbaharui Lesen Telah Disemak notifications (highlighted) -->
                    <li v-for="noti in perlulusanRenewTelahDisemakNotifications" :key="'perlulus-renew-telah-disemak-' + noti.id">
                        <a
                            :href="route('status.show', noti.id)"
                            class="block px-4 py-3 border-b last:border-b-0 bg-gradient-to-r from-blue-100 to-blue-50 hover:bg-blue-200 transition rounded"
                        >
                            <div class="font-medium text-blue-800">
                                <span class="capitalize">Perbaharui Lesen Telah Disemak</span>
                            </div>
                            <div class="text-sm text-blue-700">
                                <span v-if="noti.komen && noti.komen.trim() !== ''">Komen: {{ noti.komen }}</span>
                                <span v-else>Komen: Tiada komen</span>
                            </div>
                            <div class="text-xs text-blue-600 mt-1">
                                Permohonan perbaharui lesen telah disemak oleh Pegawai Penyemakan. Sila buat keputusan kelulusan.
                            </div>
                            <div class="text-xs text-blue-400 mt-1" v-if="noti.updated_at">
                                Dikemaskini pada: {{ new Date(noti.updated_at).toLocaleString() }}
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="flex items-center ms-3">
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                        <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                    </button>

                    <span v-else class="inline-flex rounded-md">
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                            {{ $page.props.auth.user.name }}

                            <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </span>
                </template>

                <template #content>
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        Urus Akaun
                    </div>

                    <DropdownLink :href="route('profile.show')">
                        Profil
                    </DropdownLink>

                    <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                        Token API
                    </DropdownLink>

                    <div class="border-t border-gray-200" />

                    <!-- Authentication -->
                    <form @submit.prevent="logout">
                        <DropdownLink as="button">
                            Log Keluar
                        </DropdownLink>
                    </form>
                </template>
            </Dropdown>
        </div>
      </div>
    </div>
  </div>
</nav>
<div v-if="$page.props.auth.user.role && $page.props.auth.user.role.toLowerCase() === 'admin'">
  <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
         <li>
            <a
                :href="route('dashboard')"
                :class="[$page.url === '/dashboard' ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>

               <span class="ms-3">Laman Utama</span>
            </a>
         </li>
         <li>
            <a
                :href="route('applicant.index')"
                :class="[$page.url.startsWith('/applicant') ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
              </svg>

               <span class="ms-3">Senarai Pemohonan</span>
            </a>
         </li>
         <!-- <li>
            <button type="button" @click="toggleSenaraiTindakan" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
              </svg>

                <span class="flex-1 ms-3 text-left whitespace-nowrap">Senarai Tindakan</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>
            <ul v-if="isBuka" class="py-2 space-y-2">
              <li>
                <a :href="route('status.pemohonan-baru')" class="flex items-center w-full p-2 text-gray-900 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                  Pemohonan Baru
                </a>
              </li>
              <li>
                <a :href="route('status.dalam-semakan')" class="flex items-center w-full p-2 text-gray-900 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                  Dalam Semakan
                </a>
              </li>
              <li>
                <a :href="route('status.telah-disemak')" class="flex items-center w-full p-2 text-gray-900 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                  Telah Disemak
                </a>
              </li>
              <li>
                <a :href="route('status.borang-tidak-lengkap')" class="flex items-center w-full p-2 text-gray-900 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                  Borang Tidak Lengkap
                </a>
              </li>
              <li>
                <a :href="route('status.diluluskan')" class="flex items-center w-full p-2 text-gray-900 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                  Diluluskan
                </a>
              </li>
              <li>
                <a :href="route('status.tidak-diluluskan')" class="flex items-center w-full p-2 text-gray-900 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                  Tidak Diluluskan
                </a>
              </li>
            </ul>
          </li> -->
         <li>
            <a
                :href="route('status.index')"
                :class="[$page.url.startsWith('/status') ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
              </svg>

               <span class="flex-1 ms-3 whitespace-nowrap">Senarai Tindakan</span>
            </a>
         </li>
         <li>
            <button type="button" @click="toggleDropdown" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-example">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257.75.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>

                <span class="flex-1 ms-3 text-left whitespace-nowrap">Tetapan</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>
            <ul v-if="isOpen" class="py-2 space-y-2">
              <li>
                <a :href="route('setting.role.index')" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                  Peranan
                </a>
              </li>
              <li>
                <a :href="route('setting.user.index')" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                  Pengguna
                </a>
              </li>
              <li>
                <a :href="route('setting.category.index')" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                  Kategori
                </a>
              </li>
            </ul>
          </li>
      </ul>

   </div>
   
</aside>
</div>
<div v-if="$page.props.auth.user.role && $page.props.auth.user.role.toLowerCase() === 'pegawai penyemakan'">
  <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
         <li>
            <a
                :href="route('dashboard')"
                :class="[$page.url === '/dashboard' ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>

               <span class="ms-3">Laman Utama</span>
            </a>
         </li>
         <li>
            <a
                :href="route('status.pemohonan-baru')"
                :class="[$page.url === '/status/pemohonan-baru' ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
              </svg>

               <span class="ms-3">Pemohonan Baru</span>
            </a>
         </li>
        
         
         <li>
            <a
                :href="route('status.perbaharui-lesen')"
                :class="[$page.url === '/status/perbaharui-lesen' ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
              </svg>
               <span class="ms-3">Perbaharui Lesen</span>
            </a>
         </li>
         <li>
            <a
                :href="route('status.index')"
                :class="[$page.url.startsWith('/status') ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"> <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
              </svg>

               <span class="flex-1 ms-3 whitespace-nowrap">Senarai Tindakan</span>
            </a>
         </li>
         <!-- <li>
            <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
              </svg>

               <span class="flex-1 ms-3 whitespace-nowrap">Laporan</span>
            </a>
         </li> -->
         
         
      </ul>

   </div>
   
</aside>
</div>
<div v-if="$page.props.auth.user.role && $page.props.auth.user.role.toLowerCase() === 'pegawai perlulusan'">
  <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
        <li>
            <a
                :href="route('dashboard')"
                :class="[$page.url === '/dashboard' ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>

               <span class="ms-3">Laman Utama</span>
            </a>
         </li>
         <li>
            <a
                :href="route('status.index')"
                :class="[$page.url.startsWith('/status') ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"> <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
              </svg>

               <span class="ms-3">Senarai Tindakan</span>
            </a>
         </li>
         <li>
            <a
                :href="route('status.perbaharui-lesen-telah-disemak')"
                :class="[$page.url.startsWith('/status') ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
              </svg>
               <span class="ms-3">Perbaharui Lesen</span>
            </a>
         </li>
         <!-- <li>
            <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
              </svg>

               <span class="flex-1 ms-3 whitespace-nowrap">Laporan</span>
            </a>
         </li> -->
      </ul>

   </div>
   
</aside>
</div>
<div v-if="$page.props.auth.user.role && $page.props.auth.user.role.toLowerCase() === 'kerani'">
  <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
        <li>
            <a
                :href="route('dashboard')"
                :class="[$page.url === '/dashboard' ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>

               <span class="ms-3">Laman Utama</span>
            </a>
         </li>
         <li>
            <a
                :href="route('applicant.index')"
                :class="[$page.url.startsWith('/applicant') ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
              </svg>

               <span class="ms-3">Senarai Pemohonan</span>
            </a>
         </li>
         <!-- New: Perbaharui Lesen -->
          <li>
            <a
                :href="route('renew-license.index')"
                :class="[$page.url.startsWith('/renew-license') ? 'bg-purple-200 font-semibold dark:bg-purple-700' : '', 'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-100 dark:hover:bg-purple-700 group']"
            >
              <!-- Replace old SVG with new SVG icon -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
              </svg>
               <span class="ms-3">Perbaharui Lesen</span>
            </a>
         </li>
      </ul>
   </div>
</aside>
</div>


<div class="p-4 sm:ml-64">
   <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
    <!-- Page Heading -->
    <header v-if="$slots.header" >
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
   </div>
</div>

</template>
