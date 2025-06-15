<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, onBeforeUnmount, watch } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


const props = defineProps({
  errors:Object,
  applicant: Object,
  jenis_pengalaman_penyelia: Array,
  butiranPenyelia:Object,
  auth: Object, // <-- add this line
});

const permission = ref(['kerani','admin','Pegawai Perlulusan','Pegawai Penyemakan']);
const form = useForm({

  applicant_id: props.applicant.id,
  nama_penyelia:props.butiranPenyelia?.nama_penyelia,
  kad_pengenalan_penyelia:props.butiranPenyelia?.kad_pengenalan_penyelia,
  umur_penyelia:props.butiranPenyelia?.umur_penyelia,
  kelulusan_akademik_penyelia:props.butiranPenyelia?.kelulusan_akademik_penyelia,
  jenis_pengalaman_penyelia:props.butiranPenyelia?.pengalaman_penyelia?.map(value=>value.id)??[],
  
   
});
// Dropdown state
const dropdownOpen = ref(false);

// Toggle dropdown visibility
const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};
const OnSubmit = () => {
  
 

  Swal.fire({
    title: "Adakah anda ingin menyimpan perubahan?",
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: "Simpan",
    denyButtonText: "Jangan simpan",
  }).then((result) => {
    if (result.isConfirmed) {
      // If confirmed, submit the form
      form.put(route('butiranpenyelia.update', props.butiranPenyelia.id), {
        errorBag: 'updatePenyelia',
        preserveScroll: true,
      });

      // Show success message
      Swal.fire("Disimpan!", "", "success");
    } else if (result.isDenied) {
      // If denied, show message that changes are not saved
      Swal.fire("Perubahan tidak disimpan", "", "info");
    }
  });
};

const isKeraniRenew = computed(() => {
  return props.auth?.user?.role?.toLowerCase() === 'kerani' && props.applicant.status === 'Perbaharui Lesen';
});
const isUnderReview = computed(() => {
  return props.applicant.status === 'Dalam Semakan' || props.applicant.status === 'Dalam Proses Semakan';
});
const isKeraniNewApp = computed(() => {
  return props.auth?.user?.role?.toLowerCase() === 'kerani' && props.applicant.status === 'Borang Tidak Lengkap';
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
      form.put(route('butiranpenyelia.update', props.butiranPenyelia.id), {
        preserveScroll: true,
        onSuccess: () => {
          axios.post(route('renew.complete', props.applicant.id))
            .then(() => {
              Swal.fire("Berjaya!", "Permohonan telah dihantar untuk semakan.", "success")
                .then(() => window.location.href = route('applicant.index'));
            });
        }
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
      form.put(route('butiranpenyelia.update', props.butiranPenyelia.id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire("Berjaya!", "Permohonan telah dihantar untuk semakan.", "success")
            .then(() => window.location.href = route('applicant.index'));
        }
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
  <AppLayout title="Tambah Penyelia Baru">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kemaskini Pemohonan</h2>
      <br />
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
                    <!-- Section Header -->
                    <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b">
                        <h3 class="text-lg font-semibold text-gray-700">Kemaskini Butiran Penyelia</h3>
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
                    <div v-if="(isKeraniRenew && !isUnderReview) || (isKeraniNewApp && !isUnderReview)" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                        <b>Perhatian:</b> Sila pastikan anda klik <b>Kemaskini</b> untuk menyimpan perubahan. Jika anda keluar dari halaman atau ke halaman lain tanpa klik <b>Kemaskini</b>, perubahan anda tidak akan disimpan.<br>
                        Jika ada maklumat lain perlu dikemaskini, gunakan <b>Pilih Halaman</b> di atas.
                    </div>
                    <div v-if="isUnderReview" class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
                        Permohonan sedang dalam proses semakan. Anda tidak boleh lagi mengemaskini maklumat.
                    </div>
    <form @submit.prevent="OnSubmit" class="max-w-lg mx-auto py-10" :class="{ 'pointer-events-none opacity-60': isUnderReview }">
      
      <div class="flex gap-4 mb-5">
        <!-- Nama -->
        <div class="flex-1">
          <label for="nama_penyelia" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(a) Nama</label>
          <input v-model="form.nama_penyelia" type="text" id="nama_penyelia" 
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
          <div class="text-red-600 text-xs" v-if="errors?.nama_penyelia">{{ errors.nama_penyelia }}</div>
        </div>

        <!-- Kad Pengenalan -->
        <div class="flex-1">
          <label for="kad_pengenalan_penyelia" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(b) No. Kad Pengenalan</label>
          <input v-model="form.kad_pengenalan_penyelia" type="text" id="kad_pengenalan_penyelia" 
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
          <div class="text-red-600 text-xs" v-if="errors?.kad_pengenalan_penyelia">{{ errors.kad_pengenalan_penyelia }}</div>
        </div>
      </div>

      <!-- Umur -->
      <div class="mb-5">
        <label for="umur_penyelia" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(c) Umur</label>
        <input v-model="form.umur_penyelia" type="text" id="umur_penyelia" 
          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        <div class="text-red-600 text-xs" v-if="errors?.umur_penyelia">{{ errors.umur_penyelia }}</div>
      </div>

      <!-- Kelulusan Akedmik penyelia -->
      <div class="mb-5">
        <label for="kelulusan_akademik_penyelia" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(d)  Kelulusan Akademik tertinggi</label>        
        <input v-model="form.kelulusan_akademik_penyelia" type="text" id="confirm-password" 
          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        <div class="text-red-600 text-xs" v-if="errors?.kelulusan_akademik_penyelia">{{ errors.kelulusan_akademik_penyelia }}</div>
      </div>

      <!-- Checkbox: Jenis Pengalaman -->
      <div class="mb-5">
        <label for="jenis_pengalaman_penyelia" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(f) Jenis Pengalaman </label>        
        <div>
            <div v-for="jenis in props.jenis_pengalaman_penyelia" :key="jenis.id" class="flex items-center mb-2">
            <input
                type="checkbox"
                v-model="form.jenis_pengalaman_penyelia"  
                :value="jenis.id"
                :id="'jenis_pengalaman_' + jenis.id"
                class="input-field"
            />
            <label :for="'jenis_pengalaman_' + jenis.id" class="ml-2 text-sm">{{ jenis.keterangan }}</label>
            </div>
        </div>
        <div class="text-red-500 text-sm" v-if="errors?.jenis_pengalaman_penyelia">{{ errors.jenis_pengalaman_penyelia }}</div>
        </div>





      

      <div class="flex items-center">
        <button type="submit" :disabled="isUnderReview" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-blue-700 dark:focus:ring-purple-800">
          Kemaskini
        </button>
        <button
          v-if="isKeraniRenew && !isUnderReview"
          type="button"
          class="ml-20 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
          @click="completeRenewal"
        >
          Selesai Perbaharui Lesen
        </button>
        <button
          v-if="isKeraniNewApp && !isUnderReview"
          type="button"
          class="ml-20 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
          @click="completeNewApplication"
        >
          Simpan Kemaskini
        </button>
      </div>
    </form>
  </div>
</div>
</div>
  </AppLayout>
</template>