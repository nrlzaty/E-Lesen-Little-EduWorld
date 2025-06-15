<script setup>
import { ref, nextTick, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import AppLayout from '@/Layouts/AppLayout.vue';

// Use defineProps to get documents and applicant directly from backend
const props = defineProps({
  documents: Array,
  applicant: Object,
  auth: Object,
});

const documents = ref(props.documents || []);
const form = useForm({
  id: null,
  nama_dokumen: '',
  file_dokument: null,
});
const dropdownOpen = ref(false);

const editDocument = (document) => {
  form.id = document.id;
  form.nama_dokumen = document.nama_dokumen;
  form.file_dokument = null;
  // Focus file input after opening edit
  nextTick(() => {
    const fileInput = document.getElementById('file_dokument_' + document.id);
    if (fileInput) fileInput.value = '';
  });
};

function handleFileChange(event) {
  const file = event.target.files[0];
  if (file && file.size > 10 * 1024 * 1024) {
    alert('Saiz fail melebihi 10MB. Sila pilih fail yang lebih kecil.');
    event.target.value = '';
    return;
  }
  form.file_dokument = file;
}

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

const handleRemove = (documentId) => {
  Swal.fire({
    title: 'Adakah anda pasti?',
    text: 'Fail ini akan dipadam secara kekal!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, padam!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      form.delete(route('dokumen.destroy', documentId), {
        onSuccess: () => {
          documents.value = documents.value.filter(doc => doc.id !== documentId);
          Swal.fire('Dipadam!', 'Fail telah berjaya dipadam.', 'success');
        },
        onError: () => {
          Swal.fire('Ralat!', 'Terdapat masalah semasa memadam fail.', 'error');
        },
      });
    }
  });
};

const handleSubmit = () => {
  if (!form.file_dokument) {
    Swal.fire('Ralat!', 'Sila pilih fail PDF untuk dimuat naik.', 'error');
    return;
  }
  form.submit('post', route('dokumen.update', form.id), {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Berjaya!',
        text: 'Dokumen berjaya dikemaskini.',
      }).then(() => {
        window.location.reload();
      });
    },
    onError: () => {
      Swal.fire('Ralat!', 'Terdapat masalah semasa mengemaskini fail.', 'error');
    },
    forceFormData: true,
  });
};

const canEdit = computed(() => {
  // Only allow Kerani to edit if status is Borang Tidak Lengkap or Perbaharui Lesen
  const user = props.auth?.user;
  const applicant = props.applicant;
  if (!user || user.role.toLowerCase() !== 'kerani') return true;
  return applicant && (
    applicant.status === 'Borang Tidak Lengkap' ||
    applicant.status === 'Perbaharui Lesen'
  );
});

const isKeraniRenew = computed(() => {
  return props.auth?.user?.role?.toLowerCase() === 'kerani' && props.applicant.status === 'Perbaharui Lesen';
});
const isKeraniNewApp = computed(() => {
  return props.auth?.user?.role?.toLowerCase() === 'kerani' && props.applicant.status === 'Borang Tidak Lengkap';
});
const isUnderReview = computed(() => {
  return props.applicant.status === 'Dalam Semakan' || props.applicant.status === 'Dalam Proses Semakan';
});

const completeRenewal = () => {
  Swal.fire({
    title: "Sahkan Selesai Perbaharui Lesen?",
    html: "Adakah anda sudah mengemaskini semua maklumat? <br><b>Jika belum, sila gunakan 'Pilih Halaman' untuk kemaskini.</b><br><br><span class='text-red-600'>Jika anda keluar dari halaman tanpa klik 'Kemaskini', perubahan tidak akan disimpan.</span>",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, Selesai & Hantar",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      // No form.put here, just send to complete endpoint
      axios.post(route('renew.complete', props.applicant.id))
        .then(() => {
          Swal.fire("Berjaya!", "Permohonan telah dihantar untuk semakan.", "success")
            .then(() => window.location.href = route('applicant.index'));
        });
    }
  });
};

const completeNewApplication = () => {
  Swal.fire({
    title: "Sahkan Simpan Kemaskini?",
    html: "Adakah anda sudah mengemaskini semua maklumat? <br><b>Jika belum, sila gunakan 'Pilih Halaman' untuk kemaskini.</b><br><br><span class='text-red-600'>Jika anda keluar dari halaman tanpa klik 'Kemaskini', perubahan tidak akan disimpan.</span>",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, Simpan & Hantar",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      // No form.put here, just send to complete endpoint
      axios.post(route('renew.complete', props.applicant.id))
        .then(() => {
          Swal.fire("Berjaya!", "Permohonan telah dihantar untuk semakan.", "success")
            .then(() => window.location.href = route('applicant.index'));
        });
    }
  });
};

// Warn Kerani about unsaved changes if they try to leave
let warnUnsaved = false;
watch(form, () => {
  if (isKeraniRenew.value) warnUnsaved = true;
}, { deep: true });

const beforeUnloadHandler = (e) => {
  if (isKeraniRenew.value && warnUnsaved) {
    e.preventDefault();
    e.returnValue = '';
    return '';
  }
};
if (typeof window !== 'undefined') {
  window.addEventListener('beforeunload', beforeUnloadHandler);
  onBeforeUnmount(() => window.removeEventListener('beforeunload', beforeUnloadHandler));
}
</script>

