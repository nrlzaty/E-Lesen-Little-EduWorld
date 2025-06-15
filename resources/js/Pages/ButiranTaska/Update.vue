<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, onBeforeUnmount, watch } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


const props = defineProps({
  errors: Object,
  applicant: Object,
  butiranTaska: Object,
  auth: Object, // <-- add this line
});

const permission = ref(['kerani','admin','Pegawai Perlulusan','Pegawai Penyemakan']);
const form = useForm({
    applicant_id: props.applicant.id,
    nama_taska: props.butiranTaska?.nama_taska,
    alamat_taska:props.butiranTaska?.alamat_taska,
    telefon_taska_r:props.butiranTaska?.telefon_taska_r,
    telefon_taska_b:props.butiranTaska?.telefon_taska_b,
    telefon_taska_p:props.butiranTaska?.telefon_taska_p,
    faks_taska:props.butiranTaska?.faks_taska,
    email_taska:props.butiranTaska?.email_taska,
    laman_web_taska:props.butiranTaska?.laman_web_taska,

    
});
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
      form.put(route('butirantaska.update', props.butiranTaska.id), {
        errorBag: 'updateTaska',
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

const dropdownOpen = ref(false);

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const isKeraniRenew = computed(() => {
  return props.auth?.user?.role?.toLowerCase() === 'kerani' && props.applicant.status === 'Perbaharui Lesen';
});
const isKeraniNewApp = computed(() => {
  return props.auth?.user?.role?.toLowerCase() === 'kerani' && props.applicant.status === 'Borang Tidak Lengkap';
});
const isUnderReview = computed(() => {
  return props.applicant.status === 'Dalam Semakan' || props.applicant.status === 'Dalam Proses Semakan';
});

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
      form.put(route('butirantaska.update', props.butiranTaska.id), {
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
      form.put(route('butirantaska.update', props.butiranTaska.id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire("Berjaya!", "Permohonan telah dihantar untuk semakan.", "success")
            .then(() => window.location.href = route('applicant.index'));
        }
      });
    }
  });
};
</script>

<template>
    <AppLayout title="Kemaskini Pemohonan ">
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
                    <!-- Section Header -->
                    <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b">
                        <h3 class="text-lg font-semibold text-gray-700">Butiran Taman Asuhan Kanak-kanak</h3>
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
          <label for="nama_taska" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(a) Nama TASKA</label>
          <input v-model="form.nama_taska" type="text" id="nama_taska" 
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
          <div class="text-red-600 text-xs" v-if="errors?.nama_taska">{{ errors.nama_taska }}</div>
        </div>

        <!-- Alamat -->
        <div class="flex-1">
          <label for="alamat_taska" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(b) Alamat </label>
          <input v-model="form.alamat_taska" type="text" id="alamat_taska" 
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
          <div class="text-red-600 text-xs" v-if="errors?.alamat_taska">{{ errors.alamat_taska }}</div>
        </div>
      </div>

      <!-- Telefon -->
    <div class="flex gap-4 mb-5">
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(c) No. Telefon:</label>
        <!-- R (Home Phone) -->
        <div class="flex-1">
            <label for="telefon_taska_r" class="block text-sm font-medium text-gray-900 dark:text-white">R (Rumah)</label>
            <input v-model="form.telefon_taska_r" type="text" id="telefon_taska_r"
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            <div class="text-red-600 text-xs" v-if="errors?.telefon_taska_r">{{ errors.telefon_taska_r }}</div>
        </div>

        <!-- B (Personal Phone) -->
        <div class="flex-1">
            <label for="telefon_taska_b" class="block text-sm font-medium text-gray-900 dark:text-white">B (Bimbit)</label>
            <input v-model="form.telefon_taska_b" type="text" id="telefon_taska_b"
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            <div class="text-red-600 text-xs" v-if="errors?.telefon_taska_b">{{ errors.telefon_taska_b }}</div>
        </div>

        <!-- P (Office Phone) -->
        <div class="flex-1">
            <label for="telefon_taska_p" class="block text-sm font-medium text-gray-900 dark:text-white">P (Pejabat)</label>
            <input v-model="form.telefon_taska_p" type="text" id="telefon_taska_p"
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            <div class="text-red-600 text-xs" v-if="errors?.telefon_taska_p">{{ errors.telefon_taska_p }}</div>
        </div>
    </div>
    <!-- faks -->
    <div class="mb-5">
        <label for="faks_taska" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(d) No. Faks </label>        
        <input v-model="form.faks_taska" type="text" id="faks_taska" 
          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        <div class="text-red-600 text-xs" v-if="errors?.faks_taska">{{ errors.faks_taska }}</div>
      </div>

      <!-- email -->
      <div class="mb-5">
        <label for="email_taska" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(e) Emel </label>
        <input v-model="form.email_taska" type="text" id="email_taska" 
          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        <div class="text-red-600 text-xs" v-if="errors?.email_taska">{{ errors.email_taska }}</div>
      </div>

      <!-- Laman Web Taska -->
      <div class="mb-5">
        <label for="laman_web_taska" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(f) Laman Web</label>        
        <input v-model="form.laman_web_taska" type="text" id="laman_web_taska" 
          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        <div class="text-red-600 text-xs" v-if="errors?.laman_web_taska">{{ errors.laman_web_taska }}</div>
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
