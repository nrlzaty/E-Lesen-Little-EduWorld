<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


defineProps({
  errors:Object,
  roles:Object
});

const permission = ref(['kerani','admin','Pegawai Perlulusan','Pegawai Penyemakan']);
const form = useForm({
    name:null,
    email:null,
    role:null,
    password:null,
    confirm_password:null,
    selected_role:null,
});
const OnSubmit = () => {
  // Validation: Check if required fields are filled
  if (!form.name || !form.email || !form.selected_role || !form.password || form.password !== form.confirm_password) {
    Swal.fire({
      title: "Sila lengkapkan semua maklumat",
      text: "Pastikan anda telah mengisi semua maklumat dan kata laluan sepadan.",
      icon: "warning",
      confirmButtonText: "OK"
    });
    return; // Prevent form submission
  }

  // If validation passes, submit the form
  form.role = form.selected_role.name;
  form.post(route('setting.user.store'), {
    errorBag: 'createUser',
    preserveScroll: true,
  });

  Swal.fire("Tambah Pengguna Berjaya!");
}
</script>

<template>
    <AppLayout title="Tambah Pengguna">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Pengguna
            </h2>  
            <br>
            <!-- Breadcrumb Navigation -->
            <nav class="flex" aria-label="Breadcrumb">
              <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                  <a :href="route('dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                      <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Laman Utama
                  </a>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a :href="route('setting.user.index')" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
                      Pengguna
                    </a>
                  </div>
                </li>
                <li aria-current="page">
                  <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">
                      Tambah Pengguna
                    </span>
                  </div>
                </li>
              </ol>
            </nav>
            <br>
           
        </template>
        


        <form @submit.prevent="OnSubmit" class="max-w-sm mx-auto">
            <div class="mb-5">
              <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Penuh</label>
              <input v-model="form.name" type="text" id="name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder=""  />
              <div class="text-red-600 text-xs" v-if="form.errors.name">{{ form.errors.name }}</div>
            </div>
            <div class="mb-5">
              <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
              <input v-model="form.email" type="email" id="email" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder="name@gmail.com"  />
              <div class="text-red-600 text-xs" v-if="form.errors.email">{{ form.errors.email }}</div>
            </div>
            <div class="mb-5">
              <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Peranan</label>
              <Multiselect :options="roles" v-model="form.selected_role" label="name" track-by="id" placeholder="Sila Pilih" :custom-label="(value) =>  `${value.name}`"/>
                <div class="text-red-600 text-xs" v-if="form.errors.role">{{ form.errors.role }}</div>
              <!-- <input type="email" id="email" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder="name@flowbite.com" required /> -->
            </div>
            <div class="mb-5">
              <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kata Laluan</label>
              <input v-model="form.password" type="password" id="password" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
              <div class="text-red-600 text-xs" v-if="form.errors.password">{{ form.errors.password }}</div>
            </div>
            <div class="mb-5">
              <label for="confirm-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ulang Kata Laluan</label>
              <input v-model="form.confirm_password" type="password" id="confirm-password" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
              <div class="text-red-600 text-xs" v-if="form.errors.confirm_password">{{ form.errors.confirm_password }}</div>
            </div>
            
            <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-blue-700 dark:focus:ring-purple-800">Tambah Pengguna</button>
          </form>

    </AppLayout>
</template>
