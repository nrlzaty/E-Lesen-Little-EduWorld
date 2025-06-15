<script setup>
import { ref } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from 'sweetalert2';

const { props } = usePage();
const templateDocuments = [
  { name: "MAKLUMAT KANAK-KANAK", file: "/templates/MAKLUMAT KANAK-KANAK.docx", field: "maklumat_kanak" },
  { name: "NISBAH KANAK-KANAK DENGAN PENGASUH", file: "/templates/NISBAH KANAK-KANAK DENGAN PENGASUH.docx", field: "nisbah_kanak" },
  { name: "SENARAI MAKLUMAT PEKERJA", file: "/templates/SENARAI MAKLUMAT PEKERJA.docx", field: "senarai_pekerja" },
];

const form = useForm({
  applicant_id: props.applicant.id,
  salinan_ic: null,
  maklumat_kanak: null,
  nisbah_kanak: null,
  senarai_pekerja: null,
});
const errors = ref({});

function handleFileUpload(event, field) {
  form[field] = event.target.files[0];
}

function clearFile(field) {
  form[field] = null;
}

function uploadDocument() {
  // Check all required files
  if (
    !form.salinan_ic ||
    !form.maklumat_kanak ||
    !form.nisbah_kanak ||
    !form.senarai_pekerja
  ) {
    Swal.fire({
      icon: 'warning',
      title: 'Sila muat naik semua dokumen!',
      text: 'Semua dokumen wajib dimuat naik sebelum menghantar.',
    });
    return;
  }
  form.post(route('document.store'), {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Permohonan Selesai!',
        text: 'Permohonan anda telah lengkap dan akan mula dinilai (Dalam Semakan).',
      }).then(() => {
        window.location.reload();
      });
    },
    onError: (err) => {
      errors.value = err;
    },
  });
}
</script>

<template>
  <AppLayout title="Tambah Pemohonan Baru">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Tambah Pemohonan Baru
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
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Tambah Pemohonan Baru</span>
            </div>
          </li>
        </ol>
      </nav>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
          <!-- Section Header -->
          <div class="px-6 py-4 bg-gray-100 border-b">
            <h3 class="text-lg font-semibold text-gray-700">Muat Naik Dokumen Sokongan</h3>
            <p class="text-sm text-gray-500 mt-1">
              Sila muat turun template dokumen, lengkapkan, dan muat naik dokumen yang telah diisi (format PDF).
            </p>
          </div>
        
          <!-- Upload section -->
          <form @submit.prevent="uploadDocument" class="p-6 space-y-6">
            <!-- Salinan IC Pemohon -->
            <div class="border rounded-lg p-4 bg-gray-50">
              <label class="block mb-2 text-sm font-medium text-gray-900">Salinan IC Pemohon (PDF)</label>
              <div class="relative flex items-center space-x-2">
                <input
                  @change="event => handleFileUpload(event, 'salinan_ic')"
                  type="file"
                  id="salinan_ic"
                  accept="application/pdf"
                  class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50"
                />
                <button v-if="form.salinan_ic" type="button" @click="clearFile('salinan_ic')" class="text-red-600 text-xs">Padam</button>
              </div>
              <p class="mt-2 text-xs text-gray-500">
                Hanya fail PDF dibenarkan. Saiz fail maksimum: 10MB.
              </p>
              <div class="text-red-600 text-xs mt-1" v-if="errors?.salinan_ic">
                {{ errors.salinan_ic }}
              </div>
            </div>
            <!-- Dynamic document upload blocks -->
            <div
              v-for="doc in templateDocuments"
              :key="doc.field"
              class="border rounded-lg p-4 bg-gray-50"
            >
              <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-medium text-gray-900">
                  {{ doc.name }} (PDF)
                </label>
                <a :href="doc.file" download class="text-xs text-blue-600 hover:underline">Contoh Template</a>
              </div>
              <div class="relative flex items-center space-x-2">
                <input
                  @change="event => handleFileUpload(event, doc.field)"
                  type="file"
                  :id="doc.field"
                  accept="application/pdf"
                  class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50"
                />
                <button v-if="form[doc.field]" type="button" @click="clearFile(doc.field)" class="text-red-600 text-xs">Padam</button>
              </div>
              <p class="mt-2 text-xs text-gray-500">
                Hanya fail PDF dibenarkan. Saiz fail maksimum: 10MB.
              </p>
              <div class="text-red-600 text-xs mt-1" v-if="errors?.[doc.field]">
                {{ errors[doc.field] }}
              </div>
            </div>
            <div class="flex justify-end space-x-4">
              <button
                type="submit"
                class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-blue-700 dark:focus:ring-purple-800"
              >
                Muat Naik
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>