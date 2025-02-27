<template>
  <div>
    <button @click="showForm = !showForm">Add Dynamic Field</button>

    <div v-if="showForm">
      <form @submit.prevent="submitForm">
        <div>
          <label for="fieldName">Field Name:</label>
          <input type="text" v-model="fieldName" required />
        </div>

        <div>
          <label for="fieldType">Field Type:</label>
          <select v-model="fieldType" required>
            <option value="text">Text</option>
            <option value="number">Number</option>
            <option value="date">Date</option>
          </select>
        </div>

        <div>
          <label for="fieldValue">Field Value:</label>
          <input type="text" v-model="fieldValue" required />
        </div>

        <div>
          <button type="submit" :disabled="isSubmitting">Submit</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';

export default {
  setup() {
    const route = useRoute();
    const showForm = ref(false);
    const fieldName = ref('');
    const fieldType = ref('text');
    const fieldValue = ref('');
    const isSubmitting = ref(false);
    const userId = route.params.id;

    const submitForm = async () => {
      if (!fieldName.value || !fieldType.value || !fieldValue.value) {
        alert('Please fill in all fields.');
        return;
      }
      console.log('User ID being sent:', userId);

      const formData = {
        id: userId, // Add the user ID inside the form data
        fieldName: fieldName.value,
        fieldType: fieldType.value,
        fieldValue: fieldValue.value
      };
      console.log('Form Data:', formData);


      isSubmitting.value = true;

      try {
        const response = await axios.post(
            `https://127.0.0.1:8000/api/metadata`, // No more ID in the URL
            formData,
            {
              headers: {
                'Content-Type': 'application/json',
              },
            }
        );

        if (response.data.status === 'success') {
          alert('User metadata added successfully');
        } else {
          alert('Failed to add user metadata');
        }

        showForm.value = false;
        resetForm();
      } catch (error) {
        console.error('Error adding field:', error);
        alert('There was an error adding the metadata. Please try again.');
      } finally {
        isSubmitting.value = false;
      }
    };

    const resetForm = () => {
      fieldName.value = '';
      fieldType.value = 'text';
      fieldValue.value = '';
    };

    return {
      showForm,
      fieldName,
      fieldType,
      fieldValue,
      isSubmitting,
      submitForm,
    };
  },
};
</script>

<style scoped>
form {
  margin-top: 10px;
}
</style>
