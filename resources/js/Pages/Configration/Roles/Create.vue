<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineProps({
  errors:Object
});

const permission = ref (['kerani','admin','Pegawai Perlulusan','Pegawai Penyemakan']);
const form = useForm({
    name:null,
    code_name:null,
    is_active:null,

});
const OnSubmit = () => {
  // Check if required fields are completed or checkbox is unchecked
  if (!form.name || !form.code_name || form.is_active === null) {
    // If any field is empty or checkbox is not checked, show an alert
    Swal.fire({
      title: "Sila lengkapkan semua maklumat",
      text: "Pastikan anda telah mengisi semua maklumat dan memeriksa pilihan 'Aktif?'",
      icon: "warning",
      confirmButtonText: "OK"
    });
    return;
  }

  // If all fields are filled, submit the form
  form.post(route('setting.role.store'), {
    errorBag: 'createUser',
    preserveScroll: true,
  });

  Swal.fire("Tambah Peranan Berjaya!");
}   //function 
</script>

<template>
    <AppLayout title="Tambah Peranan">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Peranan
            </h2>
            <br>
            <!-- Back Button -->
          <a :href="route('setting.role.index')"><button type="button" id="createProductModalButton" data-modal-target="createProductModal" data-modal-toggle="createProductModal" class="flex items-center justify-center text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-purple-600 dark:hover:bg-purple-700 focus:outline-none dark:focus:ring-purple-800">
                        <!-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg> -->
                        Kembali
                    </button>
                    </a>
        </template>


        <form @submit.prevent="OnSubmit" class="max-w-sm mx-auto">
            <div class="mb-5">
              <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Peranan</label>
              <input v-model="form.name" type="text" id="name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder=""  />
              <div class="text-red-600 text-xs" v-if="form.errors.name">{{ form.errors.name }}</div>
            </div>
            <div class="mb-5">
              <label for="code_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kod Peranan</label>
              <input v-model="form.code_name" type="text" id="code_name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder=""  />
              <div class="text-red-600 text-xs" v-if="form.errors.code_name">{{ form.errors.code_name }}</div>
            </div>
            
            <div class="flex items-center mb-4">
                <input id="default-radio-1" type="checkbox" value="1" v-model="form.is_active" name="default-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="default-radio-1" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Aktif?</label>
            </div>
           

            
            <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-blue-700 dark:focus:ring-purple-800">Tambah Peranan</button>
          </form>

    </AppLayout>
</template>
