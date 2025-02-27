<template>
  <div>
    <h1>Liste des Personnes</h1>
    <button v-if="isAdmin" @click="ajouterPersonne" class="btn btn-outline-secondary">Ajouter Personne</button>

    <table class="table">
      <thead class="thead-dark">
      <tr>
        <th>#</th>
        <th>FirstName</th>
        <th>LastName</th>
        <th>Age</th>
        <th>Details</th>
        <th v-if="isAdmin">Delete</th>
        <th v-if="isAdmin">Editer</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="personne in personnes" :key="personne.id">
        <td>{{ personne.id }}</td>
        <td>{{ personne.firstName }}</td>
        <td>{{ personne.name }}</td>
        <td>{{ personne.age }}</td>
        <td>
          <a :href="'/personne/' + personne.id"><i class="fa-solid fa-info"></i></a>
        </td>
        <td v-if="isAdmin">
          <a :href="'/personne/delete/' + personne.id"><i class="fa-solid fa-delete-left"></i></a>
        </td>
        <td v-if="isAdmin">
          <a :href="'/personne/edit/' + personne.id"><i class="fa-solid fa-pen-to-square"></i></a>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const personnes = ref([]);
const isAdmin = ref(false);

const getData = async () => {
  try {
    const response = await axios.get('https://127.0.0.1:8000/api/personnes');
    personnes.value = response.data;
  } catch (error) {
    console.error('Erreur lors de la récupération des données:', error);
  }
};

// Simule l'état de connexion d'un admin
const checkAdmin = async () => {
  try {
    const response = await axios.get('/api/check-role');
    isAdmin.value = response.data.isAdmin;
  } catch (error) {
    console.error('Erreur lors de la vérification du rôle:', error);
  }
};

const ajouterPersonne = () => {
  window.location.href = '/personne/edit/0';
};

onMounted(() => {
  getData();
  checkAdmin();
});
</script>

<style scoped>
.table {
  width: 100%;
  border-collapse: collapse;
}

.table th, .table td {
  padding: 8px;
  border: 1px solid #ddd;
}

.btn {
  margin-bottom: 10px;
}
</style>
