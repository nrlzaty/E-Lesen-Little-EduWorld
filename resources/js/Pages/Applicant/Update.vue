<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, onBeforeUnmount, watch } from 'vue';
import Multiselect from 'vue-multiselect';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


const props=defineProps({
  errors:Object,
  applicant:Object,
  auth:Object, // <-- add this if not present
});

const permission = ref(['kerani','admin','Pegawai Perlulusan','Pegawai Penyemakan']);
const form = useForm({
    nama:props.applicant.nama,
    kad_pengenalan:props.applicant.kad_pengenalan,
    warganegara:props.applicant.warganegara,
    alamat_rumah:props.applicant.alamat_rumah,
    alamat_berlainan:props.applicant.alamat_berlainan,
    email:props.applicant.email,
    telefon_r:props.applicant.telefon_r,
    telefon_b:props.applicant.telefon_b,
    telefon_p:props.applicant.telefon_p,
    faks:props.applicant.faks,

    
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
      form.put(route('applicant.update', props.applicant.id), {
        errorBag: 'updateApplicant',
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

// New: Disable editing if under review
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
      form.put(route('applicant.update', props.applicant.id), {
        preserveScroll: true,
        onSuccess: () => {
          // Send a separate request to update status only
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
      form.put(route('applicant.update', props.applicant.id), {
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
                <div class="bg-white overflow-hidden sm:rounded-lg shadow-lg">
                    
                    <!-- Section Header -->
                    <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b">
                        <h3 class="text-lg font-semibold text-gray-700">Kemaskini Butiran Pemohon</h3>
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
                    <!-- Alert for Kerani in renew mode -->
                    <div v-if="(isKeraniRenew && !isUnderReview) || (isKeraniNewApp && !isUnderReview)" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                        <b>Perhatian:</b> Sila pastikan anda klik <b>Kemaskini</b> untuk menyimpan perubahan. Jika anda keluar dari halaman atau ke halaman lain tanpa klik <b>Kemaskini</b>, perubahan anda tidak akan disimpan.<br>
                        Jika ada maklumat lain perlu dikemaskini, gunakan <b>Pilih Halaman</b> di atas.
                    </div>
                    <!-- Alert if under review -->
                    <div v-if="isUnderReview" class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
                        Permohonan sedang dalam proses semakan. Anda tidak boleh lagi mengemaskini maklumat.
                    </div>
                    <form @submit.prevent="OnSubmit" class="max-w-sm mx-auto py-10" :class="{ 'pointer-events-none opacity-60': isUnderReview }">
        
                  <div class="mb-5">
                    <label for="nama" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(a) Nama</label>
                    <input v-model="form.nama" type="text" id="nama" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder=""  />
                    <div class="text-red-600 text-xs" v-if="form.errors.nama">{{ form.errors.nama }}</div>
                  </div>
                  <div class="mb-5">
                    <label for="kad_pengenalan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(b) No. Kad Pengenalan</label>
                    <input v-model="form.kad_pengenalan" type="kad_pengenalan" id="kad_pengenalan" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder="name@flowbite.com"  />
                    <div class="text-red-600 text-xs" v-if="form.errors.kad_pengenalan">{{ form.errors.kad_pengenalan }}</div>
                  </div>
                  
                  <div class="mb-5">
                    <label for="warganegara" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(c) Warganegara</label>
                    <input v-model="form.warganegara" type="warganegara" id="warganegara" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
                    <div class="text-red-600 text-xs" v-if="form.errors.warganegara">{{ form.errors.warganegara }}</div>
                  </div>
                  <div class="mb-5">
                    <label for="alamat_rumah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(d) Alamat Rumah</label>
                    <input v-model="form.alamat_rumah" type="alamat_rumah" id="alamat_rumah" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
                    <div class="text-red-600 text-xs" v-if="form.errors.alamat_rumah">{{ form.errors.alamat_rumah }}</div>
                  </div>
                  <div class="mb-5">
                    <label for="alamat_berlainan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(e) Alamat pos jika berlainan daripada (d)</label>
                    <input v-model="form.alamat_berlainan" type="alamat_berlainan" id="alamat_berlainan" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
                    <div class="text-red-600 text-xs" v-if="form.errors.alamat_berlainan">{{ form.errors.alamat_berlainan }}</div>
                  </div>
                  <div class="mb-5">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(f) Emel</label>
                    <input v-model="form.email" type="email" id="email" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
                    <div class="text-red-600 text-xs" v-if="form.errors.email">{{ form.errors.email }}</div>
                  </div>
                  <div class="flex gap-4 mb-5">
                  <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(g) No. Telefon:</label>

                  <div class="mb-5">
                    <label for="telefon_r" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">R (Rumah)</label>
                    <input v-model="form.telefon_r" type="telefon_r" id="telefon_r" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
                    <div class="text-red-600 text-xs" v-if="form.errors.telefon_r">{{ form.errors.telefon_r }}</div>
                  </div>
                  <div class="mb-5">
                    <label for="telefon_b" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">B (Bimbit)</label>
                    <input v-model="form.telefon_b" type="telefon_b" id="telefon_b" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
                    <div class="text-red-600 text-xs" v-if="form.errors.telefon_b">{{ form.errors.telefon_b }}</div>
                  </div>
                  <div class="mb-5">
                    <label for="telefon_p" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">P (Pejabat)</label>
                    <input v-model="form.telefon_p" type="telefon_p" id="telefon_p" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
                    <div class="text-red-600 text-xs" v-if="form.errors.telefon_p">{{ form.errors.telefon_p }}</div>
                  </div>
                  </div>
                  <div class="mb-5">
                    <label for="faks" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">(h) No. Faks</label>
                    <input v-model="form.faks" type="faks" id="faks" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"  />
                    <div class="text-red-600 text-xs" v-if="form.errors.faks">{{ form.errors.faks }}</div>
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
              <!-- </div> -->


        

    </AppLayout>
</template>
