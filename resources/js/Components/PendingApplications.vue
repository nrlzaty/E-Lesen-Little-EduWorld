<template>
  <div>
    <h3>Pending Applications</h3>
    <ul>
      <li v-for="applicant in pendingApplications" :key="applicant.id">
        {{ applicant.nama }}
        <button @click="reviewApplication(applicant.id)">Review</button>
        <button @click="approveRenewal(applicant.id)">Approve</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const pendingApplications = ref([]);

const fetchPendingApplications = async () => {
  const response = await axios.get('/api/pending-applications');
  pendingApplications.value = response.data;
};

const reviewApplication = async (id) => {
  await axios.put(`/applicant/${id}/review`);
  fetchPendingApplications();
};

const approveRenewal = async (id) => {
  await axios.put(`/applicant/${id}/approve`);
  fetchPendingApplications();
};

fetchPendingApplications();
</script>
