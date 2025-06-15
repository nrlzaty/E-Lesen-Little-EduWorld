<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


const props = defineProps({
  errors:Object,
  jenis_pengusaha:Object,
  applicant: Object,
  butiranPengusaha: Object,


});

const permission = ref(['user','admin','officer']);
const form = useForm({
  applicant_id: props.applicant.id,
  nama_pengusaha: props.butiranPengusaha?.nama_pengusaha,
  kad_pengenalan_pengusaha: props.butiranPengusaha?.kad_pengenalan_pengusaha,
  warganegara_pengusaha: props.butiranPengusaha?.warganegara_pengusaha,
  alamat_rumah_pengusaha: props.butiranPengusaha?.alamat_rumah_pengusaha,
  alamat_berlainan_pengusaha: props.butiranPengusaha?.alamat_berlainan_pengusaha,
  email_pengusaha: props.butiranPengusaha?.email_pengusaha,
  telefon_pengusaha: props.butiranPengusaha?.telefon_pengusaha,
  faks_pengusaha: props.butiranPengusaha?.faks_pengusaha,
  jenis_pengusaha:props.butiranPengusaha?.jenis_pengusaha,

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
  if (!form.nama_pengusaha || !form.kad_pengenalan_pengusaha || !form.warganegara_pengusaha || !form.alamat_rumah_pengusaha || !form.alamat_berlainan_pengusaha || !form.alamat_berlainan_pengusaha || !form.email_pengusaha || !form.telefon_pengusaha || !form.faks_pengusaha || !form.jenis_pengusaha) {
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
      form.post(route('pengusaha.store'), {
        errorBag: 'createPengusaha',
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire("Tambah Pengusaha Baru Berjaya!");
          saved.value = true;
        },
      });
    }
  });
}

const OnNext = () => {
  window.location.href = route('butirantaska.create', { applicant_id: props.applicant.id });
};

const goBack = () => {
  // Go back to applicant create page so kerani can see/edit applicant data
  window.location.href = route('applicant.create');
};
const OnSkip = () => {
  // Navigate to the next page without saving
  window.location.href = route('butirantaska.create',{ applicant_id: props.applicant.id });
};
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
                    <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b">
                        <h3 class="text-lg font-semibold text-gray-700">Isi Butiran Pengusaha</h3>
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
                          <label for="nama_pengusaha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(a) Nama</label>
                          <input v-model="form.nama_pengusaha" type="text" id="nama_pengusaha" 
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                          <div class="text-red-600 text-xs" v-if="errors?.nama_pengusaha">{{ errors.nama_pengusaha }}</div>
                        </div>

                        <!-- Kad Pengenalan -->
                        <div class="flex-1">
                          <label for="kad_pengenalan_pengusaha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(b) No. Kad Pengenalan</label>
                          <input v-model="form.kad_pengenalan_pengusaha" type="kad_pengenalan_pengusaha" id="kad_pengenalan_pengusaha" 
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                          <div class="text-red-600 text-xs" v-if="errors?.kad_pengenalan_pengusaha">{{ errors.kad_pengenalan_pengusaha }}</div>
                        </div>
                      </div>

                      <!-- Warganegara -->
                      <div class="mb-5">
                        <label for="warganegara_pengusaha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(c) Warganegara</label>
                        <input v-model="form.warganegara_pengusaha" type="text" id="warganegara" 
                          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        <div class="text-red-600 text-xs" v-if="errors?.warganegara_pengusaha">{{ errors.warganegara_pengusaha }}</div>
                      </div>

                      <!-- Alamat Rumah -->
                      <div class="mb-5">
                        <label for="alamat_rumah_pengusaha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(d) Alamat Rumah</label>        
                        <input v-model="form.alamat_rumah_pengusaha" type="text" id="confirm-password" 
                          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        <div class="text-red-600 text-xs" v-if="errors?.alamat_rumah_pengusaha">{{ errors.alamat_rumah_pengusaha }}</div>
                      </div>

                      <div class="mb-5">
                        <label for="alamat_berlainan_pengusaha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(e) Alamat pos jika berlainan daripada (d) </label>        
                        <input v-model="form.alamat_berlainan_pengusaha" type="text" id="alamat_berlainan_pengusaha" 
                          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        <div class="text-red-600 text-xs" v-if="errors?.alamat_berlainan_pengusaha">{{ errors.alamat_berlainan_pengusaha }}</div>
                      </div>

                      <div class="mb-5">
                        <label for="email_pengusaha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(f) Emel </label>        
                        <input v-model="form.email_pengusaha" type="text" id="email_pengusaha" 
                          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        <div class="text-red-600 text-xs" v-if="errors?.email_pengusaha">{{ errors.email_pengusaha }}</div>
                      </div>

                      <div class="flex gap-4 mb-5">
                      <label for="telefon_pengusaha"  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(g) No. Telefon:</label>
                      <!-- R (Home Phone) -->
                      <div class="flex-1">
                        
                        <input v-model="form.telefon_pengusaha" type="text" id="telefon_pengusaha"
                          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        <div class="text-red-600 text-xs" v-if="errors?.telefon_pengusaha">{{ errors.telefon_pengusaha }}</div>
                      </div>
                      </div>
                      <div class="mb-5">
                        <label for="faks_pengusaha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(h) No. Faks </label>        
                        <input v-model="form.faks_pengusaha" type="faks" id="faks_pengusaha" 
                          class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        <div class="text-red-600 text-xs" v-if="errors?.faks_pengusaha">{{ errors.faks_pengusaha }}</div>
                      </div>
                      <div class="flex items-center bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-lg px-4 py-2 mb-2 mt-2" role="alert">
                        <svg class="w-4 h-4 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-8 4a1 1 0 100-2 1 1 0 000 2zm.93-7.412a.75.75 0 00-1.86 0l-.7 4.2a.75.75 0 00.74.912h1.78a.75.75 0 00.74-.912l-.7-4.2z"/></svg>
                        Jika tiada, sila tulis <span class="font-semibold mx-1">"Tiada"</span> di ruang kosong.
                      </div>

                      <!--buat dropdwon -->
                      <div class="mb-5">
                        <label for="jenis_pengusaha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Pengusaha</label>
                          <select id="jenis_pengusaha" v-model="form.jenis_pengusaha" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            
                            <template v-for="jenisPengusaha in props.jenis_pengusaha "  >
                              <option :value="jenisPengusaha.keterangan">{{ jenisPengusaha.keterangan }}</option>
                            </template>
                            
                          
                          </select>          
                        <div class="text-red-600 text-xs" v-if="errors?.jenis_pengusaha">{{ errors.jenis_pengusaha }}</div>
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