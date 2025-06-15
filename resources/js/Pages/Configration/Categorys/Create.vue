<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  errors:Object,
  category:String,
});

const permission = ref (['kerani','admin','Pegawai Perlulusan','Pegawai Penyemakan']);
const form = useForm({
    kod:null,
    kategori:props.category==='new'?'':props.category,
    keterangan:null,
    is_aktif:null,
    kod_lain:null,

});
const OnSubmit = () => {
    form.post(route('setting.code.store'), {
        errorBag:'createCode',
        preserveScroll:true,
    })
} //function 
</script>

<template>
    <AppLayout title="Tambah Kategori">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Kategori
            </h2>
        </template>


        <form @submit.prevent="OnSubmit" class="max-w-sm mx-auto">
            <div class="mb-5">
              <label for="kategori" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
              <input v-model="form.kategori" type="text" id="kategori" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder=""  />
              <div class="text-red-600 text-xs" v-if="form.errors.kategori">{{ form.errors.kategori }}</div>
            </div>
            <div class="mb-5">
              <label for="kod" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kod</label>
              <input v-model="form.kod" type="text" id="kod" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder=""  />
              <div class="text-red-600 text-xs" v-if="form.errors.kod">{{ form.errors.kod }}</div>
            </div>
            <div class="mb-5">
              <label for="kod_lain" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kod Lain</label>
              <input v-model="form.kod_lain" type="text" id="kod_lain" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder=""  />
              <div class="text-red-600 text-xs" v-if="form.errors.kod_lain">{{ form.errors.kod_lain }}</div>
            </div>
            <div class="mb-5">
                <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                <textarea v-model="form.keterangan" id="keterangan" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder=""></textarea>
                <div class="text-red-600 text-xs" v-if="form.errors.keterangan">{{ form.errors.keterangan }}</div>
            </div>
            <div class="flex items-center mb-4">
                <input id="default-radio-1" type="checkbox" value="true" v-model="form.is_aktif" name="default-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="default-radio-1" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Aktif?</label>
            </div>
            
            <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-blue-700 dark:focus:ring-purple-800">
                Tambah Kategori</button>
          </form>

    </AppLayout>
</template>
