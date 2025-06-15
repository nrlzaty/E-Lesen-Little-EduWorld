<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


const props=defineProps({
  errors:Object,
  user:Object,
  roles:Object,

});

const permission = ref(['kerani','admin','Pegawai Perlulusan','Pegawai Penyemakan']);
const form = useForm({
    name:props.user.name,
    email:props.user.email,
    role:props.user.role,
    password:null,
    confirm_password:null,
    selected_role:props.roles.find(option=>option.name===props.user.role),
});
const OnSubmit = () => {
  
  form.role = form.selected_role.name;

  Swal.fire({
    title: "Adakah anda ingin menyimpan perubahan?",
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: "Simpan",
    denyButtonText: "Jangan simpan",
  }).then((result) => {
    if (result.isConfirmed) {
      // If confirmed, submit the form
      form.put(route('setting.user.update', props.user.id), {
        errorBag: 'updateUser',
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

</script>

<template>
    <AppLayout title="Kemaskini Pengguna">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kemaskini Pengguna
            </h2>
            <br>
            <!-- Back Button -->
          <a :href="route('setting.user.index')">
            <button type="button" id="createProductModalButton" data-modal-target="createProductModal" data-modal-toggle="createProductModal" class="flex items-center justify-center text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-purple-600 dark:hover:bg-purple-700 focus:outline-none dark:focus:ring-purple-800">
              Kembali
            </button>
          </a>
        </template>


        <form @submit.prevent="OnSubmit" class="max-w-sm mx-auto">
            <div class="mb-5">
              <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Penuh</label>
              <input v-model="form.name" type="text" id="name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder=""  />
              <div class="text-red-600 text-xs" v-if="form.errors.name">{{ form.errors.name }}</div>
            </div>
            <div class="mb-5">
              <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
              <input v-model="form.email" type="email" id="email" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder="name@flowbite.com"  />
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
            
            <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-blue-700 dark:focus:ring-purple-800">Kemaskini</button>
          </form>

    </AppLayout>
</template>
