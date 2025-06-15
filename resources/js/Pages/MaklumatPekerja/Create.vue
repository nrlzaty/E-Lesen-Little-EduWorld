<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


const props = defineProps({
  errors:Object,
  jantina:Object,
  applicant: Object,
  maklumatPekerja: Object,
});

const permission = ref(['user','admin','officer']);
const form = useForm({
  applicant_id: props.applicant.id,
  workers: [
    {
      nama_pekerja: props.maklumatPekerja?.nama_pekerja || '',
      kad_pengenalan_pekerja: props.maklumatPekerja?.kad_pengenalan_pekerja || '',
      umur_pekerja: props.maklumatPekerja?.umur_pekerja || '',
      jantina_pekerja: props.maklumatPekerja?.jantina_pekerja || '',
      kelayakan_pekerja: props.maklumatPekerja?.kelayakan_pekerja || '',
      jawatan_pekerja: props.maklumatPekerja?.jawatan_pekerja || '',
      tarikh_mula_pekerja: props.maklumatPekerja?.tarikh_mula_pekerja || '',
    }
  ]
});

const addWorker = () => {
  form.workers.push({
    nama_pekerja: '',
    kad_pengenalan_pekerja: '',
    umur_pekerja: '',
    jantina_pekerja: '',
    kelayakan_pekerja: '',
    jawatan_pekerja: '',
    tarikh_mula_pekerja: '',
  });
};

// Dropdown state
const dropdownOpen = ref(false);

// Toggle dropdown visibility
const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};
const OnSubmit = () => {
  // Validation: Check if required fields are filled for each worker
  for (const worker of form.workers) {
    if (!worker.nama_pekerja || !worker.kad_pengenalan_pekerja || !worker.umur_pekerja || !worker.jantina_pekerja || !worker.kelayakan_pekerja || !worker.jawatan_pekerja || !worker.tarikh_mula_pekerja) {
      Swal.fire({
        title: "Sila lengkapkan semua maklumat",
        text: "Pastikan anda telah mengisi semua maklumat dan kata laluan sepadan.",
        icon: "warning",
        confirmButtonText: "OK"
      });
      return; // Prevent form submission
    }
  }

  // If validation passes, submit the form
  form.post(route('maklumatpekerja.store'), {
    errorBag: 'createPekerja',
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire("Tambah Maklumat Pekerja Berjaya!");
      window.location.href = route('dokumen.create', { applicant_id: props.applicant.id });
    }
  });
};
const goBack = () => {
  window.history.back();
};
const OnSkip = () => {
  // Navigate to the next page without saving
  window.location.href = route('dokumen.create', { applicant_id: props.applicant.id });
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
            <h3 class="text-lg font-semibold text-gray-700">Isi Maklumat Pekerja</h3>
          </div>
          <form @submit.prevent="OnSubmit" class="max-w-lg mx-auto py-10">
            <div v-for="(worker, index) in form.workers" :key="index" class="mb-5">
              <h4 class="text-lg font-semibold text-gray-700 mb-4">Pekerja {{ index + 1 }}</h4>
              <div class="flex gap-4 mb-5">
                <!-- Nama -->
                <div class="flex-1">
                  <label :for="'nama_pekerja_' + index" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(a) Nama</label>
                  <input v-model="worker.nama_pekerja" :id="'nama_pekerja_' + index" type="text" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                  <div class="text-red-600 text-xs" v-if="errors?.workers?.[index]?.nama_pekerja">{{ errors.workers[index].nama_pekerja }}</div>
                </div>

                <!-- Kad Pengenalan -->
                <div class="flex-1">
                  <label :for="'kad_pengenalan_pekerja_' + index" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(b) No. Kad Pengenalan</label>
                  <input v-model="worker.kad_pengenalan_pekerja" :id="'kad_pengenalan_pekerja_' + index" type="text" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                  <div class="text-red-600 text-xs" v-if="errors?.workers?.[index]?.kad_pengenalan_pekerja">{{ errors.workers[index].kad_pengenalan_pekerja }}</div>
                </div>
              </div>

              <!-- Umur -->
              <div class="mb-5">
                <label :for="'umur_pekerja_' + index" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(c) Umur</label>
                <input v-model="worker.umur_pekerja" :id="'umur_pekerja_' + index" type="text" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="text-red-600 text-xs" v-if="errors?.workers?.[index]?.umur_pekerja">{{ errors.workers[index].umur_pekerja }}</div>
              </div>

              <!-- Jantina -->
              <div class="mb-5">
                <label :for="'jantina_pekerja_' + index" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(d) Jantina</label>
                <select v-model="worker.jantina_pekerja" :id="'jantina_pekerja_' + index" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                  <template v-for="jantina in props.jantina">
                    <option :value="jantina.keterangan">{{ jantina.keterangan }}</option>
                  </template>
                </select>
                <div class="text-red-600 text-xs" v-if="errors?.workers?.[index]?.jantina_pekerja">{{ errors.workers[index].jantina_pekerja }}</div>
              </div>

              <!-- Kelayakan -->
              <div class="mb-5">
                <label :for="'kelayakan_pekerja_' + index" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(e) Kelayakan</label>
                <input v-model="worker.kelayakan_pekerja" :id="'kelayakan_pekerja_' + index" type="text" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="text-red-600 text-xs" v-if="errors?.workers?.[index]?.kelayakan_pekerja">{{ errors.workers[index].kelayakan_pekerja }}</div>
              </div>

              <!-- Jawatan -->
              <div class="mb-5">
                <label :for="'jawatan_pekerja_' + index" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(f) Jawatan</label>
                <input v-model="worker.jawatan_pekerja" :id="'jawatan_pekerja_' + index" type="text" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="text-red-600 text-xs" v-if="errors?.workers?.[index]?.jawatan_pekerja">{{ errors.workers[index].jawatan_pekerja }}</div>
              </div>

              <!-- Tarikh Mula -->
              <div class="mb-5">
                <label :for="'tarikh_mula_pekerja_' + index" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(g) Tarikh Mula Bekerja</label>
                <input v-model="worker.tarikh_mula_pekerja" :id="'tarikh_mula_pekerja_' + index" type="date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                <div class="text-red-600 text-xs" v-if="errors?.workers?.[index]?.tarikh_mula_pekerja">{{ errors.workers[index].tarikh_mula_pekerja }}</div>
              </div>
            </div>

            <button type="button" @click="addWorker" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
              Tambah Pekerja
            </button>

            <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-blue-700 dark:focus:ring-purple-800">
              Simpan dan Setursnya
            </button>
            <button @click="goBack" type="button" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                        Kembali
                      </button>
                      <button @click="OnSkip" type="button" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                  Skip
                </button>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>