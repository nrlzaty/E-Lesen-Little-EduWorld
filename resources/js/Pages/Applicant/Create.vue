<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  errors: Object,
  user: Object,
});

const permission = ref(['kerani', 'admin', 'Pegawai Perlulusan','Pegawai Penyemakan']);
const form = useForm({
  nama: props.user.name,
  kad_pengenalan: null,
  warganegara: null,
  alamat_rumah: null,
  alamat_berlainan: null,
  email: props.user.email,
  telefon_r: null,
  telefon_b: null,
  telefon_p: null,
  faks: null,
  user_id: props.user.id,
});

const formVisible = ref(true);
const saved = ref(false);

const toggleFormVisibility = () => {
  formVisible.value = !formVisible.value;
};

const OnSave = () => {
  // Validation: Check if required fields are filled
  if (!form.nama || !form.kad_pengenalan || !form.warganegara || !form.alamat_rumah || !form.email) {
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
      form.post(route('applicant.store'), {
        errorBag: 'createApplicant',
        preserveScroll: true,
        onSuccess: (response) => {
          Swal.fire("Permohonan berjaya disimpan!");
          saved.value = true;
          if (response && response.props && response.props.applicant_id) {
            form.id = response.props.applicant_id;
          }
        }
      });
    }
  });
}

const OnNext = () => {
  // Use the correct applicant id for navigation
  const applicantId = form.id || props.user.id;
  window.location.href = route('pengusaha.create', { applicant_id: applicantId });
};

const OnBack = () => {
  // Go back to the applicant list (or previous logical page)
  window.location.href = route('applicant.index');
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
            <h3 class="text-lg font-semibold text-gray-700">Isi Butiran Pemohon</h3>
            <!-- <button @click="toggleFormVisibility" class="focus:outline-none">
              <svg v-if="formVisible" class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
              <svg v-else class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
              </svg>
            </button> -->
          </div>
          <div v-if="formVisible" class="p-6">
            <form @submit.prevent="OnSave" class="max-w-lg mx-auto">
              <div class="flex gap-4 mb-5">
                <!-- Nama -->
                <div class="flex-1">
                  <label for="nama" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(a) Nama</label>
                  <input v-model="form.nama" type="text" id="nama" 
                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                  <div class="text-red-600 text-xs" v-if="errors?.nama">{{ errors.nama }}</div>
                </div>

                <!-- Kad Pengenalan -->
                <div class="flex-1">
                  <label for="kad_pengenalan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(b) No. Kad Pengenalan</label>
                  <input v-model="form.kad_pengenalan" type="text" id="kad_pengenalan" 
                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                  <div class="text-red-600 text-xs" v-if="errors?.kad_pengenalan">{{ errors.kad_pengenalan }}</div>
                </div>
              </div>

              <!-- Warganegara -->
              <div class="mb-5">
                <label for="warganegara" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(c) Warganegara</label>
                <input v-model="form.warganegara" type="text" id="warganegara" 
                  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="text-red-600 text-xs" v-if="errors?.warganegara">{{ errors.warganegara }}</div>
              </div>

              <!-- Alamat Rumah -->
              <div class="mb-5">
                <label for="alamat_rumah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(d) Alamat Rumah</label>        
                <input v-model="form.alamat_rumah" type="text" id="confirm-password" 
                  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="text-red-600 text-xs" v-if="errors?.alamat_rumah">{{ errors.alamat_rumah }}</div>
              </div>

              <div class="mb-5">
                <label for="alamat_rumah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(e) Alamat pos jika berlainan daripada (d) </label>        
                <input v-model="form.alamat_berlainan" type="text" id="alamat_berlainan" 
                  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="text-red-600 text-xs" v-if="errors?.alamat_berlainan">{{ errors.alamat_berlainan }}</div>
                <div class="flex items-center bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-lg px-4 py-2 mb-2 mt-2" role="alert">
                  <svg class="w-4 h-4 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-8 4a1 1 0 100-2 1 1 0 000 2zm.93-7.412a.75.75 0 00-1.86 0l-.7 4.2a.75.75 0 00.74.912h1.78a.75.75 0 00.74-.912l-.7-4.2z"/></svg>
                  Jika tiada, sila tulis <span class="font-semibold mx-1">"Tiada"</span> di ruang kosong.
                </div>
              </div>

              <div class="mb-5">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(f) Emel </label>        
                <input v-model="form.email" type="text" id="email" 
                  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="text-red-600 text-xs" v-if="errors?.email">{{ errors.email }}</div>
              </div>

              <div class="flex gap-4 mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(g) No. Telefon:</label>
                <!-- R (Home Phone) -->
                <div class="flex-1">
                  <label for="telefon_r" class="block text-sm font-medium text-gray-900 dark:text-white">R (Rumah)</label>
                  <input v-model="form.telefon_r" type="text" id="telefon_r"
                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                  <div class="text-red-600 text-xs" v-if="errors?.telefon_r">{{ errors.telefon_r }}</div>
                </div>
                <!-- B (Personal Phone) -->
                <div class="flex-1">
                  <label for="telefon_b" class="block text-sm font-medium text-gray-900 dark:text-white">B (Bimbit)</label>
                  <input v-model="form.telefon_b" type="text" id="telefon_b"
                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                  <div class="text-red-600 text-xs" v-if="errors?.telefon_b">{{ errors.telefon_b }}</div>
                </div>
                <!-- P (Office Phone) -->
                <div class="flex-1">
                  <label for="telefon_p" class="block text-sm font-medium text-gray-900 dark:text-white">P (Pejabat)</label>
                  <input v-model="form.telefon_p" type="text" id="telefon_p"
                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                  <div class="text-red-600 text-xs" v-if="errors?.telefon_p">{{ errors.telefon_p }}</div>
                </div>
              </div>
              <div class="flex items-center bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-lg px-4 py-2 mb-2 mt-2" role="alert">
                <svg class="w-4 h-4 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-8 4a1 1 0 100-2 1 1 0 000 2zm.93-7.412a.75.75 0 00-1.86 0l-.7 4.2a.75.75 0 00.74.912h1.78a.75.75 0 00.74-.912l-.7-4.2z"/></svg>
                Jika tiada, sila tulis <span class="font-semibold mx-1">"Tiada"</span> di ruang kosong.
              </div>
              <div class="mb-5">
                <label for="faks" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(h) No. Faks </label>        
                <input v-model="form.faks" type="faks" id="faks" 
                  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="text-red-600 text-xs" v-if="errors?.faks">{{ errors.faks }}</div>
                <div class="flex items-center bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-lg px-4 py-2 mb-2 mt-2" role="alert">
                  <svg class="w-4 h-4 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-8 4a1 1 0 100-2 1 1 0 000 2zm.93-7.412a.75.75 0 00-1.86 0l-.7 4.2a.75.75 0 00.74.912h1.78a.75.75 0 00.74-.912l-.7-4.2z"/></svg>
                  Jika tiada, sila tulis <span class="font-semibold mx-1">"Tiada"</span> di ruang kosong.
                </div>
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
    </div>
  </AppLayout>
</template>