<template>
  <AppLayout title="Kemaskini Pemohonan">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Kemaskini Pemohonan
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
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Kemaskini Pemohonan</span>
            </div>
          </li>
        </ol>
      </nav>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
          <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b">
            <h3 class="text-lg font-semibold text-gray-700">Kemaskini Dokumen Sokongan</h3>
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
                  :href="route('applicant.edit', applicant.id)"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Butiran Pemohon
                </a>
                <a
                  :href="route('pengusaha.edit', applicant.id)"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Butiran Pengusaha
                </a>
                <a
                  :href="route('butirantaska.edit', applicant.id)"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Butiran Taman Asuhan Kanak-Kanak
                </a>
                <a
                  href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Bilangan Kanak-Kanak
                </a>
                <a
                  href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Butiran Bayaran Kanak
                </a>
                <a
                  :href="route('butiranpengurus.edit', applicant.id)"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Butiran Pengurus
                </a>
                <a
                  :href="route('butiranpenyelia.edit', applicant.id)"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Butiran Penyelia
                </a>
                <a
                  :href="route('maklumatpekerja.edit', applicant.id)"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Maklumat Pekerja
                </a>
                <a
                  :href="route('dokumen.edit', applicant.id)"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Dokumen Sokongan
                </a>
              </div>
            </div>
          </div>
          <div class="p-6 space-y-6">
            <div v-if="(isKeraniRenew && !isUnderReview) || (isKeraniNewApp && !isUnderReview)" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
              <b>Perhatian:</b> Sila pastikan anda klik <b>Kemaskini</b> untuk menyimpan perubahan. Jika anda keluar dari halaman atau ke halaman lain tanpa klik <b>Kemaskini</b>, perubahan anda tidak akan disimpan.<br>
              Jika ada maklumat lain perlu dikemaskini, gunakan <b>Pilih Halaman</b> di atas.
            </div>
            <div v-if="isUnderReview" class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
              Permohonan sedang dalam proses semakan. Anda tidak boleh lagi mengemaskini maklumat.
            </div>
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm" :class="{ 'pointer-events-none opacity-60': isUnderReview }">
              <h4 class="text-md font-medium text-gray-700 mb-4">Senarai dan Kemaskini Dokumen</h4>
              <ul class="space-y-4">
                <li
                  v-for="document in documents"
                  :key="document.id"
                  class="border rounded-lg p-4 bg-white shadow-sm"
                >
                  <div class="flex justify-between items-center mb-4">
                    <template v-if="document.file_dokument">
                      <a
                        :href="`/storage/${document.file_dokument}`"
                        target="_blank"
                        class="text-blue-500 hover:underline"
                      >
                        {{ document.nama_dokumen || 'Tanpa Nama Dokumen' }}
                      </a>
                    </template>
                    <template v-else>
                      <span class="font-semibold text-gray-700">{{ document.nama_dokumen || 'Tanpa Nama Dokumen' }}</span>
                    </template>
                    <div class="flex space-x-2">
                      <button
                        @click="editDocument(document)"
                        class="text-sm text-blue-500 hover:underline"
                      >
                        Kemaskini
                      </button>
                      <!-- <button
                        @click="handleRemove(document.id)"
                        class="text-sm text-red-500 hover:underline"
                      >
                        Padam
                      </button> -->
                    </div>
                  </div>
                  <div
                    v-if="!document.file_dokument && (isKeraniRenew || isKeraniNewApp)"
                    class="bg-red-50 border border-red-200 text-red-600 rounded px-3 py-2 mb-2"
                  >
                    Dokumen tidak dijumpai. Nama fail: <b>{{ document.nama_dokumen || 'Tanpa Nama Dokumen' }}</b>
                  </div>
                  <div v-if="form.id === document.id" class="mt-4">
                    <form @submit.prevent="handleSubmit" class="space-y-4">
                      <div>
                        <label
                          :for="'file_dokument_' + document.id"
                          class="block text-sm font-medium text-gray-700"
                        >
                          Fail Dokumen
                        </label>
                        <input
                          @change="handleFileChange"
                          type="file"
                          :id="'file_dokument_' + document.id"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                        <p class="mt-2 text-xs text-gray-500">
                          Hanya fail PDF dibenarkan. Saiz fail maksimum: 10MB.
                        </p>
                      </div>
                      <div class="flex justify-end">
                        <button
                          type="submit"
                          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300"
                        >
                          Simpan
                        </button>
                      </div>
                    </form>
                  </div>
                </li>
              </ul>
              <div v-if="documents.length === 0" class="text-gray-500 text-center py-8">
                Tiada dokumen untuk dikemaskini.
              </div>
              <div class="flex items-center mt-6">
                <button
                  v-if="isKeraniRenew && !isUnderReview"
                  type="button"
                  class="ml-auto text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                  @click="completeRenewal"
                >
                  Selesai Perbaharui Lesen
                </button>
                <button
                  v-if="isKeraniNewApp && !isUnderReview"
                  type="button"
                  class="ml-auto text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                  @click="completeNewApplication"
                >
                  Simpan Kemaskini
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>