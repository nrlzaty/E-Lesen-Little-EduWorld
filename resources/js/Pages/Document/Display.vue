<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const { props } = usePage();
const documents = ref([]);
const applicant = props.applicant ?? {};

const dropdownOpen = ref(false);
const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

onMounted(() => {
  documents.value = props.documents;
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
          <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b">
            <h3 class="text-lg font-semibold text-gray-700">Dokumen yang dimuat naik</h3>
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
                  Dokumen Sokongan
                </a>
              </div>
            </div>
          </div>
          <div class="p-8">
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
  </AppLayout>
</template>