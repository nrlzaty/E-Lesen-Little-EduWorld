<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


const props = defineProps({
  errors:Object,
  applicant: Object,
  jenis_pengalaman_penyelia: Array,
  butiranPenyelia:Object,
});

const permission = ref(['kerani', 'admin', 'Pegawai Perlulusan','Pegawai Penyemakan']);
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
const saved = ref(false);

const OnSubmit = () => {
  // Validation: Check if required fields are filled
  if (!form.nama_penyelia || !form.kad_pengenalan_penyelia || !form.umur_penyelia || !form.kelulusan_akademik_penyelia ) {
    Swal.fire({
      title: "Sila lengkapkan semua maklumat",
      text: "Pastikan anda telah mengisi semua maklumat.",
      icon: "warning",
      confirmButtonText: "OK"
    });
    return; // Prevent form submission
  }

  // Confirmation before saving
  Swal.fire({
    title: "Sahkan Maklumat",
    text: "Pastikan semua maklumat adalah betul. Anda TIDAK BOLEH mengubah maklumat selepas klik Simpan.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Simpan",
    cancelButtonText: "Semak Semula"
  }).then((result) => {
    if (result.isConfirmed) {
      form.post(route('butiranpenyelia.store'), {
        errorBag: 'createPenyelia',
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire("Tambah Penyelia Baru Berjaya!");
          saved.value = true;
        }
      });
    }
  });
}

const OnNext = () => {
  window.location.href = route('maklumatpekerja.create', { applicant_id: props.applicant.id });
};

const goBack = () => {
  // Go back to butiranpengurus edit page to see and edit previous data
  window.location.href = route('butiranpengurus.edit', props.applicant.id);
};
</script>
<template>
  <AppLayout title="Tambah Pemohonan Baru">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pemohonan Baru</h2>
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
                    <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b">
                        <h3 class="text-lg font-semibold text-gray-700">Isi Butiran Penyelia</h3>
                        <!-- <div class="relative">
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
                                    :href="route('pengusaha.create', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Butiran Pengusaha
                                </a>
                               <a
                                   :href="route('butirantaska.create', applicant.id)"
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
                                    :href="route('butiranpengurus.create', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Butiran Pengurus
                                </a>
                                <a
                                    :href="route('butiranpenyelia.create', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Butiran Penyelia
                                </a>
                              
                                <a
                                    :href="route('maklumatpekerja.create', applicant.id)"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Maklumat Pekerja
                                </a>
                                <a
                                    href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Document Sokongan
                                </a>
                            </div>
                      </div> -->

                    </div>
    <form @submit.prevent="OnSubmit" class="max-w-lg mx-auto py-10">
      
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
        <input v-model="form.kelulusan_akademik_penyelia" type="text" id="kelulusan_akademik_penyelia" 
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





      

        <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-blue-700 dark:focus:ring-purple-800">
                Simpan
              </button>
              <button
                v-if="saved"
                type="button"
                @click="OnNext"
                class="ml-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              >
                Seterusnya
              </button>
    </form>
  </div>
</div>
</div>
  </AppLayout>
</template>