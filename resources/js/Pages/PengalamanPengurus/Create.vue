<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

// Props from parent
const props = defineProps({
  errors: Object,
  applicant: Object,
  jenis_pengalaman_pengurus: Array, 
});

// Form state
const form = useForm({
  applicant_id: props.applicant.id,
  nama_pengurus: null,
  kad_pengenalan_pengurus: null,
  umur_pengurus: null,
  kelulusan_akademik_pengurus: null,
  jawatan_hakiki_pengurus: null,
  jenis_pengalaman_pengurus: [], 
});

// Submit Handler
const OnSubmit = () => {
  // Validation: Check if required fields are filled
  if (!form.nama_pengurus || !form.kad_pengenalan_pengurus || !form.umur_pengurus || !form.kelulusan_akademik_pengurus || !form.jawatan_hakiki_pengurus) {
    Swal.fire({
      title: "Sila lengkapkan semua maklumat",
      text: "Pastikan anda telah mengisi semua maklumat dengan betul.",
      icon: "warning",
      confirmButtonText: "OK",
    });
    return; // Prevent form submission
  }

  // Submit the form
  form.post(route('butiranpengurus.store'), {
    errorBag: 'createPengurus',
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire("Berjaya!", "Butiran pengurus telah ditambah.", "success");
    },
  });
};
</script>

<template>
  <AppLayout title="Tambah Butiran Pengurus">
    <!-- Page Header -->
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Butiran Pengurus</h2>
        <!-- Back Button -->
        <a :href="route('applicant.index')">
          <button
            type="button"
            class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2"
          >
            Kembali
          </button>
        </a>
      </div>
    </template>

    <!-- Form Section -->
    <div class="py-8 max-w-7xl mx-auto">
      <div class="bg-white shadow-lg rounded-lg p-6">
        <!-- Form -->
        <form @submit.prevent="OnSubmit">
          

          <!-- Checkbox: Jenis Pengalaman -->
          <div class="mb-5">
            <label for="jenis_pengalaman_pengurus" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(f) Jenis Pengalaman </label>        
            <div>
                <div v-for="jenis in props.jenis_pengalaman_pengurus" :key="jenis.id" class="flex items-center mb-2">
                <input
                    type="checkbox"
                    v-model="form.jenis_pengalaman_pengurus"  
                    :value="jenis.id"
                    :id="'jenis_pengalaman_' + jenis.id"
                    class="input-field"
                />
                <label :for="'jenis_pengalaman_' + jenis.id" class="ml-2 text-sm">{{ jenis.keterangan }}</label>
                </div>
            </div>
            <div class="text-red-500 text-sm" v-if="errors?.jenis_pengalaman_pengurus">{{ errors.jenis_pengalaman_pengurus }}</div>
            </div>


          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-300"
          >
            Tambah Pengurus
          </button>
        </form>
      </div>
    </div>
  </AppLayout>
</template>


